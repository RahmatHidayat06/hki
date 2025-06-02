<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PengajuanHkiExport;

class AdminController extends Controller
{
    // Dashboard admin: tampilkan semua pengajuan
    public function dashboard()
    {
        $pengajuan = PengajuanHki::orderBy('created_at', 'desc')->paginate(15);
        $totalLengkap = PengajuanHki::whereNotNull('judul_karya')
            ->whereNotNull('kategori')
            ->whereNotNull('deskripsi')
            ->whereNotNull('file_karya')
            ->whereNotNull('file_dokumen_pendukung')
            ->count();
        $total = PengajuanHki::count();
        return view('admin.dashboard', compact('pengajuan', 'total', 'totalLengkap'));
    }

    // Rekap data: export ke Excel jika semua data lengkap
    public function rekap()
    {
        $total = PengajuanHki::count();
        $totalLengkap = PengajuanHki::whereNotNull('judul_karya')
            ->whereNotNull('kategori')
            ->whereNotNull('deskripsi')
            ->whereNotNull('file_karya')
            ->whereNotNull('file_dokumen_pendukung')
            ->count();
        if ($total === 0 || $total !== $totalLengkap) {
            return Redirect::back()->with('error', 'Tidak semua data lengkap. Rekap hanya bisa dilakukan jika semua data sudah lengkap.');
        }
        return Excel::download(new PengajuanHkiExport, 'rekap_pengajuan_hki.xlsx');
    }

    // Daftar Pengajuan untuk admin
    public function pengajuan()
    {
        $pengajuan = PengajuanHki::orderBy('created_at', 'desc')->paginate(15);
        $total = PengajuanHki::count();
        $totalLengkap = PengajuanHki::whereNotNull('judul_karya')
            ->whereNotNull('kategori')
            ->whereNotNull('deskripsi')
            ->whereNotNull('file_karya')
            ->whereNotNull('file_dokumen_pendukung')
            ->count();
        return view('admin.pengajuan', compact('pengajuan', 'total', 'totalLengkap'));
    }

    // Detail pengajuan untuk admin
    public function show($id)
    {
        $pengajuan = \App\Models\PengajuanHki::findOrFail($id);
        return view('admin.show', compact('pengajuan'));
    }
} 