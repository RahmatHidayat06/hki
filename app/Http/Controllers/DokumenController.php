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
        $path = $file->store('dokumen-hki', 'public');

        // Update document status to indicate it needs a signature or stamp
        $dokumen = DokumenHki::create([
            'pengajuan_hki_id' => $pengajuanId,
            'jenis_dokumen' => $request->jenis_dokumen,
            'nama_file' => $file->getClientOriginalName(),
            'path_file' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'status' => 'menunggu_ttd_materai' // New status indicating it needs signature and stamp
        ]);

        // Check if the document type requires a signature or stamp
        if ($request->jenis_dokumen === 'surat_pernyataan') {
            // Logic to ensure the document has a stamp and signature
            // This could involve checking the file content or metadata
        } else if ($request->jenis_dokumen === 'surat_pengalihan') {
            // Logic to ensure the document has a signature
        }

        return back()->with('success', 'Dokumen berhasil diupload');
    }
}