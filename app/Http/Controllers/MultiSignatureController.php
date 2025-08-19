<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\PengajuanHki;
use App\Models\Signature;
use App\Models\User;

class MultiSignatureController extends Controller
{
	/**
	 * Tampilkan halaman daftar signatures untuk pengajuan
	 */
	public function index($pengajuanId)
	{
		$pengajuan = PengajuanHki::with(['signatures.signedBy', 'user'])->findOrFail($pengajuanId);
		
		// Check permission
		if (!$this->canAccessSignatures($pengajuan)) {
			abort(403, 'Tidak memiliki akses ke halaman ini');
		}

		return view('signatures.index', compact('pengajuan'));
	}

	/**
	 * Tampilkan halaman sign untuk pencipta tertentu dengan token
	 */
	public function showSignPage($token)
	{
		$signature = Signature::where('signature_token', $token)->firstOrFail();
		$pengajuan = $signature->pengajuanHki;

		// Check if already signed
		if ($signature->status === 'signed') {
			return view('signatures.already-signed', compact('signature', 'pengajuan'));
		}

		return view('signatures.sign', compact('signature', 'pengajuan'));
	}

	/**
	 * Simpan tanda tangan untuk pencipta tertentu
	 */
	public function saveSignature(Request $request, $token)
	{
		$signature = Signature::where('signature_token', $token)->firstOrFail();
		
		if ($signature->status === 'signed') {
			return response()->json(['message' => 'Signature sudah ditandatangani sebelumnya'], 422);
		}

		$request->validate([
			'ktp_file' => 'required|image|mimes:jpeg,jpg,png|max:5120', // 5MB
			'signature_method' => 'required|in:canvas,upload',
			'signature_data' => 'required_if:signature_method,canvas',
			'signature_file' => 'required_if:signature_method,upload|image|mimes:jpeg,jpg,png|max:2048',
			'placement' => 'nullable|string'
		]);

		// Process KTP upload
		$ktpFile = $request->file('ktp_file');
		$ktpFilename = 'ktp_images/pengajuan_' . $signature->pengajuan_hki_id . '_pencipta_' . $signature->pencipta_ke . '_' . Str::uuid() . '.' . $ktpFile->getClientOriginalExtension();
		$ktpPath = $ktpFile->storeAs('', $ktpFilename, 'public');

		$signaturePath = null;
		$signatureImagePath = null;
		$page = null; $x = null; $y = null; $w = null; $h = null;

		if ($request->input('signature_method') === 'canvas') {
			// Process canvas signature data
			$dataUrl = $request->input('signature_data');
			if (!preg_match('/^data:image\/(png|jpe?g);base64,/', $dataUrl)) {
				return response()->json(['message' => 'Format data signature tidak valid'], 422);
			}

			$extension = (strpos($dataUrl, 'jpeg') !== false || strpos($dataUrl, 'jpg') !== false) ? 'jpg' : 'png';
			$imageData = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1));
			$filename = 'signatures/pengajuan_' . $signature->pengajuan_hki_id . '_pencipta_' . $signature->pencipta_ke . '_' . Str::uuid() . '.' . $extension;
			Storage::disk('public')->put($filename, $imageData);
			$signaturePath = $filename;
		} else {
			// Process uploaded signature image
			$signatureFile = $request->file('signature_file');
			$signatureFilename = 'signature_images/pengajuan_' . $signature->pengajuan_hki_id . '_pencipta_' . $signature->pencipta_ke . '_' . Str::uuid() . '.' . $signatureFile->getClientOriginalExtension();
			$signatureImagePath = $signatureFile->storeAs('', $signatureFilename, 'public');
		}

		// Placement optional
		if ($request->filled('placement')) {
			try {
				$pl = json_decode($request->input('placement'), true);
				$page = isset($pl['page']) ? (int)$pl['page'] : null;
				$x = isset($pl['x_percent']) ? (float)$pl['x_percent'] : null;
				$y = isset($pl['y_percent']) ? (float)$pl['y_percent'] : null;
				$w = isset($pl['width_percent']) ? (float)$pl['width_percent'] : null;
				$h = isset($pl['height_percent']) ? (float)$pl['height_percent'] : null;
			} catch (\Throwable $e) {
				// ignore invalid placement; fallback ke preset
			}
		}

		// Update signature record
		try {
			$signature->update([
				'signature_path' => $signaturePath,
				'signature_image_path' => $signatureImagePath,
				'ktp_path' => $ktpPath,
				'signed_at' => now(),
				'signed_by' => auth()->id() ?? null,
				'status' => 'signed',
				'page' => $page,
				'x_percent' => $x,
				'y_percent' => $y,
				'width_percent' => $w,
				'height_percent' => $h,
			]);
		} catch (\Exception $e) {
			Log::error('Error updating signature: ' . $e->getMessage());
			return response()->json(['message' => 'Error updating signature: ' . $e->getMessage()], 500);
		}

		// Add tracking
		try {
			$pengajuan = $signature->pengajuanHki;
			$pengajuan->addTracking(
				'signature_received',
				"Tanda Tangan Pencipta {$signature->pencipta_ke} Diterima",
				"Tanda tangan dari {$signature->nama_pencipta} telah diterima",
				'fas fa-signature',
				'success'
			);
		} catch (\Exception $e) {
			Log::error('Error adding tracking: ' . $e->getMessage());
			// Continue execution even if tracking fails
		}

		// Check if all signatures are complete - HANYA BUAT OVERLAY JIKA SEMUA LENGKAP
		try {
			$totalSignatures = \App\Models\Signature::where('pengajuan_hki_id', $signature->pengajuan_hki_id)->count();
			$signedSignatures = \App\Models\Signature::where('pengajuan_hki_id', $signature->pengajuan_hki_id)->where('status', 'signed')->count();
			$allKtpUploaded = \App\Models\Signature::where('pengajuan_hki_id', $signature->pengajuan_hki_id)
				->whereNull('ktp_path')->count() === 0;
			
			Log::info('Signature completion check', [
				'pengajuan_id' => $signature->pengajuan_hki_id,
				'total_signatures' => $totalSignatures,
				'signed_signatures' => $signedSignatures,
				'all_ktp_uploaded' => $allKtpUploaded,
				'all_complete' => $totalSignatures > 0 && $totalSignatures === $signedSignatures && $allKtpUploaded
			]);
			
			if ($totalSignatures > 0 && $totalSignatures === $signedSignatures && $allKtpUploaded) {
				$pengajuan->addTracking(
					'all_signatures_complete',
					'Semua Tanda Tangan & KTP Lengkap',
					'Semua pencipta telah menandatangani dokumen dan upload KTP',
					'fas fa-check-circle',
					'success'
				);
				// BUAT OVERLAY TANDA TANGAN SETELAH SEMUA LENGKAP (hormati koordinat jika tersedia)
				$this->createOverlayDataFromSignatures($pengajuan);
				// AUTO-SIGN PDF SETELAH OVERLAY DIBUAT
				try {
					$pdfSigner = new \App\Http\Controllers\PdfSigningController();
					$pengajuan->refresh();
					$dokumenJson = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
					$overlays = $dokumenJson['overlays'] ?? [];
					$suratController = new \App\Http\Controllers\SuratController();
					if (!isset($dokumenJson['surat_pengalihan'])) {
						$pengalihanPath = $suratController->autoGeneratePengalihan($pengajuan);
						if ($pengalihanPath) { $dokumenJson['surat_pengalihan'] = $pengalihanPath; }
					}
					if (!isset($dokumenJson['surat_pernyataan'])) {
						$pernyataanPath = $suratController->autoGeneratePernyataan($pengajuan);
						if ($pernyataanPath) { $dokumenJson['surat_pernyataan'] = $pernyataanPath; }
					}
					$pengajuan->update(['file_dokumen_pendukung' => json_encode($dokumenJson)]);
					foreach (['surat_pengalihan', 'surat_pernyataan'] as $docType) {
						if (isset($overlays[$docType]) && !empty($overlays[$docType]) && isset($dokumenJson[$docType])) {
							$signedPath = $pdfSigner->signPdf($pengajuan, $docType);
							if ($signedPath) { Log::info("Auto-signed PDF created: {$signedPath} for pengajuan {$pengajuan->id}"); }
							else { Log::error("Failed to create signed PDF for {$docType} in pengajuan {$pengajuan->id}"); }
						} else {
							Log::warning("Skipping {$docType} - missing overlay or document", [
								'pengajuan_id' => $pengajuan->id,
								'has_overlay' => isset($overlays[$docType]) && !empty($overlays[$docType]),
								'has_document' => isset($dokumenJson[$docType])
							]);
						}
					}
					$pengajuan->addTracking(
						'documents_auto_signed',
						'Dokumen Ditandatangani Otomatis',
						'Surat pengalihan dan pernyataan telah ditandatangani dengan tanda tangan pencipta',
						'fas fa-file-signature',
						'success'
					);
				} catch (\Exception $e) {
					Log::error('Failed to auto-sign PDFs: ' . $e->getMessage(), [
						'pengajuan_id' => $pengajuan->id,
						'error' => $e->getTraceAsString()
					]);
				}
				// Setelah auto-sign, buat KTP gabungan
				try {
					$ktpCombined = $this->generateCombinedKtpDocument($signature->pengajuan_hki_id);
					if ($ktpCombined) {
						$pengajuan->addTracking('ktp_combined', 'Dokumen KTP Gabungan Dibuat', 'Semua KTP pencipta telah digabung menjadi satu dokumen', 'fas fa-file-pdf', 'success');
					} else {
						Log::warning('Combined KTP not generated', ['pengajuan_id' => $pengajuan->id]);
					}
				} catch (\Exception $e) {
					Log::error('Failed to generate combined KTP after signing: ' . $e->getMessage(), [ 'pengajuan_id' => $pengajuan->id ]);
				}
			}
		} catch (\Exception $e) {
			Log::error('Error in signature completion check: ' . $e->getMessage(), [
				'pengajuan_id' => $signature->pengajuan_hki_id,
				'error' => $e->getTraceAsString()
			]);
		}

		// Tentukan redirect yang lebih tepat berdasarkan peran pengguna
		$redirectUrl = route('signatures.index', $pengajuan->id);
		$user = auth()->user();
		if ($user && $user->role === 'direktur') { $redirectUrl = route('persetujuan.index'); }

		return response()->json(['message' => 'Tanda tangan berhasil disimpan','redirect' => $redirectUrl]);
	}

	/**
	 * Tampilkan preview dokumen dengan signatures
	 */
	public function previewDocument($pengajuanId)
	{
		$pengajuan = PengajuanHki::with(['signatures' => function($query) { $query->where('status', 'signed'); }])->findOrFail($pengajuanId);
		if (!$this->canAccessSignatures($pengajuan)) { abort(403, 'Tidak memiliki akses ke halaman ini'); }
		return view('signatures.preview-document', compact('pengajuan'));
	}

	/** Regenerate KTP Gabungan secara manual */
	public function regenerateKtp($pengajuanId)
	{
		$pengajuan = PengajuanHki::findOrFail($pengajuanId);
		try {
			$path = $this->generateCombinedKtpDocument($pengajuanId);
			return response()->json(['message' => $path ? 'KTP gabungan dibuat' : 'Tidak ada KTP yang dapat digabung', 'path' => $path]);
		} catch (\Exception $e) {
			Log::error('Regenerate KTP failed', ['pengajuan_id' => $pengajuanId, 'error' => $e->getMessage()]);
			return response()->json(['message' => 'Gagal membuat KTP gabungan'], 500);
		}
	}

	/**
	 * Reset signature untuk pencipta tertentu (admin only)
	 */
	public function resetSignature(Request $request, $signatureId)
	{
		if (auth()->user()->role !== 'admin') { abort(403, 'Hanya admin yang dapat mereset tanda tangan'); }
		$signature = Signature::findOrFail($signatureId);
		if ($signature->signature_path && Storage::disk('public')->exists($signature->signature_path)) { Storage::disk('public')->delete($signature->signature_path); }
		$signature->update(['signature_path' => null,'signed_at' => null,'signed_by' => null,'status' => 'pending','signature_token' => Signature::generateToken(),'page' => null,'x_percent' => null,'y_percent' => null,'width_percent' => null,'height_percent' => null]);
		$pengajuan = $signature->pengajuanHki;
		$pengajuan->addTracking('signature_reset', "Tanda Tangan Pencipta {$signature->pencipta_ke} Direset", "Tanda tangan dari {$signature->nama_pencipta} telah direset oleh admin", 'fas fa-undo','warning');
		return response()->json(['message' => 'Tanda tangan berhasil direset']);
	}

	/**
	 * Kirim email reminder untuk tanda tangan
	 */
	public function sendReminder($signatureId)
	{
		$signature = Signature::with('pengajuanHki')->findOrFail($signatureId);
		if ($signature->status === 'signed') { return response()->json(['message' => 'Tanda tangan sudah lengkap'], 422); }
		if (!$signature->email_pencipta) { return response()->json(['message' => 'Email pencipta tidak tersedia'], 422); }
		return response()->json(['message' => 'Reminder berhasil dikirim']);
	}

	/**
	 * Get signature progress untuk AJAX
	 */
	public function getProgress($pengajuanId)
	{
		$signatures = Signature::forPengajuan($pengajuanId)->get();
		$progress = Signature::getProgressForPengajuan($pengajuanId);
		return response()->json(['progress' => $progress,'total' => $signatures->count(),'signed' => $signatures->where('status', 'signed')->count(),'pending' => $signatures->where('status', 'pending')->count(),'signatures' => $signatures->map(function($sig) {return ['id' => $sig->id,'pencipta_ke' => $sig->pencipta_ke,'nama_pencipta' => $sig->nama_pencipta,'status' => $sig->status,'signed_at' => $sig->signed_at?->format('d/m/Y H:i'),'signed_by' => $sig->signedBy?->nama_lengkap];})]);
	}

	/**
	 * Check permission untuk akses signatures
	 */
	private function canAccessSignatures($pengajuan)
	{
		$user = auth()->user();
		if ($user->role === 'admin') { return true; }
		if ($pengajuan->user_id === $user->id) { return true; }
		if ($user->role === 'direktur') { return true; }
		return false;
	}

	/**
	 * Create overlay data from collected signatures
	 */
	private function createOverlayDataFromSignatures($pengajuan)
	{
		$signatures = Signature::where('pengajuan_hki_id', $pengajuan->id)->where('status', 'signed')->get();
		Log::info('Creating overlay data from signatures', ['pengajuan_id' => $pengajuan->id,'signatures_count' => $signatures->count(),'signatures' => $signatures->map(function($sig) {return ['id' => $sig->id,'pencipta_ke' => $sig->pencipta_ke,'status' => $sig->status,'signature_path' => $sig->signature_path,'signature_image_path' => $sig->signature_image_path,'page' => $sig->page,'x' => $sig->x_percent,'y' => $sig->y_percent,'w' => $sig->width_percent,'h' => $sig->height_percent];})]);
		if ($signatures->isEmpty()) { Log::warning('No signed signatures found for overlay creation', ['pengajuan_id' => $pengajuan->id]); return; }
		$dokumenJson = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
		if (!isset($dokumenJson['overlays'])) { $dokumenJson['overlays'] = []; }

		$signaturePositions = [
			'surat_pengalihan' => [
				1 => ['page' => 1, 'x_percent' => 65.0, 'y_percent' => 82.5],
				2 => ['page' => 2, 'x_percent' => 15.0, 'y_percent' => 18.0],
				3 => ['page' => 2, 'x_percent' => 65.0, 'y_percent' => 18.0],
				4 => ['page' => 2, 'x_percent' => 15.0, 'y_percent' => 43.0],
				5 => ['page' => 2, 'x_percent' => 65.0, 'y_percent' => 43.0],
			],
		];

		foreach (['surat_pengalihan'] as $documentType) {
			$overlays = [];
			foreach ($signatures as $signature) {
				$signatureFile = null;
				if ($signature->signature_path && Storage::disk('public')->exists($signature->signature_path)) { $signatureFile = $signature->signature_path; }
				elseif ($signature->signature_image_path && Storage::disk('public')->exists($signature->signature_image_path)) { $signatureFile = $signature->signature_image_path; }
				if ($signatureFile) {
					// Jika user sudah menentukan koordinat (tempel), gunakan itu
					if ($signature->page && $signature->x_percent !== null && $signature->y_percent !== null) {
						$pos = ['page' => (int)$signature->page,'x_percent' => (float)$signature->x_percent,'y_percent' => (float)$signature->y_percent,'width_percent' => (float)($signature->width_percent ?? 15.0),'height_percent' => (float)($signature->height_percent ?? 4.0)];
					} else {
						// Fallback ke preset
						$penciptaKe = $signature->pencipta_ke;
						$docPositions = $signaturePositions[$documentType] ?? $signaturePositions['surat_pengalihan'];
						$pos = $docPositions[$penciptaKe] ?? ['page' => 2 + floor(($penciptaKe - 6) / 4),'x_percent' => ($penciptaKe % 2 == 1) ? 25.0 : 75.0,'y_percent' => 35.0 + (floor(($penciptaKe - 6) / 2) * 40),];
						$pos['width_percent'] = 15.0; $pos['height_percent'] = 4.0;
					}
					$overlays[] = ['type' => 'signature','url' => Storage::url($signatureFile),'page' => $pos['page'],'x_percent' => $pos['x_percent'],'y_percent' => $pos['y_percent'],'width_percent' => $pos['width_percent'],'height_percent' => $pos['height_percent'],'auto' => true];
				}
			}
			$dokumenJson['overlays'][$documentType] = $overlays;
		}
		
		// Direktur (jika ada)
		$direkturSignature = $signatures->first(function($sig) { return ($sig->pencipta_ke == 0 || stripos($sig->nama_pencipta, 'direktur') !== false) && $sig->status === 'signed'; });
		if ($direkturSignature) {
			$signatureFile = null;
			if ($direkturSignature->signature_path && \Storage::disk('public')->exists($direkturSignature->signature_path)) { $signatureFile = $direkturSignature->signature_path; }
			elseif ($direkturSignature->signature_image_path && \Storage::disk('public')->exists($direkturSignature->signature_image_path)) { $signatureFile = $direkturSignature->signature_image_path; }
			if ($signatureFile) {
				$dokumenJson['overlays']['surat_pengalihan'][] = ['type' => 'signature','url' => \Storage::url($signatureFile),'page' => 1,'x_percent' => 38.0,'y_percent' => 63.0,'width_percent' => 24.0,'height_percent' => 6.0,'is_direktur' => true,];
				$dokumenJson['overlays']['surat_pernyataan'][] = ['type' => 'signature','url' => \Storage::url($signatureFile),'page' => 2,'x_percent' => 60.0,'y_percent' => 40.0,'width_percent' => 20.0,'height_percent' => 5.0,'is_direktur' => true,];
			}
		}

		Log::info('Final dokumen JSON before save', ['pengajuan_id' => $pengajuan->id,'overlays' => $dokumenJson['overlays'] ?? []]);
		$pengajuan->update(['file_dokumen_pendukung' => json_encode($dokumenJson)]);
	}

	/**
	 * Combine all KTP images from signatures into one PDF document
	 * Format: 1. Pemohon, 2. Direktur Polban, 3-N. Pencipta KTP
	 */
	public function generateCombinedKtpDocument($pengajuanId)
	{
		$pengajuan = PengajuanHki::findOrFail($pengajuanId);
		$dokumen = is_string($pengajuan->file_dokumen_pendukung) ? json_decode($pengajuan->file_dokumen_pendukung, true) : ($pengajuan->file_dokumen_pendukung ?? []);
		$signatures = Signature::where('pengajuan_hki_id', $pengajuanId)
							   ->where('status', 'signed')
							   ->orderBy('pencipta_ke')
							   ->get();
		try {
			$pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
			$pdf->SetCreator('Sistem HKI Poliban');
			$pdf->SetAuthor('Politeknik Negeri Banjarmasin');
			$pdf->SetTitle('KTP Gabungan - ' . $pengajuan->judul_karya);
			$pdf->SetSubject('Dokumen KTP Gabungan');
			$pdf->SetMargins(10, 15, 10);
			$pdf->SetAutoPageBreak(true, 15);
			// --- Halaman 1: KTP Pemohon & Pemegang Hak Cipta ---
			$pdf->AddPage();
			$pdf->SetFont('helvetica', '', 12);
			// KTP Pemohon (kiri)
			$pdf->SetXY(15, 20);
			$pdf->SetFont('helvetica', 'B', 11);
			$pdf->Cell(90, 8, 'KTP Pemohon', 0, 2, 'L');
			$pdf->SetFont('helvetica', '', 10);
			$ktpPemohon = isset($dokumen['ktp_pemohon']) && Storage::disk('public')->exists($dokumen['ktp_pemohon'])
				? storage_path('app/public/' . $dokumen['ktp_pemohon']) : null;
			if ($ktpPemohon && file_exists($ktpPemohon)) {
				$pdf->Image($ktpPemohon, 15, 38, 80, 50, '', '', '', false);
			} else {
				$pdf->Rect(15, 38, 80, 50);
				$pdf->SetXY(15, 60);
				$pdf->Cell(80, 10, 'KTP tidak tersedia', 0, 2, 'C');
			}
			// KTP Pemegang Hak Cipta (kanan)
			$pdf->SetXY(110, 20);
			$pdf->SetFont('helvetica', 'B', 11);
			$pdf->Cell(90, 8, 'KTP Pemegang Hak Cipta', 0, 2, 'L');
			$pdf->SetFont('helvetica', '', 10);
			$pdf->Cell(90, 6, '(Direktur)', 0, 2, 'L');
			$ktpPemegang = isset($dokumen['ktp_pemegang_hak']) && Storage::disk('public')->exists($dokumen['ktp_pemegang_hak'])
				? storage_path('app/public/' . $dokumen['ktp_pemegang_hak']) : null;
			if ($ktpPemegang && file_exists($ktpPemegang)) {
				$pdf->Image($ktpPemegang, 110, 38, 80, 50, '', '', '', false);
			} else {
				$pdf->Rect(110, 38, 80, 50);
				$pdf->SetXY(110, 60);
				$pdf->Cell(80, 10, 'KTP tidak tersedia', 0, 2, 'C');
			}
			// --- KTP Pencipta: gabungkan pada halaman yang sama (maksimal 5) ---
			$penciptaList = $signatures->sortBy('pencipta_ke')->values();
			if ($penciptaList->count() > 0) {
				$pdf->SetFont('helvetica', 'B', 12);
				$pdf->SetXY(15, 96);
				$pdf->Cell(0, 8, 'KTP Pencipta', 0, 1, 'L');

				$perRow = 2;
				$imgWidth = 80;
				$imgHeight = 45;
				foreach ($penciptaList as $i => $sig) {
					if ($i >= 5) break;
					$row = (int)($i / $perRow);
					$col = $i % $perRow;
					$x = 15 + $col * 95;
					$y = 105 + $row * 60;
					$pdf->SetXY($x, $y);
					$pdf->SetFont('helvetica', 'B', 10);
					$pdf->Cell($imgWidth, 6, 'KTP Pencipta ' . ($sig->pencipta_ke) , 0, 2, 'L');
					$pdf->SetFont('helvetica', '', 9);
					$pdf->Cell($imgWidth, 5, '(' . $sig->nama_pencipta . ')', 0, 2, 'L');
					$ktpPath = ($sig->ktp_path && Storage::disk('public')->exists($sig->ktp_path)) ? storage_path('app/public/' . $sig->ktp_path) : null;
					if ($ktpPath && file_exists($ktpPath)) {
						$pdf->Image($ktpPath, $x, $y + 15, $imgWidth, $imgHeight, '', '', '', false);
					} else {
						$pdf->Rect($x, $y + 15, $imgWidth, $imgHeight);
						$pdf->SetXY($x, $y + 35);
						$pdf->Cell($imgWidth, 10, 'KTP tidak tersedia', 0, 2, 'C');
					}
				}
			}
			// Save the combined PDF
			$timestamp = now()->format('Ymd_His');
			$fileName = 'ktp_gabungan_pengajuan_' . $pengajuan->id . '_' . $timestamp . '.pdf';
			$filePath = 'combined_ktp/' . $fileName;
			$combinedKtpDir = storage_path('app/public/combined_ktp');
			if (!file_exists($combinedKtpDir)) { mkdir($combinedKtpDir, 0777, true); \Log::info('Folder combined_ktp dibuat', ['path' => $combinedKtpDir]); }
			$fullPath = storage_path('app/public/' . $filePath);
			$pdf->Output($fullPath, 'F');
			if (!file_exists($fullPath)) { \Log::error('File PDF KTP Gabungan gagal dibuat', ['pengajuan_id' => $pengajuan->id,'file_path' => $fullPath]); return null; }
			$dokumen['ktp_gabungan'] = $filePath;
			$pengajuan->file_dokumen_pendukung = json_encode($dokumen);
			$pengajuan->save();
			\Log::info('Combined KTP document generated successfully', ['pengajuan_id' => $pengajuan->id,'file_path' => $filePath,'signatures_count' => $signatures->count()]);
			return $filePath;
		} catch (\Exception $e) {
			\Log::error('Error generating combined KTP document', ['pengajuan_id' => $pengajuan->id,'error' => $e->getMessage(),'trace' => $e->getTraceAsString()]);
			return null;
		}
	}

	/**
	 * Add a KTP page to the PDF with proper formatting
	 */
	private function addKtpPage($pdf, $title, $name, $ktpImagePath, $pageNumber)
	{
		$pdf->AddPage();
		
		// Add page header
		$pdf->SetFont('helvetica', 'B', 16);
		$pdf->Cell(0, 12, $title, 0, 1, 'C');
		$pdf->SetFont('helvetica', '', 12);
		$pdf->Cell(0, 8, '(' . $name . ')', 0, 1, 'C');
		$pdf->Ln(10);

		if ($ktpImagePath && file_exists($ktpImagePath)) {
			// Get image dimensions and calculate display size
			$imageInfo = getimagesize($ktpImagePath);
			if ($imageInfo) {
				$originalWidth = $imageInfo[0];
				$originalHeight = $imageInfo[1];
				
				// Calculate display dimensions (fit 2 per page like in example)
				$maxWidth = 170; // mm (leave margins)
				$maxHeight = 110; // mm (fit 2 per page)
				
				$ratio = min($maxWidth / ($originalWidth * 0.264583), $maxHeight / ($originalHeight * 0.264583));
				$displayWidth = ($originalWidth * 0.264583) * $ratio;
				$displayHeight = ($originalHeight * 0.264583) * $ratio;
				
				// Center the image horizontally
				$x = (210 - $displayWidth) / 2;
				$y = $pdf->GetY();
				
				$pdf->Image($ktpImagePath, $x, $y, $displayWidth, $displayHeight);
			}
		} else {
			// Show placeholder when KTP image not available
			$pdf->SetFont('helvetica', 'I', 12);
			$pdf->SetFillColor(240, 240, 240);
			$pdf->Cell(0, 80, 'KTP tidak tersedia atau belum diupload', 1, 1, 'C', true);
			$pdf->SetFont('helvetica', '', 10);
			$pdf->Cell(0, 8, 'Harap upload KTP melalui sistem signature', 0, 1, 'C');
		}
	}

	/**
	 * Automatically generate combined KTP document when all signatures are complete
	 */
	private function autoGenerateCombinedKtp($pengajuanId)
	{
		try {
			$this->generateCombinedKtpDocument($pengajuanId);
		} catch (\Exception $e) {
			Log::error('Auto-generate combined KTP failed', [
				'pengajuan_id' => $pengajuanId,
				'error' => $e->getMessage()
			]);
		}
	}
}
