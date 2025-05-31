<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PengajuanHkiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $pengajuan = PengajuanHki::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('pengajuan.index', compact('pengajuan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('pengajuan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'nama_pengusul' => 'required|string|max:255',
            'nip_nidn' => ['nullable', 'string', 'max:255', 'required_if:role,dosen'],
            'no_hp' => 'required|string|max:255',
            'id_sinta' => 'nullable|string|max:255',
            'jumlah_pencipta' => 'required|string|in:1 orang,2 orang,3 orang,4 orang,5 orang',
            'identitas_ciptaan' => 'required|string|in:karya tulis,karya audio visual,karya lainnya',
            'sub_jenis_ciptaan' => 'required|string|in:Buku,E-Book,Diktat,Modul,Buku Panduan/Petunjuk,Karya Ilmiah,Karya Tulis/Artikel,Laporan Penelitian,Jurnal,Kuliah,Karya Rekaman Video,Karya Siaran Video,Program Komputer,Permainan Video,Basis Data',
            'tanggal_pertama_kali_diumumkan' => 'required|date',
            'contoh_ciptaan' => 'required|file|mimes:pdf,mp4,mp3,jpg,jpeg,png,gif,svg,webp,doc,docx|max:10240',
            'surat_pengalihan_hak_cipta' => 'required|file|mimes:pdf|max:10240',
            'surat_pernyataan_hak_cipta' => 'required|file|mimes:pdf|max:10240',
            'ktp_seluruh_pencipta' => 'required|file|mimes:pdf|max:10240',
            'role' => 'required|in:dosen,mahasiswa',
            'pencipta' => 'required|array',
            'pencipta.*.nama' => 'required|string|max:255',
            'pencipta.*.email' => 'required|email|max:255',
            'pencipta.*.no_hp' => 'nullable|string|max:255',
            'pencipta.*.alamat' => 'nullable|string',
        ]);

        $pathContohCiptaan = $request->file('contoh_ciptaan')->store('dokumen_ciptaan');
        $pathSuratPengalihan = $request->file('surat_pengalihan_hak_cipta')->store('dokumen_pengalihan');
        $pathSuratPernyataan = $request->file('surat_pernyataan_hak_cipta')->store('dokumen_pernyataan');
        $pathKtp = $request->file('ktp_seluruh_pencipta')->store('dokumen_ktp');

        $tahun = date('Y');
        $bulan = date('m');
        $lastPengajuan = PengajuanHki::whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->orderBy('id', 'desc')
            ->first();
        
        $nomorUrut = $lastPengajuan ? intval(substr($lastPengajuan->nomor_pengajuan, -4)) + 1 : 1;
        $nomorPengajuan = sprintf('HKI/%s/%s/%04d', $tahun, $bulan, $nomorUrut);

        $pengajuan = PengajuanHki::create([
            'user_id' => Auth::id(),
            'judul_karya' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'],
            'nama_pengusul' => $validated['nama_pengusul'],
            'nip_nidn' => $validated['nip_nidn'],
            'no_hp' => $validated['no_hp'],
            'id_sinta' => $validated['id_sinta'],
            'jumlah_pencipta' => $validated['jumlah_pencipta'],
            'identitas_ciptaan' => $validated['identitas_ciptaan'],
            'sub_jenis_ciptaan' => $validated['sub_jenis_ciptaan'],
            'tanggal_pertama_kali_diumumkan' => $validated['tanggal_pertama_kali_diumumkan'],
            'status' => 'menunggu_validasi',
            'tanggal_pengajuan' => now(),
            'nomor_pengajuan' => $nomorPengajuan,
            'role' => $validated['role'],
            'file_karya' => $pathContohCiptaan,
            'file_dokumen_pendukung' => json_encode([
                'surat_pengalihan' => $pathSuratPengalihan,
                'surat_pernyataan' => $pathSuratPernyataan,
                'ktp' => $pathKtp
            ]),
        ]);

        // Simpan data pencipta dinamis
        if (isset($validated['pencipta'])) {
            foreach ($validated['pencipta'] as $dataPencipta) {
                $pengajuan->pengaju()->create([
                    'nama' => $dataPencipta['nama'],
                    'email' => $dataPencipta['email'],
                    'no_hp' => $dataPencipta['no_hp'] ?? null,
                    'alamat' => $dataPencipta['alamat'] ?? null,
                    'kecamatan' => $dataPencipta['kecamatan'] ?? null,
                    'kodepos' => $dataPencipta['kodepos'] ?? null,
                ]);
            }
        }

        Notifikasi::create([
            'user_id' => Auth::id(),
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Pengajuan HKI Baru',
            'pesan' => 'Pengajuan HKI Anda dengan nomor "' . $nomorPengajuan . '" telah berhasil dibuat dan sedang menunggu validasi.',
            'status' => 'unread',
            'dibaca' => false
        ]);

        return Redirect::to(route('dashboard'))
            ->with('success', 'Pengajuan berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(PengajuanHki $pengajuan): View|RedirectResponse
    {
        if ($pengajuan->user_id !== Auth::id()) {
            return Redirect::to(route('pengajuan.index'))
                ->with('error', 'Anda tidak memiliki akses ke pengajuan ini');
        }

        return view('pengajuan.show', compact('pengajuan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PengajuanHki $pengajuan): View|RedirectResponse
    {
        if ($pengajuan->user_id !== Auth::id()) {
            return Redirect::to(route('pengajuan.index'))
                ->with('error', 'Anda tidak memiliki akses ke pengajuan ini');
        }

        if ($pengajuan->status !== 'menunggu_validasi') {
            return Redirect::to(route('pengajuan.index'))
                ->with('error', 'Pengajuan tidak dapat diubah karena sudah diproses');
        }

        return view('pengajuan.edit', compact('pengajuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PengajuanHki $pengajuan): RedirectResponse
    {
        if ($pengajuan->user_id !== Auth::id()) {
            return Redirect::to(route('pengajuan.index'))
                ->with('error', 'Anda tidak memiliki akses ke pengajuan ini');
        }

        if ($pengajuan->status !== 'menunggu_validasi') {
            return Redirect::to(route('pengajuan.index'))
                ->with('error', 'Pengajuan tidak dapat diubah karena sudah diproses');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file_pengajuan' => 'nullable|file|mimes:pdf,doc,docx|max:10240'
        ]);

        if ($request->hasFile('file_pengajuan')) {
            Storage::delete($pengajuan->file_karya);
            
            $file = $request->file('file_pengajuan');
            $path = $file->store('pengajuan');
            
            $pengajuan->file_karya = $path;
        }

        $pengajuan->update([
            'judul_karya' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi']
        ]);

        return Redirect::to(route('pengajuan.index'))
            ->with('success', 'Pengajuan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PengajuanHki $pengajuan): RedirectResponse
    {
        if ($pengajuan->user_id !== Auth::id()) {
            return Redirect::to(route('pengajuan.index'))
                ->with('error', 'Anda tidak memiliki akses ke pengajuan ini');
        }

        if ($pengajuan->status !== 'menunggu_validasi') {
            return Redirect::to(route('pengajuan.index'))
                ->with('error', 'Pengajuan tidak dapat dihapus karena sudah diproses');
        }

        Storage::delete($pengajuan->file_karya);
        $pengajuan->delete();

        return Redirect::to(route('pengajuan.index'))
            ->with('success', 'Pengajuan berhasil dihapus');
    }
} 