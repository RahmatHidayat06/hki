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

		// Normalisasi path dokumen asli
		$normalizedOriginal = null;
		if ($originalDocPath) {
			$normalizedOriginal = ltrim($originalDocPath, '/');
			if (str_starts_with($normalizedOriginal, 'storage/')) {
				$normalizedOriginal = substr($normalizedOriginal, strlen('storage/'));
			}
			// Jika berupa URL storage, konversi ke relative
			if (!\Storage::disk('public')->exists($normalizedOriginal)) {
				$rawPath = parse_url($originalDocPath, PHP_URL_PATH) ?: '';
				$maybeRel = preg_replace('#^/?storage/#', '', ltrim($rawPath, '/'));
				if ($maybeRel && \Storage::disk('public')->exists($maybeRel)) {
					$normalizedOriginal = $maybeRel;
				}
			}
		}

		// Fallback: jika overlays kosong dan tipe dokumen adalah form_permohonan_pendaftaran,
		// coba buat overlay default dari ttd_path pengajuan agar proses bisa lanjut
		if (empty($overlays) && $documentType === 'form_permohonan_pendaftaran') {
			// Cari ttd_path dari pengajuan atau user
			$ttdPathCandidate = null;
			if (!empty($pengajuan->ttd_path)) {
				$ttdPathCandidate = $pengajuan->ttd_path; // bisa berupa 'storage/...' atau relative
			} elseif (!empty($pengajuan->user) && !empty($pengajuan->user->ttd_path)) {
				$ttdPathCandidate = $pengajuan->user->ttd_path;
			}
			if ($ttdPathCandidate) {
				$normalizedTtd = ltrim($ttdPathCandidate, '/');
				if (str_starts_with($normalizedTtd, 'storage/')) {
					$normalizedTtd = substr($normalizedTtd, strlen('storage/'));
				}
				// Jika berupa URL storage, konversi ke relative
				if (!\Storage::disk('public')->exists($normalizedTtd)) {
					$rawPath = parse_url($ttdPathCandidate, PHP_URL_PATH) ?: '';
					$maybeRel = preg_replace('#^/?storage/#', '', ltrim($rawPath, '/'));
					if ($maybeRel && \Storage::disk('public')->exists($maybeRel)) {
						$normalizedTtd = $maybeRel;
					}
				}
				if (\Storage::disk('public')->exists($normalizedTtd)) {
					$ttdUrl = \Storage::url($normalizedTtd);
					$overlays = [[
						'url' => $ttdUrl,
						'page' => 1,
						'x_percent' => 60,
						'y_percent' => 80,
						'width_percent' => 30,
					]];
					$dokumenJson['overlays'][$documentType] = $overlays;
					$pengajuan->file_dokumen_pendukung = json_encode($dokumenJson);
					$pengajuan->save();
				} else {
					\Log::warning("PDF Signing: ttd_path tidak ditemukan di storage untuk pengajuan #{$pengajuan->id}", [
						'ttd_raw' => $ttdPathCandidate,
						'ttd_normalized' => $normalizedTtd
					]);
				}
			} else {
				\Log::warning("PDF Signing: Tidak ada ttd_path untuk overlay default pada pengajuan #{$pengajuan->id}");
			}
		}

		// Fallback: auto-generate form jika tipe adalah form_permohonan_pendaftaran dan dokumen belum ada
		if ((!$normalizedOriginal || !\Storage::disk('public')->exists($normalizedOriginal)) && $documentType === 'form_permohonan_pendaftaran') {
			try {
				$suratController = new \App\Http\Controllers\SuratController();
				$generatedFormPath = $suratController->autoGenerateFormPermohonan($pengajuan);
				if ($generatedFormPath) {
					$dokumenJson[$documentType] = $generatedFormPath;
					$pengajuan->file_dokumen_pendukung = json_encode($dokumenJson);
					$pengajuan->save();
					$normalizedOriginal = ltrim($generatedFormPath, '/');
				}
			} catch (\Exception $e) {
				\Log::error("PDF Signing: Gagal auto-generate form untuk pengajuan #{$pengajuan->id}: " . $e->getMessage());
			}
		}

		if (!$normalizedOriginal || !\Storage::disk('public')->exists($normalizedOriginal) || empty($overlays)) {
			\Log::error("PDF Signing: Dokumen asli atau data overlay tidak ditemukan untuk pengajuan #{$pengajuan->id}", [
				'document_type' => $documentType,
				'originalDocPath' => $originalDocPath,
				'normalized' => $normalizedOriginal,
				'original_exists' => $normalizedOriginal ? \Storage::disk('public')->exists($normalizedOriginal) : false,
				'overlay_count' => is_array($overlays) ? count($overlays) : 0,
			]);
			return null;
		}

		$fullOriginalPath = storage_path('app/public/' . ltrim($normalizedOriginal, '/'));

		try {
			$pdf = new Fpdi();
			// Pastikan tidak ada header/footer, margin, dan auto page break yang menggeser koordinat
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
			$pdf->SetMargins(0, 0, 0);
			$pdf->SetHeaderMargin(0);
			$pdf->SetFooterMargin(0);
			$pdf->SetAutoPageBreak(false, 0);
			
			// Multi-page: proses seluruh halaman, aplikasi overlay pada halaman terkait
			$pageCount = $pdf->setSourceFile($fullOriginalPath);

			for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
				// Untuk Form Permohonan gunakan MediaBox (sesuai PDF.js). Dokumen lain gunakan CropBox.
				$useCropBox = in_array($documentType, ['surat_pengalihan', 'surat_pernyataan']);
				try {
					$tplId = $pdf->importPage($pageNo, $useCropBox ? '/CropBox' : '/MediaBox');
				} catch (\Throwable $e) {
					$tplId = $pdf->importPage($pageNo); // fallback
				}
				$size   = $pdf->getTemplateSize($tplId);

				$pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
				$pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);

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

					// Hitung ukuran gambar berdasarkan persen (support width_percent/height_percent)
					$declaredWidthPct  = isset($overlay['width_percent'])  ? (float)$overlay['width_percent']  : null;
					$declaredHeightPct = isset($overlay['height_percent']) ? (float)$overlay['height_percent'] : null;
					$imgSize = @getimagesize($fullImagePath);
					$imgRatio = ($imgSize && $imgSize[0] > 0) ? ($imgSize[1] / $imgSize[0]) : 0.35; // fallback ratio approx

					if ($declaredWidthPct !== null) {
						$w = ($declaredWidthPct / 100) * $size['width'];
						$h = $w * $imgRatio;
					} elseif ($declaredHeightPct !== null) {
						$h = ($declaredHeightPct / 100) * $size['height'];
						$w = $h / max($imgRatio, 0.001);
					} else {
						// default width 20% jika tidak dideklarasikan
						$w = 0.2 * $size['width'];
						$h = $w * $imgRatio;
					}

					// Koordinat dari editor disimpan sebagai TOP-LEFT (bukan center)
					$x = ($overlay['x_percent'] / 100) * $size['width'];
					$y = ($overlay['y_percent'] / 100) * $size['height'];

					// Terapkan offset kalibrasi untuk Form Permohonan (jika dikonfigurasi)
					if ($documentType === 'form_permohonan_pendaftaran') {
						$formCfg = config('pdf_signing.form_permohonan', []);
						$dxPct = isset($formCfg['x_offset_percent']) ? floatval($formCfg['x_offset_percent']) : 0.0;
						$dyPct = isset($formCfg['y_offset_percent']) ? floatval($formCfg['y_offset_percent']) : 0.0;
						if ($dxPct || $dyPct) {
							$x += ($dxPct/100.0) * $size['width'];
							$y += ($dyPct/100.0) * $size['height'];
						}
					}

					// Penyesuaian offset kecil untuk menyamakan pratinjau dan hasil final pada Form Permohonan
					// Tidak ada offset untuk Form Permohonan agar 1:1 dengan preview
					if ($documentType === 'surat_pengalihan' && empty($overlay['is_direktur'])) {
						$y += 0.03 * $size['height']; // turun ~3% untuk para pencipta
					}
					// Koreksi khusus untuk direktur di surat pengalihan (biarkan logika lama)
					if ($documentType === 'surat_pengalihan' && !empty($overlay['is_direktur'])) {
						$y += 0.03 * $size['height'];
					}

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

			return $newFilePath;
		} catch (\Exception $e) {
			\Log::error('PDF Signing Error: ' . $e->getMessage(), [
				'pengajuan_id' => $pengajuan->id,
				'document_type' => $documentType
			]);
			return null;
		}
	}
}
