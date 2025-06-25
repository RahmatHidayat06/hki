<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\PengajuanHki;
use setasign\Fpdi\Tcpdf\Fpdi;

class PdfSigningController extends Controller
{
    /**
     * Menempelkan overlay (gambar) ke file PDF yang ada.
     *
     * @param PengajuanHki $pengajuan
     * @param string $documentType 'surat_pernyataan' atau 'surat_pengalihan'
     * @return string|null Path ke file PDF yang baru, atau null jika gagal.
     */
    public function signPdf(PengajuanHki $pengajuan, string $documentType): ?string
    {
        // 1. Dapatkan Path Dokumen Asli dan Data Overlay
        $dokumenJson = is_string($pengajuan->file_dokumen_pendukung)
            ? json_decode($pengajuan->file_dokumen_pendukung, true)
            : ($pengajuan->file_dokumen_pendukung ?? []);

        $originalDocPath = $dokumenJson[$documentType] ?? null;
        $overlays = $dokumenJson['overlays'][$documentType] ?? [];

        if (!$originalDocPath || !Storage::disk('public')->exists($originalDocPath) || empty($overlays)) {
            Log::error("PDF Signing: Dokumen asli atau data overlay tidak ditemukan untuk pengajuan #{$pengajuan->id}");
            return null;
        }

        $fullOriginalPath = storage_path('app/public/' . ltrim($originalDocPath, '/'));

        try {
            $pdf = new Fpdi();
            
            // Ambil hanya halaman pertama (logika awal)
            $pageCount = $pdf->setSourceFile($fullOriginalPath);
            $tplId = $pdf->importPage(1);
            $size   = $pdf->getTemplateSize($tplId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tplId);

            // Tempelkan semua overlay (diasumsikan memang untuk halaman 1)
            foreach ($overlays as $overlay) {
                $imagePath = ltrim(parse_url($overlay['url'], PHP_URL_PATH), '/storage');
                if (!Storage::disk('public')->exists($imagePath)) {
                    Log::warning("PDF Signing: File gambar overlay tidak ditemukan: {$imagePath}");
                    continue;
                }

                $fullImagePath = storage_path('app/public/' . $imagePath);

                $x = ($overlay['x_percent'] / 100) * $size['width'];
                $y = ($overlay['y_percent'] / 100) * $size['height'];
                $w = ($overlay['width_percent'] / 100) * $size['width'];
                $h = 0;

                $pdf->SetAlpha(1);
                $pdf->Image($fullImagePath, $x, $y, $w, $h, 'PNG', '', '', false, 300, '', false, false, 0);
            }

            // 3. Simpan PDF Baru dengan nama yang deskriptif dan mudah diidentifikasi
            $timestamp = now()->format('Ymd_His');
            $pengajuName = str_replace(' ', '_', $pengajuan->user->name);
            $newFileName = $pengajuan->id . '_' . $documentType . '_' . $pengajuName . '_' . $timestamp . '.pdf';
            $newFilePath = 'signed_documents/' . $newFileName;
            
            Storage::disk('public')->makeDirectory('signed_documents');
            $fullNewPath = storage_path('app/public/'.$newFilePath);

            $pdf->Output($fullNewPath, 'F');
            
            // 4. Simpan path versi signed pada key 'signed' dan ganti dokumen asli
            if (!isset($dokumenJson['signed'])) {
                $dokumenJson['signed'] = [];
            }
            $dokumenJson['signed'][$documentType] = $newFilePath;
            // Perbarui juga dokumen asli agar halaman detail langsung menggunakan PDF bertanda tangan
            $dokumenJson[$documentType] = $newFilePath;
            $pengajuan->file_dokumen_pendukung = json_encode($dokumenJson);
            $pengajuan->save();

            Log::info("PDF Signing berhasil untuk pengajuan #{$pengajuan->id}, file: {$newFileName}");
            return $newFilePath;

        } catch (\Exception $e) {
            Log::error("PDF Signing failed for pengajuan #{$pengajuan->id}: " . $e->getMessage());
            return null;
        }
    }
}
