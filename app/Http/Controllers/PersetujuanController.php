<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use App\Models\PersetujuanDirektur;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PersetujuanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:direktur');
    }

    public function index(): View
    {
        $pengajuan = PengajuanHki::where('status', 'divalidasi')->paginate(10);
        return view('persetujuan.index', compact('pengajuan'));
    }

    public function show(PengajuanHki $pengajuan): View
    {
        return view('persetujuan.show', compact('pengajuan'));
    }

    public function approve(PengajuanHki $pengajuan): RedirectResponse
    {
        $pengajuan->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id()
        ]);

        // Buat notifikasi untuk pengaju
        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Pengajuan HKI Disetujui',
            'pesan' => 'Pengajuan HKI Anda dengan judul "' . $pengajuan->judul . '" telah disetujui.',
            'status' => 'unread',
            'dibaca' => false
        ]);

        return Redirect::to(route('persetujuan.index'))
            ->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function reject(Request $request, PengajuanHki $pengajuan): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $pengajuan->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_at' => now(),
            'approved_by' => Auth::id()
        ]);

        // Buat notifikasi untuk pengaju
        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Pengajuan HKI Ditolak',
            'pesan' => 'Pengajuan HKI Anda dengan judul "' . $pengajuan->judul . '" telah ditolak. ' .
                      'Alasan: ' . $validated['rejection_reason'],
            'status' => 'unread',
            'dibaca' => false
        ]);

        return Redirect::to(route('persetujuan.index'))
            ->with('success', 'Pengajuan berhasil ditolak.');
    }

    public function store(Request $request, PengajuanHki $pengajuan): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_persetujuan' => 'required|string'
        ]);

        $pengajuan->update([
            'status' => $request->status,
            'catatan_persetujuan' => $request->catatan_persetujuan
        ]);

        // Buat notifikasi untuk pengaju
        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Update Status Pengajuan HKI',
            'pesan' => 'Pengajuan HKI Anda dengan judul "' . $pengajuan->judul . '" telah ' . 
                      ($request->status === 'disetujui' ? 'disetujui' : 'ditolak') . '. ' .
                      'Catatan: ' . $request->catatan_persetujuan,
            'status' => 'unread',
            'dibaca' => false
        ]);

        return Redirect::to(route('persetujuan.index'))
            ->with('success', 'Pengajuan berhasil diproses');
    }
}