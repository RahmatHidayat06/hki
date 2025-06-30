<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\PengajuanHki;
use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Str;

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
            
            // Multi-page: proses seluruh halaman, aplikasi overlay pada halaman terkait
            $pageCount = $pdf->setSourceFile($fullOriginalPath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplId = $pdf->importPage($pageNo);
                $size   = $pdf->getTemplateSize($tplId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId);

                foreach ($overlays as $overlay) {
                    $overlayPage = isset($overlay['page']) ? intval($overlay['page']) : 1;
                    if ($overlayPage !== $pageNo) continue;

                    // Ekstrak path relatif di dalam disk public dari URL
                    $rawPath = parse_url($overlay['url'], PHP_URL_PATH) ?: '';
                    // Hilangkan prefix "storage/" atau "/storage/" jika ada
                    $imagePath = preg_replace('#^/?storage/#', '', ltrim($rawPath, '/'));
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
            }

            // 3. Buat nama file baru bertanda tangan
            $timestamp    = now()->format('Ymd_His');
            $pengajuName  = str_replace(' ', '_', $pengajuan->user->name);
            $newFileName  = $pengajuan->id . '_' . $documentType . '_' . $pengajuName . '_' . $timestamp . '.pdf';
            $newFilePath  = 'signed_documents/' . $newFileName;
            
            Storage::disk('public')->makeDirectory('signed_documents');

            // Hapus file signed lama (sebelum menyimpan yang baru)
            $allSigned = Storage::disk('public')->files('signed_documents');
            foreach ($allSigned as $old) {
                if (Str::startsWith($old, $pengajuan->id . '_' . $documentType . '_')) {
                    Storage::disk('public')->delete($old);
                }
            }

            $fullNewPath = storage_path('app/public/' . $newFilePath);

            $pdf->Output($fullNewPath, 'F');
            
            // 4. Simpan path versi signed pada key 'signed' (jangan timpa file asli)
            if (!isset($dokumenJson['signed'])) {
                $dokumenJson['signed'] = [];
            }
            $dokumenJson['signed'][$documentType] = $newFilePath;
            // Gantikan path dokumen asli agar tampilan detail memuat versi bertanda tangan
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
