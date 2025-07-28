<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanHki;
use Illuminate\Support\Facades\Storage;

class FormPermohonanPendaftaranController extends Controller
{
    public function show($id)
    {
        $pengajuan = PengajuanHki::with(['user'])->findOrFail($id);

        // Ambil data tanda tangan
        $fileName = 'ttd_' . $pengajuan->id . '.png'; // Sesuaikan jika file-nya statis, misal: 'ttd.png'
        $filePath = 'public/signatures/' . $fileName;

        $ttdPath = null;
        if (Storage::exists($filePath)) {
            $ttdPath = Storage::url('signatures/' . $fileName); // URL publik
        }

        return view('surat.form_permohonan_pendaftaran', compact('pengajuan', 'ttdPath'));
    }
}
