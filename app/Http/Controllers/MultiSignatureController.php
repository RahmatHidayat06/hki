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
            'signature_file' => 'required_if:signature_method,upload|image|mimes:jpeg,jpg,png|max:2048'
        ]);

        // Process KTP upload
        $ktpFile = $request->file('ktp_file');
        $ktpFilename = 'ktp_images/pengajuan_' . $signature->pengajuan_hki_id . '_pencipta_' . $signature->pencipta_ke . '_' . Str::uuid() . '.' . $ktpFile->getClientOriginalExtension();
        $ktpPath = $ktpFile->storeAs('', $ktpFilename, 'public');

        $signaturePath = null;
        $signatureImagePath = null;

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

        // Update signature record
        try {
        $signature->update([
                'signature_path' => $signaturePath,
                'signature_image_path' => $signatureImagePath,
                'ktp_path' => $ktpPath,
            'signed_at' => now(),
                'signed_by' => auth()->id() ?? null, // Handle case when user not authenticated
            'status' => 'signed'
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
            
            Log::info('Signature completion check', [
                'pengajuan_id' => $signature->pengajuan_hki_id,
                'total_signatures' => $totalSignatures,
                'signed_signatures' => $signedSignatures,
                'all_complete' => $totalSignatures > 0 && $totalSignatures === $signedSignatures
            ]);
            
        if (Signature::allSignedForPengajuan($signature->pengajuan_hki_id)) {
            $pengajuan->addTracking(
                'all_signatures_complete',
                'Semua Tanda Tangan Lengkap',
                'Semua pencipta telah menandatangani dokumen',
                'fas fa-check-circle',
                'success'
            );

                // BUAT OVERLAY TANDA TANGAN SETELAH SEMUA LENGKAP
                $this->createOverlayDataFromSignatures($pengajuan);

                // AUTO-SIGN PDF SETELAH OVERLAY DIBUAT
                try {
                    $pdfSigner = new \App\Http\Controllers\PdfSigningController();
                    
                    // Refresh dokumen JSON untuk mendapatkan overlay terbaru
                    $pengajuan->refresh();
                    $dokumenJson = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
                    $overlays = $dokumenJson['overlays'] ?? [];
                    
                    // Pastikan semua dokumen sudah ada, jika belum generate dulu
                    $suratController = new \App\Http\Controllers\SuratController();
                    if (!isset($dokumenJson['form_permohonan_pendaftaran'])) {
                        $formPath = $suratController->autoGenerateFormPermohonan($pengajuan);
                        if ($formPath) {
                            $dokumenJson['form_permohonan_pendaftaran'] = $formPath;
                        }
                    }
                    if (!isset($dokumenJson['surat_pengalihan'])) {
                        $pengalihanPath = $suratController->autoGeneratePengalihan($pengajuan);
                        if ($pengalihanPath) {
                            $dokumenJson['surat_pengalihan'] = $pengalihanPath;
                        }
                    }
                    if (!isset($dokumenJson['surat_pernyataan'])) {
                        $pernyataanPath = $suratController->autoGeneratePernyataan($pengajuan);
                        if ($pernyataanPath) {
                            $dokumenJson['surat_pernyataan'] = $pernyataanPath;
                        }
                    }
                    
                    // Update pengajuan dengan semua dokumen yang baru dibuat (jika ada)
                    $pengajuan->update(['file_dokumen_pendukung' => json_encode($dokumenJson)]);
                    
                    // Generate signed PDF untuk semua dokumen yang ada overlay
                    foreach (['form_permohonan_pendaftaran', 'surat_pengalihan', 'surat_pernyataan'] as $docType) {
                        if (isset($overlays[$docType]) && !empty($overlays[$docType]) && isset($dokumenJson[$docType])) {
                            $signedPath = $pdfSigner->signPdf($pengajuan, $docType);
                            if ($signedPath) {
                                Log::info("Auto-signed PDF created: {$signedPath} for pengajuan {$pengajuan->id}");
                            } else {
                                Log::error("Failed to create signed PDF for {$docType} in pengajuan {$pengajuan->id}");
                            }
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

            // Update status pengajuan jika perlu
            if ($pengajuan->status === 'menunggu_tanda_tangan') {
                $pengajuan->update(['status' => 'siap_dikirim']);
                $pengajuan->addTracking(
                    'ready_to_send',
                    'Siap Dikirim ke DJKI',
                    'Pengajuan siap untuk dikirim ke Direktorat Jenderal Kekayaan Intelektual',
                    'fas fa-paper-plane',
                    'primary'
                );
            }

            // Generate combined KTP document
            try {
                $ktpPath = $this->generateCombinedKtpDocument($signature->pengajuan_hki_id);
                if ($ktpPath) {
                    $pengajuan->addTracking(
                        'ktp_combined',
                        'Dokumen KTP Gabungan Dibuat',
                        'Semua KTP pencipta telah digabung menjadi satu dokumen',
                        'fas fa-file-pdf',
                        'success'
                    );
                }
            } catch (\Exception $e) {
                Log::error('Failed to generate combined KTP: ' . $e->getMessage());
            }

                            // Auto-generate surat pengalihan dan pernyataan dengan tanda tangan (DISABLED TEMPORARILY)
                // try {
                //     $suratController = new \App\Http\Controllers\SuratController();
                //     
                //     // Generate surat pengalihan
                //     $request_data = new \Illuminate\Http\Request(['pengajuan_id' => $pengajuan->id]);
                //     $suratController->generateSuratPengalihan($request_data);
                //     
                //     // Generate surat pernyataan  
                //     $suratController->generateSuratPernyataan($request_data);
                //     
                //     $pengajuan->addTracking(
                //         'documents_generated',
                //         'Dokumen Surat Otomatis Dibuat',
                //         'Surat pengalihan dan surat pernyataan telah dibuat dengan tanda tangan',
                //         'fas fa-file-contract',
                //         'success'
                //     );
                // } catch (\Exception $e) {
                //     Log::error('Failed to auto-generate letters: ' . $e->getMessage());
                // }
            }
        } catch (\Exception $e) {
            Log::error('Error in completion check: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Tanda tangan berhasil disimpan',
            'redirect' => route('signatures.index', $pengajuan->id)
        ]);
    }

    /**
     * Tampilkan preview dokumen dengan signatures
     */
    public function previewDocument($pengajuanId)
    {
        $pengajuan = PengajuanHki::with(['signatures' => function($query) {
            $query->where('status', 'signed');
        }])->findOrFail($pengajuanId);

        if (!$this->canAccessSignatures($pengajuan)) {
            abort(403, 'Tidak memiliki akses ke halaman ini');
        }

        return view('signatures.preview-document', compact('pengajuan'));
    }

    /**
     * Reset signature untuk pencipta tertentu (admin only)
     */
    public function resetSignature(Request $request, $signatureId)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat mereset tanda tangan');
        }

        $signature = Signature::findOrFail($signatureId);
        
        // Delete old signature file if exists
        if ($signature->signature_path && Storage::disk('public')->exists($signature->signature_path)) {
            Storage::disk('public')->delete($signature->signature_path);
        }

        // Reset signature
        $signature->update([
            'signature_path' => null,
            'signed_at' => null,
            'signed_by' => null,
            'status' => 'pending',
            'signature_token' => Signature::generateToken()
        ]);

        // Add tracking
        $pengajuan = $signature->pengajuanHki;
        $pengajuan->addTracking(
            'signature_reset',
            "Tanda Tangan Pencipta {$signature->pencipta_ke} Direset",
            "Tanda tangan dari {$signature->nama_pencipta} telah direset oleh admin",
            'fas fa-undo',
            'warning'
        );

        return response()->json(['message' => 'Tanda tangan berhasil direset']);
    }

    /**
     * Kirim email reminder untuk tanda tangan
     */
    public function sendReminder($signatureId)
    {
        $signature = Signature::with('pengajuanHki')->findOrFail($signatureId);
        
        if ($signature->status === 'signed') {
            return response()->json(['message' => 'Tanda tangan sudah lengkap'], 422);
        }

        if (!$signature->email_pencipta) {
            return response()->json(['message' => 'Email pencipta tidak tersedia'], 422);
        }

        // Send email reminder (implement sesuai kebutuhan)
        // Mail::to($signature->email_pencipta)->send(new SignatureReminderMail($signature));

        return response()->json(['message' => 'Reminder berhasil dikirim']);
    }

    /**
     * Get signature progress untuk AJAX
     */
    public function getProgress($pengajuanId)
    {
        $signatures = Signature::forPengajuan($pengajuanId)->get();
        $progress = Signature::getProgressForPengajuan($pengajuanId);
        
        return response()->json([
            'progress' => $progress,
            'total' => $signatures->count(),
            'signed' => $signatures->where('status', 'signed')->count(),
            'pending' => $signatures->where('status', 'pending')->count(),
            'signatures' => $signatures->map(function($sig) {
                return [
                    'id' => $sig->id,
                    'pencipta_ke' => $sig->pencipta_ke,
                    'nama_pencipta' => $sig->nama_pencipta,
                    'status' => $sig->status,
                    'signed_at' => $sig->signed_at?->format('d/m/Y H:i'),
                    'signed_by' => $sig->signedBy?->nama_lengkap
                ];
            })
        ]);
    }

    /**
     * Check permission untuk akses signatures
     */
    private function canAccessSignatures($pengajuan)
    {
        $user = auth()->user();
        
        // Admin dapat akses semua
        if ($user->role === 'admin') {
            return true;
        }
        
        // Pemilik pengajuan dapat akses
        if ($pengajuan->user_id === $user->id) {
            return true;
        }
        
        // Direktur dapat akses untuk approval
        if ($user->role === 'direktur') {
            return true;
        }
        
        return false;
    }

    /**
     * Create overlay data from collected signatures
     */
    private function createOverlayDataFromSignatures($pengajuan)
    {
        $signatures = Signature::where('pengajuan_hki_id', $pengajuan->id)
            ->where('status', 'signed')
            ->get();

        Log::info('Creating overlay data from signatures', [
            'pengajuan_id' => $pengajuan->id,
            'signatures_count' => $signatures->count(),
            'signatures' => $signatures->map(function($sig) {
                return [
                    'id' => $sig->id,
                    'pencipta_ke' => $sig->pencipta_ke,
                    'status' => $sig->status,
                    'signature_path' => $sig->signature_path,
                    'signature_image_path' => $sig->signature_image_path
                ];
            })
        ]);

        if ($signatures->isEmpty()) {
            Log::warning('No signed signatures found for overlay creation', ['pengajuan_id' => $pengajuan->id]);
            return;
        }

        $dokumenJson = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
        if (!isset($dokumenJson['overlays'])) {
            $dokumenJson['overlays'] = [];
        }

        // Posisi signature yang disesuaikan agar rapi dan tepat setelah informasi masing-masing pencipta
        $signaturePositions = [
            'surat_pengalihan' => [
                1 => ['page' => 1, 'x_percent' => 65.0, 'y_percent' => 82.5], // Pencipta I - di tengah area tanda tangan halaman 1
                2 => ['page' => 2, 'x_percent' => 25.0, 'y_percent' => 18.0], // Pencipta II - di tengah area tanda tangan kiri atas halaman 2
                3 => ['page' => 2, 'x_percent' => 65.0, 'y_percent' => 18.0], // Pencipta III - di tengah area tanda tangan kanan atas halaman 2
                4 => ['page' => 2, 'x_percent' => 25.0, 'y_percent' => 43.0], // Pencipta IV - di tengah area tanda tangan kiri bawah halaman 2
                5 => ['page' => 2, 'x_percent' => 65.0, 'y_percent' => 43.0], // Pencipta V - di tengah area tanda tangan kanan bawah halaman 2
            ],  
            'form_permohonan_pendaftaran' => [
                'pemohon' => ['page' => 2, 'x_percent' => 65.0, 'y_percent' => 75.0], // Signature area pemohon di akhir form
            ]
        ];

        foreach (['surat_pengalihan', 'form_permohonan_pendaftaran'] as $documentType) {
            $overlays = [];
            foreach ($signatures as $signature) {
                // Use signature_path (canvas) or signature_image_path (upload) whichever is available
                $signatureFile = null;
                if ($signature->signature_path && Storage::disk('public')->exists($signature->signature_path)) {
                    $signatureFile = $signature->signature_path;
                } elseif ($signature->signature_image_path && Storage::disk('public')->exists($signature->signature_image_path)) {
                    $signatureFile = $signature->signature_image_path;
                }

                if ($signatureFile) {
                    $penciptaKe = $signature->pencipta_ke;
                    $docPositions = $signaturePositions[$documentType] ?? $signaturePositions['surat_pengalihan'];
                    if ($documentType === 'form_permohonan_pendaftaran') {
                        if ($penciptaKe === 1) { // Hanya signature pertama yang digunakan sebagai pemohon
                            $pos = $docPositions['pemohon'];
                            $overlays[] = [
                                'type' => 'signature',
                                'url' => Storage::url($signatureFile),
                                'page' => $pos['page'],
                                'x_percent' => $pos['x_percent'],
                                'y_percent' => $pos['y_percent'],
                                'width_percent' => 15.0,
                                'height_percent' => 4.0,
                                'auto' => true // penanda overlay otomatis
                            ];
                        }
                    } else {
                        $pos = $docPositions[$penciptaKe] ?? [
                            'page' => 2 + floor(($penciptaKe - 6) / 4),
                            'x_percent' => ($penciptaKe % 2 == 1) ? 25.0 : 75.0,
                            'y_percent' => 35.0 + (floor(($penciptaKe - 6) / 2) * 40),
                        ];
                        $overlays[] = [
                            'type' => 'signature',
                            'url' => Storage::url($signatureFile),
                            'page' => $pos['page'],
                            'x_percent' => $pos['x_percent'],
                            'y_percent' => $pos['y_percent'],
                            'width_percent' => 15.0,
                            'height_percent' => 4.0,
                            'auto' => true
                        ];
                    }
                }
            }
            $dokumenJson['overlays'][$documentType] = $overlays;
        }
        
        // Tambahkan overlay signature direktur HANYA jika direktur sudah benar-benar tanda tangan
        $direkturSignature = $signatures->first(function($sig) {
            // Asumsi: pencipta_ke = 0 atau nama_pencipta mengandung 'Direktur'
            return ($sig->pencipta_ke == 0 || stripos($sig->nama_pencipta, 'direktur') !== false) && $sig->status === 'signed';
        });
        if ($direkturSignature) {
            $signatureFile = null;
            if ($direkturSignature->signature_path && \Storage::disk('public')->exists($direkturSignature->signature_path)) {
                $signatureFile = $direkturSignature->signature_path;
            } elseif ($direkturSignature->signature_image_path && \Storage::disk('public')->exists($direkturSignature->signature_image_path)) {
                $signatureFile = $direkturSignature->signature_image_path;
            }
            if ($signatureFile) {
                // Overlay untuk surat pengalihan (direktur)
                $dokumenJson['overlays']['surat_pengalihan'][] = [
                    'type' => 'signature',
                    'url' => \Storage::url($signatureFile),
                    'page' => 1,
                    'x_percent' => 25.0,
                    'y_percent' => 82.5,
                    'width_percent' => 20.0,
                    'height_percent' => 5.0,
                    'is_direktur' => true,
                ];
                // Overlay untuk surat pernyataan (direktur, halaman 2)
                $dokumenJson['overlays']['surat_pernyataan'][] = [
                    'type' => 'signature',
                    'url' => \Storage::url($signatureFile),
                    'page' => 2,
                    'x_percent' => 60.0,
                    'y_percent' => 40.0,
                    'width_percent' => 20.0,
                    'height_percent' => 5.0,
                    'is_direktur' => true,
                ];
            }
        }

        Log::info('Final dokumen JSON before save', [
            'pengajuan_id' => $pengajuan->id,
            'overlays' => $dokumenJson['overlays'] ?? []
        ]);
        
        $pengajuan->update([
            'file_dokumen_pendukung' => json_encode($dokumenJson)
        ]);
    }

    /**
     * Combine all KTP images from signatures into one PDF document
     * Format: 1. Pemohon, 2. Direktur Polban, 3-N. Pencipta KTP
     */
    public function generateCombinedKtpDocument($pengajuanId)
    {
        $pengajuan = PengajuanHki::findOrFail($pengajuanId);
        $signatures = Signature::where('pengajuan_hki_id', $pengajuanId)
                               ->where('status', 'signed')
                               ->orderBy('pencipta_ke')
                               ->get();

        try {
            $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('Sistem HKI Poliban');
            $pdf->SetAuthor('Politeknik Negeri Banjarmasin');
            $pdf->SetTitle('KTP Seluruh Pencipta - ' . $pengajuan->judul_karya);
            $pdf->SetSubject('Dokumen KTP Gabungan');
            $pdf->SetMargins(10, 15, 10);
            $pdf->SetAutoPageBreak(true, 15);

            // --- Halaman 1: KTP Pemohon & Direktur ---
            $pdf->AddPage();
            $pdf->SetFont('helvetica', '', 12);
            // KTP Pemohon (pencipta_ke=1)
            $pemohon = $signatures->firstWhere('pencipta_ke', 1);
            $ktpPemohon = ($pemohon && $pemohon->ktp_path && Storage::disk('public')->exists($pemohon->ktp_path))
                ? storage_path('app/public/' . $pemohon->ktp_path) : null;
            $pdf->SetXY(15, 20);
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->Cell(90, 8, 'KTP Pemohon', 0, 2, 'L');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(90, 6, '(' . ($pemohon->nama_pencipta ?? 'Pemohon') . ')', 0, 2, 'L');
            if ($ktpPemohon && file_exists($ktpPemohon)) {
                $pdf->Image($ktpPemohon, 15, 38, 80, 50, '', '', '', false);
            } else {
                $pdf->Rect(15, 38, 80, 50);
                $pdf->SetXY(15, 60);
                $pdf->Cell(80, 10, 'KTP tidak tersedia', 0, 2, 'C');
            }
            // KTP Direktur (gambar statis)
            $ktpDirektur = public_path('images/ktp-direktur.png');
            $pdf->SetXY(110, 20);
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->Cell(90, 8, 'KTP Pemegang Hak Cipta', 0, 2, 'L');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(90, 6, '(Direktur Politeknik Negeri Banjarmasin)', 0, 2, 'L');
            if (file_exists($ktpDirektur)) {
                $pdf->Image($ktpDirektur, 110, 38, 80, 50, '', '', '', false);
            } else {
                $pdf->Rect(110, 38, 80, 50);
                $pdf->SetXY(110, 60);
                $pdf->Cell(80, 10, 'KTP tidak tersedia', 0, 2, 'C');
            }

            // --- Halaman 2-3: KTP Pencipta Lain ---
            $penciptaLain = $signatures->filter(fn($s) => $s->pencipta_ke > 1)->values();
            $maxKtpPerPage = 4;
            $maxPages = 2; // halaman 2 & 3 saja
            $totalPages = min($maxPages, (int)ceil($penciptaLain->count() / $maxKtpPerPage));
            for ($page = 0; $page < $totalPages; $page++) {
                $pdf->AddPage();
                $pdf->SetFont('helvetica', 'B', 13);
                $pdf->Cell(0, 10, 'KTP Pencipta', 0, 1, 'L');
                $start = $page * $maxKtpPerPage;
                $end = min($start + $maxKtpPerPage, $penciptaLain->count());
                for ($i = $start; $i < $end; $i++) {
                    $row = (int)(($i - $start) / 2);
                    $col = ($i - $start) % 2;
                    $x = 15 + $col * 95;
                    $y = 25 + $row * 65;
                    $sig = $penciptaLain[$i];
                    $pdf->SetXY($x, $y);
                    $pdf->SetFont('helvetica', 'B', 10);
                    $pdf->Cell(80, 6, 'KTP Pencipta ' . $sig->pencipta_ke, 0, 2, 'L');
                    $pdf->SetFont('helvetica', '', 9);
                    $pdf->Cell(80, 5, '(' . $sig->nama_pencipta . ')', 0, 2, 'L');
                    $ktpPath = ($sig->ktp_path && Storage::disk('public')->exists($sig->ktp_path))
                        ? storage_path('app/public/' . $sig->ktp_path) : null;
                    if ($ktpPath && file_exists($ktpPath)) {
                        $pdf->Image($ktpPath, $x, $y + 15, 80, 45, '', '', '', false);
                    } else {
                        $pdf->Rect($x, $y + 15, 80, 45);
                        $pdf->SetXY($x, $y + 35);
                        $pdf->Cell(80, 10, 'KTP tidak tersedia', 0, 2, 'C');
                    }
                }
            }

            // Save the combined PDF
            $timestamp = now()->format('Ymd_His');
            $fileName = 'ktp_gabungan_pengajuan_' . $pengajuan->id . '_' . $timestamp . '.pdf';
            $filePath = 'combined_ktp/' . $fileName;
            Storage::disk('public')->makeDirectory('combined_ktp');
            $fullPath = storage_path('app/public/' . $filePath);
            $pdf->Output($fullPath, 'F');

            // Update pengajuan with combined KTP path
            $dokumenJson = is_string($pengajuan->file_dokumen_pendukung) 
                ? json_decode($pengajuan->file_dokumen_pendukung, true) 
                : ($pengajuan->file_dokumen_pendukung ?? []);
            $dokumenJson['ktp_gabungan'] = $filePath;
            $pengajuan->update([
                'file_dokumen_pendukung' => json_encode($dokumenJson)
            ]);
            Log::info('Combined KTP document generated successfully', [
                'pengajuan_id' => $pengajuan->id,
                'file_path' => $filePath,
                'signatures_count' => $signatures->count()
            ]);
            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error generating combined KTP document', [
                'pengajuan_id' => $pengajuan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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
