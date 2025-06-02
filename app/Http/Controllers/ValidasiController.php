<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Redirect;
use App\Models\Notifikasi;

class ValidasiController extends Controller
{
    public function index(): View
    {
        $pengajuan = PengajuanHki::where('status', 'menunggu_validasi')->paginate(10);
        return view('validasi.index', compact('pengajuan'));
    }

    public function show(PengajuanHki $pengajuan): View
    {
        return view('validasi.show', compact('pengajuan'));
    }

    public function validasi(Request $request, PengajuanHki $pengajuan): RedirectResponse
    {
        $request->validate([
            'status_validasi' => 'required|in:disetujui,ditolak',
            'catatan_validasi' => 'required_if:status_validasi,ditolak|nullable|string'
        ]);

        $status = $request->status_validasi === 'disetujui' ? 'divalidasi' : 'ditolak';
        $catatan = $request->catatan_validasi;

        $pengajuan->update([
            'status' => $status,
            'catatan_validasi' => $catatan
        ]);

        // Buat notifikasi untuk pengaju
        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Update Status Validasi HKI',
            'pesan' => 'Pengajuan HKI Anda dengan judul "' . ($pengajuan->judul_karya ?? $pengajuan->judul) . '" telah ' .
                      ($status === 'divalidasi' ? 'divalidasi' : 'ditolak') . '. ' .
                      'Catatan: ' . $catatan,
            'status' => 'unread',
            'dibaca' => false
        ]);

        return Redirect::to(route('validasi.index'))
            ->with('success', 'Status pengajuan berhasil diperbarui');
    }
}