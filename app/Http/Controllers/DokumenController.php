<?php

namespace App\Http\Controllers;

use App\Models\DokumenHki;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function store(Request $request, $pengajuanId)
    {
        $request->validate([
            'jenis_dokumen' => 'required',
            'file' => 'required|file|max:10240' // max 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('dokumen-hki');

        DokumenHki::create([
            'pengajuan_hki_id' => $pengajuanId,
            'jenis_dokumen' => $request->jenis_dokumen,
            'nama_file' => $file->getClientOriginalName(),
            'path_file' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize()
        ]);

        return back()->with('success', 'Dokumen berhasil diupload');
    }
}