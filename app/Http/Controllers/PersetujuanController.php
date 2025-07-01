<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use App\Models\PersetujuanDirektur;
use App\Models\Notifikasi;
use App\Http\Controllers\PdfSigningController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader\PdfReader;
use Illuminate\Support\Str;

class PersetujuanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:direktur,admin');
    }

    public function index(Request $request): View
    {
        $query = PengajuanHki::with('user')
            ->whereNotIn('status', ['draft']); // Exclude draft from director view
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('judul_karya', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_pengajuan', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($request->filled('status')) {
            $status = $request->get('status');
            $query->where('status', $status);
        }
        
        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }
        
        // Sort functionality
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'title':
                $query->orderBy('judul_karya', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
        
        $pengajuans = $query->paginate(12);
        
        // Enhanced statistics
        $stats = [
            'total' => PengajuanHki::whereNotIn('status', ['draft'])->count(),
            'pending' => PengajuanHki::where('status', 'menunggu_validasi')->count(),
            'approved' => PengajuanHki::where('status', 'divalidasi_sedang_diproses')->count(),
            'rejected' => PengajuanHki::where('status', 'ditolak')->count(),
            'today' => PengajuanHki::whereDate('created_at', today())->whereNotIn('status', ['draft'])->count(),
            'this_week' => PengajuanHki::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->whereNotIn('status', ['draft'])->count(),
            'this_month' => PengajuanHki::whereMonth('created_at', now()->month)->whereNotIn('status', ['draft'])->count(),
        ];
        
        return view('persetujuan.index', compact('pengajuans', 'stats'));
    }

    public function show(int $id): View
    {
        // Tidak menggunakan cache untuk data yang sering berubah
            $pengajuan = PengajuanHki::with(['user', 'pengaju'])->findOrFail($id);
            
            $dokumen = [];
            if ($pengajuan->file_dokumen_pendukung) {
                $dokumen = is_string($pengajuan->file_dokumen_pendukung) 
                    ? json_decode($pengajuan->file_dokumen_pendukung, true) 
                    : $pengajuan->file_dokumen_pendukung;
            }
            
            $pencipta = $pengajuan->pengaju;
            
            // Gunakan file bertanda tangan untuk semua status yang sudah melewati tahap validasi
            $preferSigned = $pengajuan->status !== 'menunggu_validasi';
            
            $documents = [
                'contoh_ciptaan' => [
                    'label' => 'Contoh Ciptaan',
                    'description' => 'File contoh karya yang diajukan untuk hak cipta',
                    'icon' => 'fas fa-palette',
                    'color' => 'primary',
                    'file_info' => $this->getFileInfoFromFileKarya($pengajuan->file_karya)
                ],
                'surat_pengalihan' => [
                    'label' => 'Surat Pengalihan Hak',
                    'description' => 'Dokumen pengalihan hak cipta (jika ada)',
                    'icon' => 'fas fa-exchange-alt',
                    'color' => 'info',
                    'file_info' => $this->getFileInfoFromDokumen($dokumen, 'surat_pengalihan', $preferSigned)
                ],
                'surat_pernyataan' => [
                    'label' => 'Surat Pernyataan',
                    'description' => 'Surat pernyataan keaslian karya',
                    'icon' => 'fas fa-file-signature',
                    'color' => 'warning',
                    'file_info' => $this->getFileInfoFromDokumen($dokumen, 'surat_pernyataan', $preferSigned)
                ],
                'ktp' => [
                    'label' => 'KTP Pencipta',
                    'description' => 'Kartu Tanda Penduduk pencipta',
                    'icon' => 'fas fa-id-card',
                    'color' => 'success',
                    'file_info' => $this->getFileInfoFromDokumen($dokumen, 'ktp', $preferSigned)
                ]
            ];

        $data = [
                'pengajuan' => $pengajuan,
                'dokumen' => $dokumen,
                'documents' => $documents,
                'pencipta' => $pencipta,
            ];
        
        return view('persetujuan.show', $data);
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

        $dokumenJson = is_string($pengajuan->file_dokumen_pendukung) 
            ? json_decode($pengajuan->file_dokumen_pendukung, true) 
            : ($pengajuan->file_dokumen_pendukung ?? []);

        // Gunakan dokumen asli untuk editing; jika tidak ada, fallback ke versi signed
        $documentPath = $dokumenJson[$documentType] ?? ($dokumenJson['signed'][$documentType] ?? null);

        // Normalisasi path agar relative (hilangkan leading slash)
        if ($documentPath) {
            $documentPath = ltrim($documentPath, '/');
        }

        if (!$documentPath || !Storage::disk('public')->exists($documentPath)) {
            // Log untuk debug
            Log::warning("Dokumen tidak ditemukan untuk pengajuan {$pengajuan->id} dengan tipe {$documentType}. Path: {$documentPath}");
            return redirect()->back()->with('error', 'File dokumen tidak ditemukan atau path tidak valid.');
        }

        // Langsung gunakan URL dari file PDF yang sudah ada.
        $documentUrl = Storage::url($documentPath);

        $signatures = $this->getAvailableSignatures($pengajuan);
        $stamps = $this->getAvailableStamps();
        $overlays = $dokumenJson['overlays'][$documentType] ?? [];
        
        return view('persetujuan.signature-editor', compact(
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

        // Validasi konten JSON
        $overlays = json_decode($request->overlays, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($overlays)) {
            return response()->json(['message' => 'Data overlay tidak valid'], 422);
        }

        // Normalize overlay data for consistency
        $normalizedOverlays = [];
        $uniqueMap = [];
        foreach ($overlays as $overlay) {
            $normalizedOverlay = [
                'type' => $overlay['type'] ?? 'signature',
                'url' => $overlay['url'] ?? '',
                'page' => intval($overlay['page'] ?? 1),
                'x_percent' => round(floatval($overlay['x_percent'] ?? 0), 3),
                'y_percent' => round(floatval($overlay['y_percent'] ?? 0), 3),
                'width_percent' => round(floatval($overlay['width_percent'] ?? 20), 3),
                'height_percent' => round(floatval($overlay['height_percent'] ?? 10), 3),
                'x_pt' => round(floatval($overlay['x_pt'] ?? -1),1),
                'y_pt' => round(floatval($overlay['y_pt'] ?? -1),1),
                'width_pt' => round(floatval($overlay['width_pt'] ?? -1),1),
                'height_pt' => round(floatval($overlay['height_pt'] ?? -1),1),
            ];
            
            // Validate coordinate ranges
            if ($normalizedOverlay['x_percent'] < 0 || $normalizedOverlay['x_percent'] > 100 ||
                $normalizedOverlay['y_percent'] < 0 || $normalizedOverlay['y_percent'] > 100 ||
                $normalizedOverlay['width_percent'] <= 0 || $normalizedOverlay['width_percent'] > 100 ||
                $normalizedOverlay['height_percent'] <= 0 || $normalizedOverlay['height_percent'] > 100) {
                Log::warning("Invalid overlay coordinates detected", [
                    'pengajuan_id' => $pengajuan->id,
                    'overlay' => $normalizedOverlay
                ]);
                continue; // Skip invalid overlays
            }
            
            // Generate unique key based on type & page only to remove duplicate signatures per page
            $key = $normalizedOverlay['type'].'_'.$normalizedOverlay['page'];

            // If an overlay with same key already exists, keep the one that is positioned
            // further DOWN the page (lebih besar y_percent). Berdasarkan feedback, overlay
            // duplikat dengan koordinat lebih kecil (lebih ke atas) harus dibuang.
            if (!isset($uniqueMap[$key]) || $normalizedOverlay['y_percent'] > ($uniqueMap[$key]['y_percent'] ?? -1)) {
                $uniqueMap[$key] = $normalizedOverlay;
            }
        }

        $normalizedOverlays = array_values($uniqueMap);

        if (empty($normalizedOverlays)) {
            return response()->json(['message' => 'Tidak ada overlay yang valid untuk diproses'], 422);
        }

        try {
            $dokumenJson = is_string($pengajuan->file_dokumen_pendukung)
                ? json_decode($pengajuan->file_dokumen_pendukung, true)
                : ($pengajuan->file_dokumen_pendukung ?? []);

            // Pastikan struktur overlays
            if (!isset($dokumenJson['overlays']) || !is_array($dokumenJson['overlays'])) {
                $dokumenJson['overlays'] = [];
            }

            $dokumenJson['overlays'][$documentType] = $normalizedOverlays;

            // Persist updated overlays immediately so PdfSigner can read the latest data
            $pengajuan->file_dokumen_pendukung = json_encode($dokumenJson);
            $pengajuan->save();

            // Generate signed PDF immediately so preview/detail show latest version
            $pdfSigner = new PdfSigningController();
            $signedPath = $pdfSigner->signPdf($pengajuan, $documentType);

            // Clear caches so fresh data appears
            cache()->forget("persetujuan_show_{$pengajuan->id}");
            cache()->forget("validation_wizard_{$pengajuan->id}");

            return response()->json([
                'message' => 'Overlay disimpan & dokumen ditandatangani',
                'overlays_applied' => count($normalizedOverlays),
                'signed_path' => $signedPath,
            ]);

        } catch (\Exception $e) {
            Log::error("Error applying overlay for pengajuan {$pengajuan->id}", [
                'error' => $e->getMessage(),
                'document_type' => $documentType,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil daftar tanda tangan yang tersedia untuk Direktur yang sedang login.
     */
    private function getAvailableSignatures(PengajuanHki $pengajuan): array
    {
        $direktur = Auth::user();
        $signatures = [];

        if ($direktur && $direktur->ttd_path) {
            $signatures[] = [
                'id'   => 'director_current',
                'name' => 'Tanda Tangan Direktur (Aktif)',
                'path' => $direktur->ttd_path,
                'url'  => Storage::url($direktur->ttd_path),
            ];
        }

        // Ambil seluruh file tanda tangan yang ada di folder signatures
        $files = Storage::disk('public')->files('signatures');
        foreach ($files as $file) {
            // Jangan duplikasikan yang sudah menjadi ttd_path aktif
            if ($direktur && $direktur->ttd_path === $file) {
                continue;
            }
            $signatures[] = [
                'id'   => basename($file, '.png'),
                'name' => 'Tanda Tangan - ' . substr(basename($file), 0, 8),
                'path' => $file,
                'url'  => Storage::url($file),
            ];
        }

        return $signatures;
    }

    /**
     * Mengambil daftar materai yang tersedia.
     */
    private function getAvailableStamps(PengajuanHki $pengajuan = null): array
    {
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

    private function getFileInfo($filePath)
    {
        if (!$filePath) {
            return null;
        }
        
        // Hilangkan leading slash agar relative ke disk public
        $filePath = ltrim($filePath, '/');

        $size = 0;
        if (Storage::disk('public')->exists($filePath)) {
        $size = Storage::disk('public')->size($filePath);
        } else {
            $fullPublicPath = public_path($filePath);
            if (file_exists($fullPublicPath)) {
                $size = filesize($fullPublicPath);
            }
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        
        // Selalu kembalikan info dasar meski size=0 agar kartu tetap tampil
        return [
            'size' => $size,
            'size_formatted' => $size ? $this->formatFileSize($size) : 'Unknown',
            'extension' => strtoupper($extension),
            'filename' => basename($filePath),
            'icon_class' => $this->getFileTypeIcon($extension)
        ];
    }
    
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    private function getFileTypeIcon($extension)
    {
        switch (strtolower($extension)) {
            case 'pdf':
                return 'fas fa-file-pdf text-danger';
            case 'doc':
            case 'docx':
                return 'fas fa-file-word text-primary';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'fas fa-file-image text-success';
            case 'zip':
            case 'rar':
                return 'fas fa-file-archive text-warning';
            default:
                return 'fas fa-file text-secondary';
        }
    }

    private function getFileInfoFromDokumen($dokumen, $key, bool $preferSigned = false)
    {
        // 1. Jika preferSigned aktif, coba file signed dahulu
        if ($preferSigned && !empty($dokumen['signed'][$key])) {
            $info = $this->getFileInfo($dokumen['signed'][$key]);
            if ($info) return $info;
        }

        // 2. Selalu coba dokumen asli
        if (!empty($dokumen[$key])) {
            $infoOriginal = $this->getFileInfo($dokumen[$key]);
            if ($infoOriginal) return $infoOriginal;
        }
        
        // 3. Terakhir, jika preferSigned=false tapi dokumen asli gagal, coba signed (jika ada)
        if (!$preferSigned && !empty($dokumen['signed'][$key])) {
            return $this->getFileInfo($dokumen['signed'][$key]);
        }

        return null;
    }

    private function getFileInfoFromFileKarya($fileKarya)
    {
        if (!$fileKarya) {
            return null;
        }
        
        // Check if it's a URL (external link)
        if (filter_var($fileKarya, FILTER_VALIDATE_URL)) {
            // For URLs, we can't get file size, so provide basic info
            $extension = pathinfo(parse_url($fileKarya, PHP_URL_PATH), PATHINFO_EXTENSION);
            return [
                'size' => 0,
                'size_formatted' => 'Link Eksternal',
                'extension' => strtoupper($extension ?: 'LINK'),
                'filename' => basename(parse_url($fileKarya, PHP_URL_PATH)) ?: 'Link Eksternal',
                'icon_class' => $extension ? $this->getFileTypeIcon($extension) : 'fas fa-link text-info',
                'is_url' => true
            ];
        }
        
        // For local files
        return $this->getFileInfo($fileKarya);
    }

    public function approve(Request $request, $id)
    {
        // Validasi hanya untuk catatan, karena ttd/materai sudah di-handle oleh editor
        $request->validate([
            'catatan_admin' => 'nullable|string|max:1000'
        ]);

        try {
            $pengajuan = PengajuanHki::findOrFail($id);
            
            if ($pengajuan->status !== 'menunggu_validasi') {
                return redirect()->back()->with('error', 'Pengajuan tidak dapat divalidasi karena statusnya bukan "menunggu validasi".');
            }

            // Pastikan kedua dokumen sudah ditempel tanda tangan
            $dokumenJson = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
            $hasPengalihan  = !empty($dokumenJson['overlays']['surat_pengalihan']);
            $hasPernyataan  = !empty($dokumenJson['overlays']['surat_pernyataan']);

            // Periksa juga apakah sudah tersedia file signed hasil overlay
            $signedPengalihan  = isset($dokumenJson['signed']['surat_pengalihan']) &&
                                  Storage::disk('public')->exists($dokumenJson['signed']['surat_pengalihan'] ?? '');
            $signedPernyataan  = isset($dokumenJson['signed']['surat_pernyataan']) &&
                                  Storage::disk('public')->exists($dokumenJson['signed']['surat_pernyataan'] ?? '');

            if ((!$hasPengalihan && !$signedPengalihan) || (!$hasPernyataan && !$signedPernyataan)) {
                return redirect()->back()->with('error', 'Kedua dokumen (Surat Pengalihan & Surat Pernyataan) harus sudah ditandatangani sebelum melakukan validasi.');
            }

            // Panggil PDF Signer untuk "mencetak" overlay ke PDF
            $pdfSigner = new PdfSigningController();

            // Lakukan signing untuk surat pernyataan jika ada overlay
            if (!empty($dokumenJson['overlays']['surat_pernyataan'])) {
                $pdfSigner->signPdf($pengajuan, 'surat_pernyataan');
            }
            // Lakukan signing untuk surat pengalihan jika ada overlay
            if (!empty($dokumenJson['overlays']['surat_pengalihan'])) {
                $pdfSigner->signPdf($pengajuan, 'surat_pengalihan');
            }
            
            // Update status pengajuan. Perlu di-refresh karena signPdf() juga melakukan save.
            $pengajuan->refresh(); 
            $pengajuan->status = 'divalidasi_sedang_diproses';
            $pengajuan->catatan_admin = $request->catatan_admin ?? 'Disetujui dan sedang diproses oleh Direktur.';
            $pengajuan->tanggal_validasi = now();
            $pengajuan->save();

            // Clear cached show data so signed document path & status refresh
            cache()->forget("persetujuan_show_{$pengajuan->id}");
            cache()->forget("validation_wizard_{$pengajuan->id}");

            // Buat notifikasi untuk user
        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Pengajuan HKI Divalidasi',
                                    'pesan' => "Pengajuan HKI Anda dengan judul '{$pengajuan->judul_karya}' telah divalidasi dan sedang diproses oleh direktur.",
            'status' => 'unread',
            'dibaca' => false
        ]);

            // Notifikasi ke semua admin agar menindaklanjuti (finalisasi & billing)
            $adminUsers = \App\Models\User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                Notifikasi::create([
                    'user_id' => $admin->id,
                    'pengajuan_hki_id' => $pengajuan->id,
                    'judul' => 'Pengajuan HKI Divalidasi',
                    'pesan' => 'Pengajuan HKI dengan judul "' . $pengajuan->judul_karya . '" telah divalidasi & sedang diproses, menunggu penyiapan billing / finalisasi.',
                    'status' => 'unread',
                    'dibaca' => false
                ]);
            }

            return redirect()->route('persetujuan.index')->with('success', 'Pengajuan berhasil divalidasi dan dokumen telah ditandatangani!');
            
        } catch (\Exception $e) {
            Log::error("Gagal menyetujui pengajuan #{$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat proses persetujuan.');
        }
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $pengajuan = PengajuanHki::findOrFail($id);
        $validated = $request->validate([
            'catatan_admin' => 'required|string|max:1000'
        ]);

        $pengajuan->update([
            'status' => 'ditolak',
            'catatan_admin' => $validated['catatan_admin'],
        ]);

        // Buat notifikasi untuk pengaju
        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Pengajuan HKI Ditolak',
            'pesan' => 'Pengajuan HKI Anda dengan judul "' . $pengajuan->judul_karya . '" telah ditolak. ' .
                      'Alasan: ' . $validated['catatan_admin'],
            'status' => 'unread',
            'dibaca' => false
        ]);

        return Redirect::to(route('persetujuan.index'))
            ->with('success', 'Pengajuan berhasil ditolak.');
    }

    public function store(Request $request, int $id): RedirectResponse
    {
        $pengajuan = PengajuanHki::findOrFail($id);
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_persetujuan' => 'required|string'
        ]);

        $pengajuan->update([
            'status' => $request->status,
            'catatan_persetujuan' => $request->catatan_persetujuan
        ]);

        // Buat notifikasi untuk pengaju
        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Update Status Pengajuan HKI',
            'pesan' => 'Pengajuan HKI Anda dengan judul "' . $pengajuan->judul . '" telah ' . 
                      ($request->status === 'disetujui' ? 'disetujui' : 'ditolak') . '. ' .
                      'Catatan: ' . $request->catatan_persetujuan,
            'status' => 'unread',
            'dibaca' => false
        ]);

        return Redirect::to(route('persetujuan.index'))
            ->with('success', 'Pengajuan berhasil diproses');
    }

    public function updateFile(Request $request, int $id)
    {
        try {
            $pengajuan = PengajuanHki::findOrFail($id);
            
            $request->validate([
                'file' => 'required|file|max:10240', // 10MB max
                'file_type' => 'required|string|in:file_path,file_karya,surat_pengalihan,surat_pernyataan,ktp'
            ]);

            $fileType = $request->input('file_type');
            $file = $request->file('file');
            
            // Validate file type based on file_type
            if (in_array($fileType, ['surat_pengalihan', 'surat_pernyataan', 'ktp'])) {
                $request->validate([
                    'file' => 'mimes:pdf'
                ]);
            } elseif ($fileType === 'file_karya') {
                $request->validate([
                    'file' => 'mimes:pdf,doc,docx,jpg,jpeg,png,mp4,mp3'
                ]);
            } elseif ($fileType === 'file_path') {
                $request->validate([
                    'file' => 'mimes:pdf,doc,docx'
                ]);
            }

            // Store new file
            $newPath = $file->store('pengajuan', 'public');
            
            if ($fileType === 'file_path' || $fileType === 'file_karya') {
                // Delete old file if exists
                if ($pengajuan->$fileType) {
                    Storage::disk('public')->delete($pengajuan->$fileType);
                }
                
                // Update pengajuan
                $pengajuan->update([
                    $fileType => $newPath
                ]);
            } else {
                // Handle dokumen pendukung
                $dokumen = $pengajuan->file_dokumen_pendukung;
                if (is_string($dokumen)) {
                    $dokumen = json_decode($dokumen, true);
                }
                if (!is_array($dokumen)) {
                    $dokumen = [];
                }
                
                // Delete old file if exists
                if (isset($dokumen[$fileType])) {
                    Storage::disk('public')->delete($dokumen[$fileType]);
                }
                
                // Update dokumen array
                $dokumen[$fileType] = $newPath;
                
                // Update pengajuan
                $pengajuan->update([
                    'file_dokumen_pendukung' => json_encode($dokumen)
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'file_path' => $newPath
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkApprove(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pengajuan_ids' => 'required|string',
            'catatan_admin' => 'nullable|string|max:500'
        ]);

        $pengajuanIds = explode(',', $validated['pengajuan_ids']);
        $comment = $validated['catatan_admin'] ?? 'Disetujui secara bulk oleh direktur';
        
        $updatedCount = 0;
        
        foreach ($pengajuanIds as $id) {
            $pengajuan = PengajuanHki::find($id);
            if ($pengajuan && $pengajuan->status === 'menunggu_validasi') {
                $pengajuan->update([
                    'status' => 'divalidasi_sedang_diproses',
                    'catatan_admin' => $comment,
                ]);

                // Buat notifikasi untuk pengaju
                Notifikasi::create([
                    'user_id' => $pengajuan->user_id,
                    'pengajuan_hki_id' => $pengajuan->id,
                    'judul' => 'Pengajuan HKI Divalidasi',
                    'pesan' => 'Pengajuan HKI Anda dengan judul "' . $pengajuan->judul_karya . '" telah divalidasi & sedang diproses oleh direktur. Catatan: ' . $comment,
                    'status' => 'unread',
                    'dibaca' => false
                ]);
                
                // Notifikasi ke admin
                $admins = \App\Models\User::where('role','admin')->get();
                foreach($admins as $admin){
                    Notifikasi::create([
                        'user_id' => $admin->id,
                        'pengajuan_hki_id' => $pengajuan->id,
                        'judul' => 'Pengajuan HKI Divalidasi',
                        'pesan' => 'Pengajuan HKI dengan judul "' . $pengajuan->judul_karya . '" telah divalidasi & sedang diproses, menunggu tindakan admin.',
                        'status' => 'unread',
                        'dibaca' => false
                    ]);
                }
                
                $updatedCount++;
            }
        }

        return Redirect::to(route('persetujuan.index'))
            ->with('success', "Berhasil memvalidasi {$updatedCount} pengajuan secara bulk.");
    }

    public function bulkReject(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pengajuan_ids' => 'required|string',
            'catatan_admin' => 'required|string|max:1000'
        ]);

        $pengajuanIds = explode(',', $validated['pengajuan_ids']);
        $reason = $validated['catatan_admin'];
        
        $updatedCount = 0;
        
        foreach ($pengajuanIds as $id) {
            $pengajuan = PengajuanHki::find($id);
            if ($pengajuan && $pengajuan->status === 'menunggu_validasi') {
                $pengajuan->update([
                    'status' => 'ditolak',
                    'catatan_admin' => $reason,
                ]);

                // Buat notifikasi untuk pengaju
                Notifikasi::create([
                    'user_id' => $pengajuan->user_id,
                    'pengajuan_hki_id' => $pengajuan->id,
                    'judul' => 'Pengajuan HKI Ditolak',
                    'pesan' => 'Pengajuan HKI Anda dengan judul "' . $pengajuan->judul_karya . '" telah ditolak. Alasan: ' . $reason,
                    'status' => 'unread',
                    'dibaca' => false
                ]);
                
                $updatedCount++;
            }
        }

        return Redirect::to(route('persetujuan.index'))
            ->with('success', "Berhasil menolak {$updatedCount} pengajuan secara bulk.");
    }

    public function showValidationWizard($id)
    {
        $cacheKey = "validation_wizard_{$id}";
        $data = cache()->remember($cacheKey, 60, function () use ($id) {
            $pengajuan = PengajuanHki::with(['user', 'pengaju'])->findOrFail($id);
            
            $dokumen = is_string($pengajuan->file_dokumen_pendukung) 
                ? json_decode($pengajuan->file_dokumen_pendukung, true) 
                : ($pengajuan->file_dokumen_pendukung ?? []);

            $pencipta = $pengajuan->pengaju;

            $preferSigned = $pengajuan->status !== 'menunggu_validasi';

            $documents = [
                'contoh_ciptaan' => [
                    'label' => 'Contoh Ciptaan',
                    'description' => 'File contoh karya yang diajukan.',
                    'icon' => 'fas fa-palette',
                    'color' => 'primary',
                    'file_info' => $this->getFileInfoFromFileKarya($pengajuan->file_karya)
                ],
                'surat_pengalihan' => [
                    'label' => 'Surat Pengalihan Hak',
                    'description' => 'Dokumen pengalihan hak cipta (jika ada).',
                    'icon' => 'fas fa-exchange-alt',
                    'color' => 'info',
                    'file_info' => $this->getFileInfoFromDokumen($dokumen, 'surat_pengalihan', $preferSigned)
                ],
                'surat_pernyataan' => [
                    'label' => 'Surat Pernyataan',
                    'description' => 'Surat pernyataan keaslian karya.',
                    'icon' => 'fas fa-file-signature',
                    'color' => 'warning',
                    'file_info' => $this->getFileInfoFromDokumen($dokumen, 'surat_pernyataan', $preferSigned)
                ],
                'ktp' => [
                    'label' => 'KTP Pencipta',
                    'description' => 'Kartu Tanda Penduduk pencipta.',
                    'icon' => 'fas fa-id-card',
                    'color' => 'success',
                    'file_info' => $this->getFileInfoFromDokumen($dokumen, 'ktp', $preferSigned)
                ]
            ];
            
            return [
                'pengajuan' => $pengajuan,
                'dokumen' => $dokumen,
                'pencipta' => $pencipta,
                'documents' => $documents,
            ];
        });

        return view('persetujuan.validation-wizard', $data);
    }

    public function previewDocument(PengajuanHki $pengajuan, string $documentType)
    {
        if (!in_array($documentType, ['surat_pengalihan', 'surat_pernyataan'])) {
            abort(404);
        }

        // Decode dokumen pendukung
        $dokumen = is_string($pengajuan->file_dokumen_pendukung)
            ? json_decode($pengajuan->file_dokumen_pendukung, true) : ($pengajuan->file_dokumen_pendukung ?? []);

        // Ambil path original & signed
        $originalPath = $dokumen[$documentType] ?? null;
        $signedPath   = $dokumen['signed'][$documentType] ?? null;
        
        // Tentukan preferensi file berdasarkan status pengajuan
        $preferSigned = $pengajuan->status !== 'menunggu_validasi';

        // Inisialisasi
        $filePath    = null;
        $useOriginal = false;
        
        if ($preferSigned && $signedPath) {
            // Prioritaskan file signed ketika sudah divalidasi
            $signedPath = ltrim($signedPath, '/');
            if (Storage::disk('public')->exists($signedPath)) {
                $filePath    = $signedPath;
                $useOriginal = false;
            }
        }

        // Jika belum dapat file (misalnya sebelum validasi), gunakan original
        if (!$filePath && $originalPath) {
                $originalPath = ltrim($originalPath, '/');
                if (Storage::disk('public')->exists($originalPath)) {
                $filePath    = $originalPath;
                    $useOriginal = true;
                }
            }

        // Jika preferSigned=false tapi signed tersedia & original tidak ada, fallback ke signed
        if (!$filePath && $signedPath) {
            $signedPath = ltrim($signedPath, '/');
            if (Storage::disk('public')->exists($signedPath)) {
                $filePath    = $signedPath;
                $useOriginal = !$preferSigned; // Jika fallback berarti status menunggu_validasi tapi original hilang
            }
        }
        
        // Jika masih belum ada file, abort
        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        // Tentukan overlay: hanya tampilkan overlay jika menggunakan dokumen original
        $overlayData = $useOriginal ? ($dokumen['overlays'][$documentType] ?? []) : [];

        // Full path absolute untuk mtime
        $fullPath = storage_path('app/public/' . ltrim($filePath, '/'));

        // Tambahkan query param versi (mtime) agar browser ambil file terbaru
        $fileMTime = @file_exists($fullPath) ? @filemtime($fullPath) : time();
        $fileUrl = Storage::url($filePath) . '?v=' . $fileMTime;

        // Ambil page count aktual
        $actualPageCount = $this->countPdfPages($fullPath);

        return view('persetujuan.preview', [
            'pengajuan'        => $pengajuan,
            'documentType'     => $documentType,
            'fileUrl'          => $fileUrl,
            'overlays'         => $overlayData,
            'dokumen'          => $dokumen,
            'actualPageCount'  => $actualPageCount,
            'useOriginal'      => $useOriginal,
            'isSignedDocument' => !$useOriginal,
        ]);
    }
    
    /**
     * Count pages in a PDF file using multiple methods with enhanced accuracy
     */
    private function countPdfPages($pdfPath): int
    {
        try {
            if (!file_exists($pdfPath)) {
                Log::warning("PDF file not found", ['path' => $pdfPath]);
                return 0;
            }
            
            $fileSize = filesize($pdfPath);
            Log::info("Analyzing PDF file", [
                'file' => basename($pdfPath),
                'size' => $fileSize,
                'size_readable' => $this->formatFileSize($fileSize)
            ]);
            
            // First try FPDI as it's most reliable for complete PDF parsing
            $fpdiResult = null;
            try {
                $pdf = new \setasign\Fpdi\Fpdi();
                $fpdiResult = $pdf->setSourceFile($pdfPath);
                Log::info("FPDI analysis successful", [
                    'file' => basename($pdfPath),
                    'fpdi_pages' => $fpdiResult
                ]);
            } catch (\Exception $fpdiError) {
                Log::warning("FPDI analysis failed", [
                    'file' => basename($pdfPath),
                    'error' => $fpdiError->getMessage()
                ]);
            }
            
            // Read file content for regex analysis
            $content = file_get_contents($pdfPath);
            if ($content === false) {
                Log::error("Cannot read PDF content", ['file' => basename($pdfPath)]);
                return $fpdiResult ?? 1; // Use FPDI result if available
            }
            
            // Validate PDF header
            if (substr($content, 0, 4) !== '%PDF') {
                Log::warning("Invalid PDF header", ['file' => basename($pdfPath)]);
                return $fpdiResult ?? 1;
            }
            
            // Enhanced Method 1: More precise /Type /Page pattern
            preg_match_all('/\/Type\s*\/Page(?![a-zA-Z])/i', $content, $matches1);
            $pageCount1 = count($matches1[0]);
            
            // Enhanced Method 2: Look for /Count entries in catalog/page tree
            preg_match_all('/\/Count\s+(\d+)/i', $content, $countMatches);
            $pageCounts = [];
            if (!empty($countMatches[1])) {
                foreach ($countMatches[1] as $count) {
                    $pageCounts[] = (int)$count;
                }
            }
            $pageCount2 = empty($pageCounts) ? 0 : max($pageCounts);
            
            // Enhanced Method 3: Count page objects with better pattern
            preg_match_all('/\d+\s+\d+\s+obj\s*<<[^>]*\/Type\s*\/Page[^>]*>>/si', $content, $pageObjs);
            $pageCount3 = count($pageObjs[0]);
            
            // Enhanced Method 4: Alternative comprehensive pattern
            preg_match_all('/<<[^>]*\/Type\s*\/Page[^>]*>>/si', $content, $altMatches);
            $pageCount4 = count($altMatches[0]);
            
            // Method 5: Count endobj that belong to page objects
            preg_match_all('/\/Type\s*\/Page[^>]*>>.*?endobj/si', $content, $endObjMatches);
            $pageCount5 = count($endObjMatches[0]);
            
            // Compile all results
            $results = [
                'fpdi' => $fpdiResult,
                'method1_type_page' => $pageCount1,
                'method2_count_max' => $pageCount2,
                'method3_page_objects' => $pageCount3,
                'method4_alternative' => $pageCount4,
                'method5_endobj' => $pageCount5
            ];
            
            Log::info("Comprehensive PDF page analysis", array_merge($results, [
                'file' => basename($pdfPath)
            ]));
            
            // Smart selection logic - prioritize most reliable methods
            $validResults = array_filter(array_values($results), function($v) {
                return $v > 0 && $v <= 1000; // Reasonable page count
            });
            
            if (empty($validResults)) {
                Log::warning("No valid page count found, defaulting to 1", ['file' => basename($pdfPath)]);
                return 1;
            }
            
            // Priority order: FPDI > Method2 (count) > Method3 (objects) > Method1 > others
            $finalCount = 1;
            
            // If FPDI worked and gives reasonable result, prefer it
            if ($fpdiResult && $fpdiResult > 0 && $fpdiResult <= 1000) {
                $finalCount = $fpdiResult;
                $usedMethod = 'fpdi';
            }
            // If /Count is available and reasonable, use it (catalog page count)
            else if ($pageCount2 > 0 && $pageCount2 <= 1000) {
                $finalCount = $pageCount2;
                $usedMethod = 'count_catalog';
            }
            // Fall back to object counting
            else if ($pageCount3 > 0 && $pageCount3 <= 1000) {
                $finalCount = $pageCount3;
                $usedMethod = 'page_objects';
            }
            // Use type/page pattern
            else if ($pageCount1 > 0 && $pageCount1 <= 1000) {
                $finalCount = $pageCount1;
                $usedMethod = 'type_page';
            }
            // Last resort: use maximum of all valid results
            else {
                $finalCount = max($validResults);
                $usedMethod = 'max_valid';
            }
            
            // Cross-validation: if results are very different, prefer FPDI or conservative estimate
            $distinctResults = array_unique($validResults);
            if (count($distinctResults) > 2) {
                $variance = max($distinctResults) - min($distinctResults);
                if ($variance > 3) { // High variance
                    Log::warning("High variance in page count methods", [
                        'file' => basename($pdfPath),
                        'variance' => $variance,
                        'results' => $distinctResults
                    ]);
                    
                    // In case of high variance, prefer FPDI if available
                    if ($fpdiResult && $fpdiResult > 0) {
                        $finalCount = $fpdiResult;
                        $usedMethod = 'fpdi_variance_fallback';
                    }
                }
            }
            
            Log::info("Final page count decision", [
                'file' => basename($pdfPath),
                'final_count' => $finalCount,
                'method_used' => $usedMethod,
                'all_results' => $results
            ]);
            
            return $finalCount;
            
        } catch (\Exception $e) {
            Log::error("Critical error in PDF page counting", [
                'file' => $pdfPath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1; // Safe fallback
        }
    }

    /**
     * Generate a new PDF with signature overlays embedded.
     * @param string $sourcePath relative path on disk public
     * @param array $overlays overlay data array from editor
     * @param string $destPath relative path to save
     */
    private function generateSignedPdf(string $sourcePath, array $overlays, string $destPath): void
    {
        try {
            $fullSrc = storage_path('app/public/'.$sourcePath);
            $fullDest = storage_path('app/public/'.$destPath);

            Log::info("Starting PDF generation", [
                'source' => $fullSrc,
                'destination' => $fullDest,
                'source_exists' => file_exists($fullSrc),
                'overlays_count' => count($overlays)
            ]);

            if (!file_exists($fullSrc)) {
                throw new \Exception("Source PDF file not found: {$fullSrc}");
            }

            // Create new PDF instance with proper settings for page preservation
            $pdf = new Fpdi();
            
            // Disable auto page break to prevent content splitting
            $pdf->SetAutoPageBreak(false);
            
            // Set margins to 0 to preserve original layout
            $pdf->SetMargins(0, 0, 0);
            
            // Set source file and get page count
            $pageCount = $pdf->setSourceFile($fullSrc);
            
            // Validate that we can read the source PDF
            if ($pageCount < 1) {
                throw new \Exception("Source PDF has no readable pages: {$fullSrc}");
            }

            Log::info("Source PDF loaded successfully", [
                'page_count' => $pageCount,
                'source_file' => $sourcePath,
                'overlays_by_page' => array_count_values(array_column($overlays, 'page'))
            ]);

            // Process each page individually to ensure all pages are preserved
            for($pageNo = 1; $pageNo <= $pageCount; $pageNo++){
                try {
                    // Import the page template
                $template = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($template);
                    
                    // Validate page size
                    if ($size['width'] <= 0 || $size['height'] <= 0) {
                        throw new \Exception("Invalid page size for page {$pageNo}: width={$size['width']}, height={$size['height']}");
                    }
                    
                    // Determine orientation and add page with exact same dimensions
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                    
                    // Use the template to preserve ALL original content
                    // Position at (0,0) with exact dimensions to maintain 1:1 copy
                    $pdf->useTemplate($template, 0, 0, $size['width'], $size['height']);
                    
                    Log::info("Page {$pageNo} successfully imported", [
                        'page_size' => $size,
                        'orientation' => $orientation,
                        'template_id' => $template
                    ]);
                    
                } catch (\Exception $e) {
                    Log::error("Critical error processing page {$pageNo}", [
                        'error' => $e->getMessage(),
                        'page_number' => $pageNo,
                        'total_pages' => $pageCount,
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw new \Exception("Failed to process page {$pageNo}: " . $e->getMessage());
                }

                // Apply overlays to the current page only
                $overlaysApplied = 0;
                foreach($overlays as $overlayIndex => $ov){
                    $overlayPage = (int)($ov['page'] ?? 1); // Default to page 1 if not specified
                    
                    // Only apply overlay if it belongs to current page
                    if($overlayPage === $pageNo){
                        $imgPath = $this->storageUrlToPath($ov['url'] ?? '');
                        if(!$imgPath || !file_exists($imgPath)) {
                            Log::warning("Overlay image not found", [
                                'overlay_index' => $overlayIndex,
                                'url' => $ov['url'] ?? '',
                                'path' => $imgPath,
                                'page' => $pageNo
                            ]);
                            continue;
                        }
                        
                        // PERBAIKAN: Gunakan koordinat langsung dari ukuran kertas penuh
                        // Template surat sudah memiliki margin built-in, jadi overlay ditempatkan
                        // berdasarkan koordinat persentase dari ukuran kertas penuh
                        $x = round(($ov['x_percent'] / 100) * $size['width'], 2);
                        $y = round(($ov['y_percent'] / 100) * $size['height'], 2);
                        $w = round(($ov['width_percent'] / 100) * $size['width'], 2);
                        $h = round(($ov['height_percent'] / 100) * $size['height'], 2);
                        
                        // Validate overlay coordinates with more precise bounds checking
                        if ($x < 0 || $y < 0 || $w <= 0 || $h <= 0 || 
                            ($x + $w) > $size['width'] || ($y + $h) > $size['height']) {
                            Log::warning("Invalid overlay coordinates detected", [
                                'overlay_index' => $overlayIndex,
                                'page' => $pageNo,
                                'calculated' => compact('x', 'y', 'w', 'h'),
                                'page_size' => $size,
                                'percentages' => [
                                    'x_percent' => $ov['x_percent'],
                                    'y_percent' => $ov['y_percent'],
                                    'width_percent' => $ov['width_percent'],
                                    'height_percent' => $ov['height_percent']
                                ],
                                'bounds_check' => [
                                    'x_valid' => $x >= 0,
                                    'y_valid' => $y >= 0,
                                    'w_valid' => $w > 0,
                                    'h_valid' => $h > 0,
                                    'x_bounds' => ($x + $w) <= $size['width'],
                                    'y_bounds' => ($y + $h) <= $size['height']
                                ]
                            ]);
                            continue;
                        }
                        
                        // Apply the overlay image with maximum quality and precision
                        try {
                            // Use high DPI setting for crisp overlay rendering
                            $pdf->Image($imgPath, $x, $y, $w, $h, '', '', '', false, 300, '', false, false, 0, false, false, true);
                            $overlaysApplied++;
                        
                            Log::info("Successfully applied overlay on page {$pageNo}", [
                                'overlay_index' => $overlayIndex,
                                'image_path' => basename($imgPath),
                                'position' => compact('x', 'y', 'w', 'h'),
                                'page_size' => $size,
                                'percentage_coords' => [
                                    'x_percent' => $ov['x_percent'],
                                    'y_percent' => $ov['y_percent'],
                                    'width_percent' => $ov['width_percent'],
                                    'height_percent' => $ov['height_percent']
                                ]
                            ]);
                        } catch (\Exception $e) {
                            Log::error("Failed to apply overlay on page {$pageNo}", [
                                'overlay_index' => $overlayIndex,
                                'error' => $e->getMessage(),
                                'image_path' => $imgPath,
                                'position' => compact('x', 'y', 'w', 'h'),
                                'overlay_data' => $ov
                            ]);
                        }
                    }
                }

                Log::info("Page {$pageNo} processing completed", [
                    'overlays_applied' => $overlaysApplied,
                    'page_size' => $size
                ]);
            }

            // Ensure destination directory exists
            Storage::disk('public')->makeDirectory(dirname($destPath));

            // Save the PDF with all pages and overlays
            $pdf->Output($fullDest, 'F');
            
            // Comprehensive verification of the generated file
            $finalPageCount = 0;
            $finalFileSize = 0;
            $verificationSuccess = false;
            
            if (file_exists($fullDest)) {
                $finalFileSize = filesize($fullDest);
                
                try {
                    // Verify using FPDI
                    $verifyPdf = new Fpdi();
                    $finalPageCount = $verifyPdf->setSourceFile($fullDest);
                    
                    // Detailed verification - check each page can be imported
                    for ($i = 1; $i <= $finalPageCount; $i++) {
                        $verifyTemplate = $verifyPdf->importPage($i);
                        $verifySize = $verifyPdf->getTemplateSize($verifyTemplate);
                        
                        if ($verifySize['width'] <= 0 || $verifySize['height'] <= 0) {
                            Log::error("Generated PDF has invalid page {$i}", ['size' => $verifySize]);
                        } else {
                            Log::debug("Verified page {$i}", [
                            'page_size' => $verifySize,
                            'template_id' => $verifyTemplate
                        ]);
                    }
                    }
                    
                    $verificationSuccess = true;
                    
                } catch (\Exception $e) {
                    Log::error("Error verifying generated PDF", [
                        'error' => $e->getMessage(),
                        'file' => $destPath
                    ]);
                }
            } else {
                throw new \Exception("Generated PDF file not found after creation");
            }
            
            Log::info("PDF generation completed successfully", [
                'destination' => $destPath,
                'original_pages' => $pageCount,
                'final_pages' => $finalPageCount,
                'overlays_total' => count($overlays),
                'file_size_bytes' => $finalFileSize,
                'file_size_readable' => $this->formatFileSize($finalFileSize),
                'pages_match' => ($pageCount === $finalPageCount),
                'verification_success' => $verificationSuccess
            ]);

            // Critical validation: Pages must match exactly
            if ($pageCount !== $finalPageCount) {
                Log::error("CRITICAL: PAGE COUNT MISMATCH DETECTED!", [
                    'source_file' => $sourcePath,
                    'original_pages' => $pageCount,
                    'generated_pages' => $finalPageCount,
                    'overlays_count' => count($overlays),
                    'file_size' => $finalFileSize
                ]);
                
                // This is a critical error but we don't throw exception
                // as the PDF was generated and might still be usable
                // The log will help debugging
            }
            
            // Additional validation using our own page counting method
            $countedPages = $this->countPdfPages($fullDest);
            if ($countedPages !== $finalPageCount) {
                Log::warning("Page counting method discrepancy", [
                    'fpdi_count' => $finalPageCount,
                    'regex_count' => $countedPages,
                    'file' => $destPath
                ]);
            }

        } catch(\Throwable $e){
            Log::error('Critical error in PDF generation', [
                'message' => $e->getMessage(),
                'source' => $sourcePath,
                'destination' => $destPath,
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Clean up failed file if it exists
            if (file_exists($fullDest)) {
                unlink($fullDest);
                Log::info("Cleaned up failed PDF file", ['file' => $fullDest]);
            }
            
            throw new \Exception("PDF generation failed: " . $e->getMessage());
        }
    }

    private function storageUrlToPath(string $url): ?string
    {
        $prefix = rtrim(Storage::url(''), '/'); // e.g. "/storage"
        if(strpos($url, $prefix) === 0){
            $relative = ltrim(substr($url, strlen($prefix)), '/'); // remove leading slash
            return storage_path('app/public/'.$relative);
        }
        return null;
    }
}