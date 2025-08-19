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

        // Simpan sebagai file PNG dengan format konsisten
        $imageName = 'ttd_pengajuan_' . $id . '_' . time() . '.png';
        $filename = "ttd/{$imageName}";
        Storage::disk('public')->put($filename, $imageData);

        // Simpan path tanda tangan ke pengajuan (kolom konsisten: ttd_path)
        $pengajuan->ttd_path = 'storage/ttd/' . $imageName;
        $pengajuan->save();

        // Update dokumen pendukung: tambah overlay untuk form_permohonan_pendaftaran
        $dokumen = is_string($pengajuan->file_dokumen_pendukung)
            ? json_decode($pengajuan->file_dokumen_pendukung, true)
            : ($pengajuan->file_dokumen_pendukung ?? []);

        if (!isset($dokumen['overlays'])) {
            $dokumen['overlays'] = [];
        }
        
        // HAPUS semua overlay lama untuk form_permohonan_pendaftaran untuk mencegah duplikasi
        unset($dokumen['overlays']['form_permohonan_pendaftaran']);
        
        // Buat overlay baru dengan posisi yang tepat
        $dokumen['overlays']['form_permohonan_pendaftaran'] = [
            [
                'url' => Storage::url($filename),
                // Posisi sesuai template form permohonan (bottom-right, area signature-box)
                'page' => 1,
                'x_percent' => 70,  // Lebih ke kanan untuk area signature-box
                'y_percent' => 80,  // Lebih ke bawah untuk area signature yang tepat  
                'width_percent' => 20,
                'height_percent' => 8,
                'anchor' => 'center'
            ]
        ];

        // Pastikan dokumen dasar form tersedia, jika belum ada/hilang -> generate
        $needGenerateForm = true;
        $baseFormPath = $dokumen['form_permohonan_pendaftaran'] ?? null;
        if ($baseFormPath) {
            $normalized = ltrim($baseFormPath, '/');
            if (str_starts_with($normalized, 'storage/')) {
                $normalized = substr($normalized, strlen('storage/'));
            }
            if (Storage::disk('public')->exists($normalized)) {
                $needGenerateForm = false;
            }
        }
        if ($needGenerateForm) {
            try {
                $suratController = new \App\Http\Controllers\SuratController();
                $generatedFormPath = $suratController->autoGenerateFormPermohonan($pengajuan);
                if ($generatedFormPath) {
                    $dokumen['form_permohonan_pendaftaran'] = $generatedFormPath;
                }
            } catch (\Exception $e) {
                \Log::error('Gagal auto-generate Form Permohonan: ' . $e->getMessage());
            }
        }

        // Simpan update dokumen pendukung
        $pengajuan->file_dokumen_pendukung = json_encode($dokumen);
        $pengajuan->save();

        // Jalankan signing PDF agar file signed langsung tersedia
        try {
            $pdfSigner = new \App\Http\Controllers\PdfSigningController();
            $pdfSigner->signPdf($pengajuan, 'form_permohonan_pendaftaran');
        } catch (\Exception $e) {
            \Log::error('Gagal auto-sign Form Permohonan: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Signature saved',
            'redirect' => route('pengajuan.show', $id)
        ]);
    }
}
