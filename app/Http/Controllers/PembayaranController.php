<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\PengajuanHki;
use App\Models\User;
use App\Models\Notifikasi;

class PembayaranController extends Controller
{
    public function form(PengajuanHki $pengajuan)
    {
        // Hanya pemilik pengajuan yang dapat mengakses & status harus menunggu_pembayaran
        if(Auth::id() !== $pengajuan->user_id || $pengajuan->status !== 'menunggu_pembayaran'){
            abort(403);
        }
        return view('pembayaran.form', compact('pengajuan'));
    }

    public function submit(Request $request, PengajuanHki $pengajuan)
    {
        if(Auth::id() !== $pengajuan->user_id || $pengajuan->status !== 'menunggu_pembayaran'){
            abort(403);
        }

        $request->validate([
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $path = $request->file('bukti')->store('bukti_pembayaran', 'public');

        $pengajuan->update([
            'bukti_pembayaran' => $path,
            'status' => 'menunggu_verifikasi_pembayaran'
        ]);

        // Kirim notifikasi ke semua admin bahwa bukti pembayaran telah diunggah
        $adminUsers = User::where('role', 'admin')->get();
        foreach ($adminUsers as $admin) {
            Notifikasi::create([
                'user_id' => $admin->id,
                'pengajuan_hki_id' => $pengajuan->id,
                'judul' => 'Verifikasi Pembayaran Diperlukan',
                'pesan' => 'Pengajuan HKI dengan judul "' . ($pengajuan->judul_karya ?? $pengajuan->judul) . '" telah mengunggah bukti pembayaran dan menunggu verifikasi Anda.',
                'status' => 'unread',
                'dibaca' => false
            ]);
        }

        Log::info("Bukti pembayaran diupload untuk pengajuan #{$pengajuan->id} dan notifikasi dikirim ke admin.");

        return Redirect::route('pengajuan.show', $pengajuan->id)
            ->with('success', 'Bukti pembayaran berhasil diunggah, menunggu verifikasi admin.');
    }

    /**
     * Daftar pengajuan milik user yang butuh / telah mengirim pembayaran.
     */
    public function index()
    {
        // Menampilkan riwayat pembayaran lengkap (termasuk selesai) agar user mudah mengunduh sertifikat
        $pengajuans = PengajuanHki::where('user_id', Auth::id())
            ->whereIn('status', [
                'menunggu_pembayaran', 
                'menunggu_verifikasi_pembayaran',
                'disetujui',
                'selesai'
            ])
            ->latest()
            ->get();

        return view('pembayaran.index', compact('pengajuans'));
    }

    // Halaman transaksi pembayaran sebelum upload bukti
    public function pay(PengajuanHki $pengajuan)
    {
        if(Auth::id() !== $pengajuan->user_id || $pengajuan->status !== 'menunggu_pembayaran'){
            abort(403);
        }

        if(!$pengajuan->billing_code){
            return view('pembayaran.waiting', compact('pengajuan'));
        }

        return view('pembayaran.pay', compact('pengajuan'));
    }

    /**
     * Download sertifikat jika sudah tersedia.
     */
    public function downloadCertificate(PengajuanHki $pengajuan)
    {
        if(Auth::id() !== $pengajuan->user_id && Auth::user()->role !== 'admin'){
            abort(403);
        }

        $filePath = 'sertifikat/'.$pengajuan->id.'.pdf';
        if(!Storage::disk('public')->exists($filePath)){
            return Redirect::back()->with('error', 'Sertifikat belum tersedia.');
        }

        return response()->download(storage_path('app/public/'.$filePath), 'sertifikat_hki_'.$pengajuan->id.'.pdf');
    }

    /**
     * Serve payment proof file
     */
    public function serveBuktiPembayaran($id)
    {
        $pengajuan = PengajuanHki::findOrFail($id);
        
        // Check if user can access this payment proof
        if(Auth::id() !== $pengajuan->user_id && Auth::user()->role !== 'admin'){
            abort(403, 'Akses tidak diizinkan');
        }

        if(!$pengajuan->bukti_pembayaran){
            abort(404, 'Bukti pembayaran tidak ditemukan');
        }

        $filePath = storage_path('app/public/' . $pengajuan->bukti_pembayaran);
        
        if(!file_exists($filePath)){
            abort(404, 'File bukti pembayaran tidak ditemukan');
        }

        // Get file info
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
        ];
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
        ]);
    }

    /**
     * Upload sertifikat by admin
     */
    public function uploadSertifikat(Request $request, PengajuanHki $pengajuan)
    {
        // Only admin can upload sertifikat
        if(Auth::user()->role !== 'admin'){
            abort(403, 'Akses tidak diizinkan');
        }

        // Validate that payment has been verified
        if($pengajuan->status !== 'disetujui'){
            return redirect()->back()->with('error', 'Pengajuan harus disetujui terlebih dahulu sebelum upload sertifikat');
        }

        $request->validate([
            'sertifikat' => 'required|file|mimes:pdf|max:5120', // 5MB max
        ], [
            'sertifikat.required' => 'File sertifikat harus diupload',
            'sertifikat.mimes' => 'Sertifikat harus berformat PDF',
            'sertifikat.max' => 'Ukuran file maksimal 5MB'
        ]);

        // Store sertifikat file
        $path = $request->file('sertifikat')->store('sertifikat', 'public');

        // Update pengajuan with sertifikat and change status to selesai
        $pengajuan->update([
            'sertifikat' => $path,
            'status' => 'selesai'
        ]);

        // Create notification for user
        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Sertifikat HKI Sudah Tersedia',
            'pesan' => 'Sertifikat HKI untuk pengajuan "' . ($pengajuan->judul_karya ?? $pengajuan->judul) . '" sudah tersedia dan dapat diunduh.',
            'status' => 'unread',
            'dibaca' => false
        ]);

        Log::info("Sertifikat uploaded for pengajuan #{$pengajuan->id} and status changed to selesai.");

        return redirect()->back()->with('success', 'Sertifikat berhasil diupload dan status pengajuan berubah menjadi selesai');
    }

    /**
     * Serve sertifikat file
     */
    public function serveSertifikat($id)
    {
        $pengajuan = PengajuanHki::findOrFail($id);
        
        // Check if user can access this sertifikat
        if(Auth::id() !== $pengajuan->user_id && Auth::user()->role !== 'admin'){
            abort(403, 'Akses tidak diizinkan');
        }

        if(!$pengajuan->sertifikat){
            abort(404, 'Sertifikat tidak ditemukan');
        }

        $filePath = storage_path('app/public/' . $pengajuan->sertifikat);
        
        if(!file_exists($filePath)){
            abort(404, 'File sertifikat tidak ditemukan');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
        ]);
    }
} 