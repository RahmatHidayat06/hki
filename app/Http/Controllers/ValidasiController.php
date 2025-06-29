<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Log;
use Spatie\PdfToImage\Pdf;
use App\Http\Controllers\PdfSigningController;

class ValidasiController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!in_array(auth()->user()->role, ['admin', 'direktur'])) {
                abort(403, 'Unauthorized access');
            }
            return $next($request);
        });
    }

    public function index(): View
    {
        // Tampilkan pengajuan yang sudah divalidasi direktur dan menunggu finalisasi admin
        $pengajuan = PengajuanHki::whereIn('status', ['divalidasi_sedang_diproses'])->paginate(10);
        return view('validasi.index', compact('pengajuan'));
    }

    public function show(PengajuanHki $pengajuan): View
    {
        // Get available signatures
        $signatures = $this->getAvailableSignatures($pengajuan);
        
        // Get available stamps/materai
        $stamps = $this->getAvailableStamps();
        
        // Get documents with enhanced file selection logic
        // First try the dokumen field, then fall back to file_dokumen_pendukung
        $dokumen = [];
        if ($pengajuan->dokumen && is_string($pengajuan->dokumen)) {
            $dokumen = json_decode($pengajuan->dokumen, true) ?? [];
        } elseif (is_string($pengajuan->file_dokumen_pendukung)) {
            $dokumen = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
        } else {
            $dokumen = $pengajuan->file_dokumen_pendukung ?? [];
        }
        
        // Apply enhanced file selection logic for each document type
        $dokumen = $this->applyEnhancedFileSelection($dokumen);
        
        // Add page count information for each document
        $documentsPageInfo = [];
        $documentTypes = ['surat_pengalihan', 'surat_pernyataan', 'ktp'];
        
        foreach ($documentTypes as $type) {
            // Determine which file to use (original, signed, or display override)
            $filePath = null;
            if (isset($dokumen['display'][$type])) {
                $filePath = $dokumen['display'][$type];
            } elseif (isset($dokumen['signed'][$type])) {
                $filePath = $dokumen['signed'][$type];
            } elseif (isset($dokumen[$type])) {
                $filePath = $dokumen[$type];
            }
            
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                $fullPath = storage_path('app/public/' . ltrim($filePath, '/'));
                $pageCount = $this->getPdfPageCount($fullPath);
                $documentsPageInfo[$type] = [
                    'path' => $filePath,
                    'pageCount' => $pageCount,
                    'url' => Storage::url($filePath)
                ];
                
                Log::info("ValidasiController - Document page count", [
                    'document_type' => $type,
                    'file_path' => $filePath,
                    'page_count' => $pageCount
                ]);
            }
        }
        
        // Also handle file karya
        if ($pengajuan->file_karya) {
            $fileKaryaExt = pathinfo($pengajuan->file_karya, PATHINFO_EXTENSION);
            if (strtolower($fileKaryaExt) === 'pdf') {
                // Check if it's a URL or file path
                if (filter_var($pengajuan->file_karya, FILTER_VALIDATE_URL)) {
                    $documentsPageInfo['karya'] = [
                        'path' => $pengajuan->file_karya,
                        'pageCount' => 1, // Can't count pages for external URLs
                        'url' => $pengajuan->file_karya,
                        'is_url' => true
                    ];
                } else {
                    $karyaPath = ltrim($pengajuan->file_karya, '/');
                    if (Storage::disk('public')->exists($karyaPath)) {
                        $fullPath = storage_path('app/public/' . ltrim($karyaPath, '/'));
                        $pageCount = $this->getPdfPageCount($fullPath);
                        $documentsPageInfo['karya'] = [
                            'path' => $karyaPath,
                            'pageCount' => $pageCount,
                            'url' => Storage::url($karyaPath)
                        ];
                    }
                }
            }
        }
        
        return view('validasi.show', compact('pengajuan', 'signatures', 'stamps', 'dokumen', 'documentsPageInfo'));
    }

    /**
     * Menampilkan halaman editor untuk tanda tangan dan materai.
     *
     * @param  \App\Models\PengajuanHki  $pengajuan
     * @param  string  $documentType
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showSignatureEditor(PengajuanHki $pengajuan, string $documentType)
    {
        if (!in_array($documentType, ['surat_pengalihan', 'surat_pernyataan'])) {
            abort(404, 'Jenis dokumen tidak valid.');
        }

        // Get documents from file_dokumen_pendukung field (consistent with PersetujuanController)  
        $dokumenJson = [];
        if (is_string($pengajuan->file_dokumen_pendukung)) {
            $dokumenJson = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
        } else {
            $dokumenJson = $pengajuan->file_dokumen_pendukung ?? [];
        }

        // Apply enhanced file selection logic
        $dokumenJson = $this->applyEnhancedFileSelection($dokumenJson);
        
        // Get the best file path (original or signed)
        $documentPath = null;
        if (isset($dokumenJson['display'][$documentType])) {
            $documentPath = $dokumenJson['display'][$documentType];
        } elseif (isset($dokumenJson['signed'][$documentType])) {
            $documentPath = $dokumenJson['signed'][$documentType];
        } else {
            $documentPath = $dokumenJson[$documentType] ?? null;
        }

        if (!$documentPath || !Storage::disk('public')->exists($documentPath)) {
            Log::warning("Dokumen tidak ditemukan untuk validasi {$pengajuan->id} dengan tipe {$documentType}. Path: {$documentPath}");
            return redirect()->back()->with('error', 'File dokumen tidak ditemukan atau path tidak valid.');
        }

        // Langsung gunakan URL dari file PDF yang sudah ada.
        $documentUrl = Storage::url($documentPath);

        $signatures = $this->getAvailableSignatures($pengajuan);
        $stamps = $this->getAvailableStamps();
        $overlays = $dokumenJson['overlays'][$documentType] ?? [];
        
        return view('validasi.signature-editor', compact(
            'pengajuan', 
            'documentType', 
            'documentPath', 
            'documentUrl', 
            'signatures', 
            'stamps', 
            'overlays'
        ));
    }

    /**
     * Menerapkan dan menyimpan overlay (tanda tangan/materai) ke dokumen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PengajuanHki  $pengajuan
     * @param  string  $documentType
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyOverlay(Request $request, PengajuanHki $pengajuan, string $documentType)
    {
        $request->validate([
            'overlays' => 'required|string',
        ]);

        try {
            // Parse and validate overlay data
            $overlays = json_decode($request->overlays, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($overlays)) {
                return response()->json(['message' => 'Data overlay tidak valid'], 422);
            }

            // Normalize overlay data for consistency
            $normalizedOverlays = [];
            foreach ($overlays as $overlay) {
                $normalizedOverlay = [
                    'type' => $overlay['type'] ?? 'signature',
                    'signature_id' => $overlay['signature_id'] ?? null,
                    'stamp_id' => $overlay['stamp_id'] ?? null,
                    'x' => round(floatval($overlay['x'] ?? 0), 3),
                    'y' => round(floatval($overlay['y'] ?? 0), 3),
                    'width' => round(floatval($overlay['width'] ?? 20), 3),
                    'height' => round(floatval($overlay['height'] ?? 10), 3),
                ];
                
                // Validate coordinate ranges
                if ($normalizedOverlay['x'] < 0 || $normalizedOverlay['x'] > 100 ||
                    $normalizedOverlay['y'] < 0 || $normalizedOverlay['y'] > 100 ||
                    $normalizedOverlay['width'] <= 0 || $normalizedOverlay['width'] > 100 ||
                    $normalizedOverlay['height'] <= 0 || $normalizedOverlay['height'] > 100) {
                    Log::warning("Invalid overlay coordinates detected in ValidasiController", [
                        'pengajuan_id' => $pengajuan->id,
                        'overlay' => $normalizedOverlay
                    ]);
                    continue; // Skip invalid overlays
                }
                
                $normalizedOverlays[] = $normalizedOverlay;
            }

            if (empty($normalizedOverlays)) {
                return response()->json(['message' => 'Tidak ada overlay yang valid untuk diproses'], 422);
            }

            // Get documents from file_dokumen_pendukung field (consistent with PersetujuanController)
            $dokumenJson = [];
            if (is_string($pengajuan->file_dokumen_pendukung)) {
                $dokumenJson = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
            } else {
                $dokumenJson = $pengajuan->file_dokumen_pendukung ?? [];
            }

            // Ensure overlays structure exists
            if (!isset($dokumenJson['overlays'])) {
                $dokumenJson['overlays'] = [];
            }

            // Save normalized overlay data
            $dokumenJson['overlays'][$documentType] = $normalizedOverlays;

            // Save to file_dokumen_pendukung field (consistent with PersetujuanController)
                $pengajuan->file_dokumen_pendukung = json_encode($dokumenJson);
            
            $pengajuan->save();

            Log::info("Overlay application completed in ValidasiController", [
                'pengajuan_id' => $pengajuan->id,
                'document_type' => $documentType,
                'overlays_count' => count($normalizedOverlays),
                'overlays_data' => $normalizedOverlays
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tanda tangan dan materai berhasil disimpan.',
                'overlays_applied' => count($normalizedOverlays),
                'overlays_data' => $normalizedOverlays
            ]);

        } catch (\Exception $e) {
            Log::error("Error applying overlay in ValidasiController", [
                'pengajuan_id' => $pengajuan->id,
                'document_type' => $documentType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan overlay: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validasi(Request $request, PengajuanHki $pengajuan): RedirectResponse
    {
        $request->validate([
            'status_validasi' => 'required|in:disetujui,ditolak',
            'catatan_validasi' => 'required_if:status_validasi,ditolak|nullable|string'
        ]);

        $status = $request->status_validasi === 'disetujui' ? 'divalidasi_sedang_diproses' : 'ditolak';
        $catatan = $request->catatan_validasi;

        // Jika disetujui, panggil PDF signer
        if ($status === 'divalidasi_sedang_diproses') {
            $pdfSigner = new PdfSigningController();
            
                    // Get documents from file_dokumen_pendukung field (consistent with PersetujuanController)
            $dokumenJson = [];
        if (is_string($pengajuan->file_dokumen_pendukung)) {
                $dokumenJson = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
            } else {
                $dokumenJson = $pengajuan->file_dokumen_pendukung ?? [];
            }

            if (!empty($dokumenJson['overlays']['surat_pernyataan'])) {
                $pdfSigner->signPdf($pengajuan, 'surat_pernyataan');
            }
            if (!empty($dokumenJson['overlays']['surat_pengalihan'])) {
                $pdfSigner->signPdf($pengajuan, 'surat_pengalihan');
            }
            // Refresh model setelah disave oleh signer
            $pengajuan->refresh();
        }

        $pengajuan->update([
            'status' => $status,
            'catatan_validasi' => $catatan
        ]);

        // Buat notifikasi untuk pengaju
        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Update Status Validasi HKI',
            'pesan' => 'Pengajuan HKI Anda dengan judul "' . ($pengajuan->judul_karya ?? $pengajuan->judul) . '" telah ' .
                      ($status === 'divalidasi_sedang_diproses' ? 'divalidasi & sedang diproses' : 'ditolak') . '. ' .
                      'Catatan: ' . $catatan,
            'status' => 'unread',
            'dibaca' => false
        ]);

        return Redirect::to(route('validasi.index'))
            ->with('success', 'Status pengajuan berhasil diperbarui');
    }

    /**
     * Mengambil daftar tanda tangan yang tersedia untuk user yang sedang login.
     */
    private function getAvailableSignatures(PengajuanHki $pengajuan): array
    {
        $currentUser = auth()->user();
        $signatures = [];

        // Gunakan 'ttd_path' sesuai dengan model User
        if ($currentUser && $currentUser->ttd_path) {
            $signatures[] = [
                'id' => $currentUser->id,
                'name' => 'Tanda Tangan (' . $currentUser->name . ')',
                'url' => Storage::url($currentUser->ttd_path),
            ];
        }

        return $signatures;
    }

    /**
     * Mengambil daftar materai yang tersedia.
     */
    private function getAvailableStamps(): array
    {
        // Karena tidak ada kolom materai di User, gunakan materai default.
        $stamps = [];
        $defaultMateraiPath = public_path('images/materai-default.png');

        if (file_exists($defaultMateraiPath)) {
             $stamps[] = [
                'id' => 'materai-default',
                'name' => 'Materai',
                'url' => asset('images/materai-default.png'),
            ];
        }

        return $stamps;
    }

    /**
     * Finalisasi validasi oleh admin – mengubah status menjadi menunggu_pembayaran
     */
    public function finalize(PengajuanHki $pengajuan): RedirectResponse
    {
        if (!in_array($pengajuan->status, ['divalidasi_sedang_diproses'])) {
            return Redirect::back()->with('error', 'Pengajuan belum divalidasi direktur atau sudah diproses.');
        }

        $pengajuan->update([
            'status' => 'menunggu_pembayaran',
            'tanggal_validasi' => now(),
            'catatan_admin' => 'Finalisasi oleh admin, menunggu pembayaran.'
        ]);

        // Notifikasi ke pengusul
        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Menunggu Pembayaran',
            'pesan' => 'Pengajuan HKI Anda telah selesai divalidasi dan menunggu pembayaran.',
            'status' => 'unread',
            'dibaca' => false
        ]);

        return Redirect::to(route('validasi.index'))->with('success', 'Pengajuan telah difinalisasi dan menunggu pembayaran.');
    }

    /**
     * Apply enhanced file selection logic to choose the best file for each document type
     */
    private function applyEnhancedFileSelection(array $dokumen): array
    {
        $documentTypes = ['surat_pengalihan', 'surat_pernyataan'];
        
        foreach ($documentTypes as $documentType) {
            $originalPath = $dokumen[$documentType] ?? null;
            $signedPath = $dokumen['signed'][$documentType] ?? null;
            
            if ($signedPath && $originalPath) {
                $signedPath = ltrim($signedPath, '/');
                $originalPath = ltrim($originalPath, '/');
                
                if (Storage::disk('public')->exists($signedPath) && Storage::disk('public')->exists($originalPath)) {
                    try {
                        $originalFullPath = storage_path('app/public/' . $originalPath);
                        $signedFullPath = storage_path('app/public/' . $signedPath);
                        
                        $originalPages = $this->getPdfPageCount($originalFullPath);
                        $signedPages = $this->getPdfPageCount($signedFullPath);
                        
                        Log::info("Validasi file selection", [
                            'document_type' => $documentType,
                            'original_path' => $originalPath,
                            'signed_path' => $signedPath,
                            'original_pages' => $originalPages,
                            'signed_pages' => $signedPages
                        ]);
                        
                        // Use signed file if it has same or more pages than original
                        if ($signedPages >= $originalPages && $signedPages > 0) {
                            // Keep signed file as is
                            Log::info("Using signed file for validasi", [
                                'document_type' => $documentType,
                                'reason' => 'signed_file_valid',
                                'pages' => $signedPages
                            ]);
                        } else {
                            // Use original file instead
                            $dokumen['display'][$documentType] = $originalPath;
                            Log::warning("Using original file for validasi", [
                                'document_type' => $documentType,
                                'reason' => 'signed_file_invalid',
                                'original_pages' => $originalPages,
                                'signed_pages' => $signedPages
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error("Error in file selection for validasi", [
                            'document_type' => $documentType,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }
        
        return $dokumen;
    }

    /**
     * Enhanced PDF page counting with multiple methods and validation
     * Uses the same advanced methods as PersetujuanController
     */
    private function getPdfPageCount($pdfPath): int
    {
        try {
            if (!file_exists($pdfPath)) {
                return 0;
            }
            
            // First try reading the content and use regex methods
            $content = file_get_contents($pdfPath);
            if ($content === false) {
                return 1; // Default if can't read
            }
            
            // Check if it's a valid PDF
            if (substr($content, 0, 4) !== '%PDF') {
                return 1; // Not a valid PDF, default to 1
            }
            
            // Method 1: Count /Type /Page occurrences
            preg_match_all('/\/Type\s*\/Page[^s]/i', $content, $matches);
            $pageCount1 = count($matches[0]);
            
            // Method 2: Look for /Count entries in page tree
            preg_match_all('/\/Count\s+(\d+)/i', $content, $countMatches);
            $pageCount2 = 0;
            if (!empty($countMatches[1])) {
                $pageCount2 = (int)max($countMatches[1]);
            }
            
            // Method 3: Count page objects (most comprehensive and reliable)
            preg_match_all('/obj\s*<<[^>]*\/Type\s*\/Page[^>]*>>/i', $content, $pageObjs);
            $pageCount3 = count($pageObjs[0]);
            
            // Method 4: Alternative pattern
            preg_match_all('/\/Type\s*\/Page(?!\w)/', $content, $altMatches);
            $pageCount4 = count($altMatches[0]);
            
            // Enhanced decision logic for best page count accuracy
            $methods = [
                'type_page' => $pageCount1,
                'count_field' => $pageCount2, 
                'page_objects' => $pageCount3,
                'alternative' => $pageCount4
            ];
            
            // Filter out zero results
            $validMethods = array_filter($methods);
            
            if (!empty($validMethods)) {
                // Prioritize page_objects method if it's reasonable
                if ($pageCount3 > 0 && $pageCount3 <= 100) {
                    $finalCount = $pageCount3;
                    Log::info("Using page_objects method for validasi", [
                        'file' => basename($pdfPath),
                        'page_objects_result' => $pageCount3,
                        'all_methods' => $methods
                    ]);
                } else {
                    // Use the maximum of other reliable methods
                    $otherMethods = [$pageCount1, $pageCount2, $pageCount4];
                    $otherMethods = array_filter($otherMethods);
                    $finalCount = empty($otherMethods) ? 1 : max($otherMethods);
                    Log::info("Using fallback methods for validasi", [
                        'file' => basename($pdfPath),
                        'fallback_result' => $finalCount,
                        'all_methods' => $methods
                    ]);
                }
            } else {
                $finalCount = 1;
                Log::warning("No valid detection methods for validasi", [
                    'file' => basename($pdfPath),
                    'all_methods' => $methods
                ]);
            }
            
            Log::info("PDF page count analysis for validasi", [
                'file' => basename($pdfPath),
                'method1_type_page' => $pageCount1,
                'method2_count' => $pageCount2,
                'method3_objects' => $pageCount3,
                'method4_alternative' => $pageCount4,
                'final_result' => $finalCount,
                'used_method3' => ($pageCount3 > 0 && $pageCount3 <= 100)
            ]);
            
            // If we have a reasonable result, use it
            if ($finalCount > 0 && $finalCount <= 100) { // Sanity check
                return $finalCount;
            }
            
            // Final fallback: try FPDI if regex failed
            try {
                $pdf = new \setasign\Fpdi\Fpdi();
                $fpdiPageCount = $pdf->setSourceFile($pdfPath);
                Log::info("FPDI fallback successful for validasi", [
                    'file' => basename($pdfPath),
                    'fpdi_count' => $fpdiPageCount
                ]);
                return $fpdiPageCount;
            } catch (\Exception $fpdiError) {
                Log::warning("FPDI fallback failed for validasi", [
                    'file' => basename($pdfPath),
                    'error' => $fpdiError->getMessage()
                ]);
                return max(1, $finalCount); // Use regex result or default to 1
            }
            
        } catch (\Exception $e) {
            Log::error("Error counting PDF pages for validasi", [
                'file' => $pdfPath,
                'error' => $e->getMessage()
            ]);
            return 1; // Default fallback
        }
    }
}