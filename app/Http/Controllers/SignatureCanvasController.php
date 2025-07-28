<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\PengajuanHki;

class SignatureCanvasController extends Controller
{
    public function show($id)
    {
        $pengajuan = PengajuanHki::findOrFail($id);
        return view('signature.signature_canvas', compact('pengajuan'));
    }

    public function save(Request $request, $id)
    {
        $request->validate([
            'signature' => 'required|string',
        ]);

        $pengajuan = PengajuanHki::findOrFail($id);
        $base64Image = $request->input('signature');

        // Pecah prefix dan datanya
        [$prefix, $data] = explode(',', $base64Image);
        $imageData = base64_decode($data);

        // Simpan sebagai file PNG
        $filename = "signatures/{$id}.png";
        Storage::disk('public')->put($filename, $imageData);

        // Simpan path jika ingin disimpan di database
        $pengajuan->path_ttd = $filename;
        $pengajuan->save();

        return response()->json([
            'message' => 'Signature saved',
            'redirect' => route('form_permohonan_pendaftaran', $id) // ganti dengan route tampilan PDF
        ]);
    }
}
