<?php

namespace App\Http\Controllers;

use App\Models\PengajuanHki;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PengajuanHkiExport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PengajuanHkiSingleExport;


class AdminController extends Controller
{
    // Dashboard admin: tampilkan semua pengajuan
    public function dashboard()
    {
        // Exclude draft from admin view
        $pengajuan = PengajuanHki::where('status', '!=', 'draft')->orderBy('created_at', 'desc')->paginate(15);
        $total = PengajuanHki::where('status', '!=', 'draft')->count();
        $totalSelesai = PengajuanHki::where('status', 'selesai')->count();
        $totalMenunggu = PengajuanHki::where('status', 'menunggu_validasi')->count();
        $totalDivalidasi = PengajuanHki::whereIn('status', ['divalidasi', 'sedang_di_proses', 'divalidasi_sedang_diproses'])->count();
        $totalSedangDiProses = 0; // Tidak digunakan lagi karena digabung dengan divalidasi
        $totalMenungguPembayaran = PengajuanHki::where('status', 'menunggu_pembayaran')->count();
        $totalMenungguVerifikasi = PengajuanHki::where('status', 'menunggu_verifikasi_pembayaran')->count();
        $totalDitolak = PengajuanHki::where('status', 'ditolak')->count();

        // Hitung pengajuan yang sudah lengkap datanya (exclude draft)
        $totalLengkap = PengajuanHki::where('status', '!=', 'draft')
            ->whereNotNull('judul_karya')
            ->whereNotNull('deskripsi')
            ->whereNotNull('file_karya')
            ->whereNotNull('file_dokumen_pendukung')
            ->count();

        // Statistik pengajuan 30 hari terakhir
        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Ambil jumlah pengajuan per tanggal (exclude draft)
        $rawDaily = PengajuanHki::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('status', '!=', 'draft')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Susun array label (tgl) dan data berurutan per hari
        $labels = [];
        $data = [];
        for ($i = 0; $i < 30; $i++) {
            $current = $startDate->copy()->addDays($i);
            $dateKey = $current->format('Y-m-d');
            $labels[] = $current->format('d M');
            $data[] = $rawDaily[$dateKey] ?? 0;
        }

        return view('admin.dashboard', compact(
            'pengajuan',
            'total',
            'totalSelesai',
            'totalMenunggu',
            'totalDivalidasi',
            'totalSedangDiProses',
            'totalMenungguPembayaran',
            'totalMenungguVerifikasi',
            'totalDitolak',
            'totalLengkap',
            'labels',
            'data'
        ));
    }

    // Rekap data: export ke Excel jika semua data lengkap
    public function rekap()
    {
        // Exclude draft from export
        $total = PengajuanHki::where('status', '!=', 'draft')->count();
        $totalLengkap = PengajuanHki::where('status', '!=', 'draft')
            ->whereNotNull('judul_karya')
            ->whereNotNull('deskripsi')
            ->whereNotNull('file_karya')
            ->whereNotNull('file_dokumen_pendukung')
            ->count();
        if ($total === 0 || $total !== $totalLengkap) {
            return Redirect::back()->with('error', 'Tidak semua data lengkap. Rekap hanya bisa dilakukan jika semua data sudah lengkap.');
        }
        return Excel::download(new PengajuanHkiExport, 'rekap_pengajuan_hki.xlsx');
    }

    // Daftar Pengajuan untuk admin
    public function pengajuan(Request $request)
    {
        // Start query excluding draft from admin view
        $query = PengajuanHki::where('status', '!=', 'draft');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('judul_karya', 'like', "%{$search}%")
                  ->orWhere('nama_pengusul', 'like', "%{$search}%")
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
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $pengajuan = $query->paginate(15);
        $total = PengajuanHki::where('status', '!=', 'draft')->count();
        $totalLengkap = PengajuanHki::where('status', '!=', 'draft')
            ->whereNotNull('judul_karya')
            ->whereNotNull('deskripsi')
            ->whereNotNull('file_karya')
            ->whereNotNull('file_dokumen_pendukung')
            ->count();
        return view('admin.pengajuan', compact('pengajuan', 'total', 'totalLengkap'));
    }

    // Detail pengajuan untuk admin
    public function show($id)
    {
        $pengajuan = PengajuanHki::with(['user','pengaju'])->findOrFail($id);

        // Proses dokumen pendukung untuk menampilkan file terbaru/bertanda tangan
        $dokumen = [];
        if($pengajuan->file_dokumen_pendukung){
            $dokumen = is_string($pengajuan->file_dokumen_pendukung)
                ? json_decode($pengajuan->file_dokumen_pendukung, true)
                : $pengajuan->file_dokumen_pendukung;
        }
        
        $pencipta = $pengajuan->pengaju;
        $preferSigned = in_array($pengajuan->status, ['divalidasi','sedang_di_proses','divalidasi_sedang_diproses','menunggu_pembayaran','menunggu_verifikasi_pembayaran','selesai']);

        // Konfigurasi dokumen dengan prioritas file bertanda tangan
        $documents = [
            'contoh_ciptaan' => [
                'label'=>'Contoh Ciptaan',
                'description'=>'File contoh karya yang diajukan',
                'icon'=>'fas fa-palette',
                'color'=>'primary',
                'file_info'=>$this->getFileInfoFromFileKarya($pengajuan->file_karya)
            ],
            'surat_pengalihan' => [
                'label'=>'Surat Pengalihan Hak',
                'description'=>'Dokumen pengalihan hak cipta',
                'icon'=>'fas fa-exchange-alt',
                'color'=>'info',
                'file_info'=>$this->getFileInfoFromDokumen($dokumen,'surat_pengalihan',$preferSigned)
            ],
            'surat_pernyataan' => [
                'label'=>'Surat Pernyataan',
                'description'=>'Surat pernyataan keaslian karya',
                'icon'=>'fas fa-file-signature',
                'color'=>'warning',
                'file_info'=>$this->getFileInfoFromDokumen($dokumen,'surat_pernyataan',$preferSigned)
            ],
            'ktp'=>[
                'label'=>'KTP Pencipta',
                'description'=>'Kartu Tanda Penduduk',
                'icon'=>'fas fa-id-card',
                'color'=>'success',
                'file_info'=>$this->getFileInfoFromDokumen($dokumen,'ktp',$preferSigned)
            ]
        ];

        // Kembalikan tampilan detail khusus admin yang mencakup fitur persetujuan, verifikasi & pembayaran
        return view('admin.show', compact('pengajuan', 'dokumen', 'pencipta', 'documents'));
    }

    // Update status pengajuan
    public function updateStatus(Request $request, PengajuanHki $pengajuan)
    {
        // Add logging for debugging
        Log::info('AdminController::updateStatus called', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role ?? 'unknown',
            'pengajuan_id' => $pengajuan->id,
            'old_status' => $pengajuan->status,
            'new_status' => $request->status,
            'request_data' => $request->all()
        ]);

        // Check if user is admin
        if(auth()->user()->role !== 'admin'){
            Log::warning('Unauthorized access to updateStatus', [
                'user_id' => auth()->id(),
                'user_role' => auth()->user()->role ?? 'unknown'
            ]);
            abort(403, 'Akses tidak diizinkan');
        }

        $request->validate([
            'status' => 'required|in:menunggu_validasi,divalidasi_sedang_diproses,menunggu_pembayaran,menunggu_verifikasi_pembayaran,disetujui,selesai,ditolak'
        ]);

        $oldStatus = $pengajuan->status;
        $pengajuan->status = $request->status;
        $pengajuan->save();

        // Create notification for user when status changes
        if($oldStatus !== $request->status) {
            Notifikasi::create([
                'user_id' => $pengajuan->user_id,
                'pengajuan_hki_id' => $pengajuan->id,
                'judul' => 'Status Pengajuan Diperbarui',
                'pesan' => 'Status pengajuan HKI Anda telah diperbarui dari "' . 
                          ucfirst(str_replace('_', ' ', $oldStatus)) . '" menjadi "' . 
                          ucfirst(str_replace('_', ' ', $request->status)) . '".',
                'status' => 'unread',
                'dibaca' => false
            ]);
        }

        Log::info('Status updated successfully', [
            'pengajuan_id' => $pengajuan->id,
            'old_status' => $oldStatus,
            'new_status' => $pengajuan->status
        ]);

        return Redirect::back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function destroy(PengajuanHki $pengajuan)
    {
        // hapus file terkait jika perlu TODO
        $pengajuan->delete();
        return Redirect::back()->with('success', 'Pengajuan berhasil dihapus.');
    }

    public function setBillingCode(Request $request, PengajuanHki $pengajuan)
    {
        if(auth()->user()->role !== 'admin'){
            abort(403);
        }
        $request->validate([
            'billing_code' => 'required|string|max:50'
        ]);

        $pengajuan->update([
            'billing_code' => $request->billing_code,
            'status' => $pengajuan->status === 'menunggu_pembayaran' ? 'menunggu_pembayaran' : $pengajuan->status,
        ]);

        // Notifikasi dalam sistem
        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Kode Billing Pembayaran',
            'pesan' => 'Kode billing untuk pengajuan HKI Anda: ' . $request->billing_code .'. Silakan melakukan pembayaran sesuai instruksi.',
            'status' => 'unread',
            'dibaca' => false
        ]);

        // WhatsApp notification
        $this->sendWhatsappNotification($pengajuan->no_hp, "Kode billing pembayaran HKI Anda: {$request->billing_code}. Silakan membayar sebelum jatuh tempo.");

        return Redirect::back()->with('success', 'Kode billing berhasil disimpan dan pemberitahuan telah dikirim.');
    }

    private function sendWhatsappNotification(?string $phone, string $message): void
    {
        if(!$phone){
            return; // no phone number
        }
        try {
            $apiUrl = config('services.whatsapp.url');
            $token = config('services.whatsapp.token');
            if(!$apiUrl || !$token){
                Log::warning('WhatsApp API not configured');
                return;
            }
            $client = new \GuzzleHttp\Client();
            $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'to' => $phone,
                    'message' => $message,
                ]
            ]);
        } catch(\Throwable $e){
            Log::error('Failed send WhatsApp: '.$e->getMessage());
        }
    }

    /* --------- Helper methods (salinan dari PersetujuanController) --------- */

    private function getFileInfoFromDokumen($dokumen, $key, bool $preferSigned = false)
    {
        if (!is_array($dokumen)) {
            return [
                'exists' => false,
                'url' => null,
                'filename' => null,
                'extension' => null,
                'size' => null,
                'is_signed' => false,
                'original_exists' => false,
                'signed_exists' => false
            ];
        }

        $originalFile = $dokumen[$key] ?? null;
        $signedFile = $dokumen['signed'][$key] ?? null;
        
        // Tentukan file mana yang akan digunakan
        $useSignedFile = $preferSigned && $signedFile;
        $selectedFile = $useSignedFile ? $signedFile : $originalFile;
        
        if (!$selectedFile) {
            return [
                'exists' => false,
                'url' => null,
                'filename' => null,
                'extension' => null,
                'size' => null,
                'is_signed' => false,
                'original_exists' => (bool)$originalFile,
                'signed_exists' => (bool)$signedFile
            ];
        }

        // Dapatkan info file utama
        $fileInfo = $this->getFileInfo($selectedFile);
        if (!$fileInfo) {
            return [
                'exists' => false,
                'url' => null,
                'filename' => null,
                'extension' => null,
                'size' => null,
                'is_signed' => false,
                'original_exists' => (bool)$originalFile,
                'signed_exists' => (bool)$signedFile
            ];
        }

        // Cek keberadaan file original dan signed
        $originalExists = $originalFile ? $this->fileExists($originalFile) : false;
        $signedExists = $signedFile ? $this->fileExists($signedFile) : false;
        
        return [
            'exists' => true,
            'url' => $fileInfo['url'],
            'filename' => $fileInfo['filename'],
            'extension' => $fileInfo['extension'],
            'size' => $fileInfo['size'],
            'is_signed' => $useSignedFile,
            'original_exists' => $originalExists,
            'signed_exists' => $signedExists,
            'original_url' => $originalExists && $originalFile ? $this->getFileUrl($originalFile) : null,
            'signed_url' => $signedExists && $signedFile ? $this->getFileUrl($signedFile) : null,
            'original_filename' => $originalFile ? basename($originalFile) : null,
            'signed_filename' => $signedFile ? basename($signedFile) : null
        ];
    }

    private function getFileInfoFromFileKarya($fileKarya)
    {
        if (!$fileKarya) {
            return [
                'exists' => false,
                'url' => null,
                'filename' => null,
                'extension' => null,
                'size' => null,
                'is_signed' => false
            ];
        }

        // Jika URL eksternal, kembalikan info dasar
        if (filter_var($fileKarya, FILTER_VALIDATE_URL)) {
            return [
                'exists' => true,
                'url' => $fileKarya,
                'filename' => basename($fileKarya),
                'extension' => strtoupper(pathinfo($fileKarya, PATHINFO_EXTENSION)),
                'size' => 0,
                'is_signed' => false // File karya tidak ditandatangani
            ];
        }

        $fileInfo = $this->getFileInfo($fileKarya);
        if (!$fileInfo) {
            return [
                'exists' => false,
                'url' => null,
                'filename' => null,
                'extension' => null,
                'size' => null,
                'is_signed' => false
            ];
        }

        return [
            'exists' => true,
            'url' => $fileInfo['url'],
            'filename' => $fileInfo['filename'],
            'extension' => $fileInfo['extension'],
            'size' => $fileInfo['size'],
            'is_signed' => false // File karya tidak ditandatangani
        ];
    }

    private function fileExists($filePath)
    {
        if (!$filePath) return false;
        
        // Normalize path
        $normalized = ltrim($filePath, '/');
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }
        
        return Storage::disk('public')->exists($normalized);
    }

    private function getFileUrl($filePath)
    {
        if (!$filePath) return null;
        
        // Jika sudah URL, return as is
        if (filter_var($filePath, FILTER_VALIDATE_URL)) {
            return $filePath;
        }
        
        // Normalize path dan return storage URL
        $normalized = ltrim($filePath, '/');
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }
        
        return Storage::url($normalized);
    }

    private function getFileInfo($filePath)
    {
        if (!$filePath) return null;
        // Normalize various forms: leading slash, /storage/, full URL
        $normalized = ltrim($filePath, '/');
        if(!Storage::disk('public')->exists($normalized)){
            // Strip 'storage/' prefix if present
            if(str_starts_with($normalized, 'storage/')){
                $maybe = substr($normalized, strlen('storage/'));
                if(Storage::disk('public')->exists($maybe)){
                    $normalized = $maybe;
                }
            } elseif(filter_var($normalized, FILTER_VALIDATE_URL)){
                $maybeRel = $this->storageUrlToRelative($normalized);
                if($maybeRel && Storage::disk('public')->exists($maybeRel)){
                    $normalized = $maybeRel;
                }
            }
        }

        if (!Storage::disk('public')->exists($normalized)) return null;

        $size = Storage::disk('public')->size($normalized);
        $extension = pathinfo($normalized, PATHINFO_EXTENSION);

        return [
            'size' => $size,
            'size_formatted' => $this->formatFileSize($size),
            'extension' => strtoupper($extension),
            'filename' => basename($normalized),
            'icon_class' => $this->getFileTypeIcon($extension),
            'url' => Storage::url($normalized),
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
            return $bytes . ' B';
        }
    }

    private function getFileTypeIcon($extension)
    {
        $ext = strtolower($extension);
        return match($ext) {
            'pdf' => 'fas fa-file-pdf text-danger',
            'doc','docx' => 'fas fa-file-word text-primary',
            'xls','xlsx','csv' => 'fas fa-file-excel text-success',
            'ppt','pptx' => 'fas fa-file-powerpoint text-warning',
            'jpg','jpeg','png','gif','svg','webp' => 'fas fa-file-image text-info',
            default => 'fas fa-file'
        };
    }

    public function downloadBukti(PengajuanHki $pengajuan)
    {
        if(auth()->user()->role !== 'admin'){
            abort(403);
        }
        $path = $pengajuan->bukti_pembayaran;
        if(!$path || !Storage::disk('public')->exists($path)){
            return Redirect::back()->with('error','Bukti pembayaran tidak ditemukan.');
        }
        return response()->file(storage_path('app/public/'.$path));
    }

    public function viewSignedSuratPengalihan(PengajuanHki $pengajuan)
    {
        if(auth()->user()->role !== 'admin'){
            abort(403);
        }

        $dokumen = $this->getDokumenJson($pengajuan);
        $signedFile = null;

        // 1. Prioritas utama: cari di signed path yang disimpan langsung
        if (!empty($dokumen['signed']['surat_pengalihan'])) {
            $signedFile = $dokumen['signed']['surat_pengalihan'];
            Log::info("Found signed pengalihan in JSON", ['path' => $signedFile]);
        }

        // 2. Fallback: cari dengan pattern nama file di folder signed_documents
        if (!$signedFile || !$this->fileExists($signedFile)) {
            $signedFile = $this->searchSignedFile($pengajuan->id, 'surat_pengalihan');
            Log::info("Searched for signed pengalihan file", ['found' => $signedFile]);
        }

        // 3. Fallback tambahan: cari file yang mengandung pattern ID dan document type
        if (!$signedFile || !$this->fileExists($signedFile)) {
            $signedFile = $this->findSignedDocumentByPattern($pengajuan->id, 'surat_pengalihan');
            Log::info("Pattern search for signed pengalihan", ['found' => $signedFile]);
        }

        if (!$signedFile || !$this->fileExists($signedFile)) {
            Log::error("Signed surat pengalihan not found", [
                'pengajuan_id' => $pengajuan->id,
                'dokumen_json' => $dokumen,
                'searched_patterns' => [
                    $pengajuan->id . '_surat_pengalihan_*',
                    '*_surat_pengalihan_*_' . $pengajuan->id . '_*'
                ]
            ]);
            return redirect()->back()->with('error', 'Surat pengalihan yang ditandatangani tidak ditemukan. Pastikan direktur sudah menandatangani dokumen.');
        }

        // Normalize path untuk response
        $normalizedPath = $this->normalizePath($signedFile);
        
        // Log untuk debugging
        Log::info("Serving signed surat pengalihan", [
            'pengajuan_id' => $pengajuan->id,
            'signed_file' => $signedFile,
            'normalized_path' => $normalizedPath,
            'file_exists' => file_exists(storage_path('app/public/' . $normalizedPath))
        ]);

        return response()->file(storage_path('app/public/' . $normalizedPath));
    }

    public function viewSignedSuratPernyataan(PengajuanHki $pengajuan)
    {
        if(auth()->user()->role !== 'admin'){
            abort(403);
        }

        $dokumen = $this->getDokumenJson($pengajuan);
        $signedFile = null;

        // 1. Prioritas utama: cari di signed path yang disimpan langsung
        if (!empty($dokumen['signed']['surat_pernyataan'])) {
            $signedFile = $dokumen['signed']['surat_pernyataan'];
            Log::info("Found signed pernyataan in JSON", ['path' => $signedFile]);
        }

        // 2. Fallback: cari dengan pattern nama file di folder signed_documents
        if (!$signedFile || !$this->fileExists($signedFile)) {
            $signedFile = $this->searchSignedFile($pengajuan->id, 'surat_pernyataan');
            Log::info("Searched for signed pernyataan file", ['found' => $signedFile]);
        }

        // 3. Fallback tambahan: cari file yang mengandung pattern ID dan document type
        if (!$signedFile || !$this->fileExists($signedFile)) {
            $signedFile = $this->findSignedDocumentByPattern($pengajuan->id, 'surat_pernyataan');
            Log::info("Pattern search for signed pernyataan", ['found' => $signedFile]);
        }

        if (!$signedFile || !$this->fileExists($signedFile)) {
            Log::error("Signed surat pernyataan not found", [
                'pengajuan_id' => $pengajuan->id,
                'dokumen_json' => $dokumen,
                'searched_patterns' => [
                    $pengajuan->id . '_surat_pernyataan_*',
                    '*_surat_pernyataan_*_' . $pengajuan->id . '_*'
                ]
            ]);
            return redirect()->back()->with('error', 'Surat pernyataan yang ditandatangani tidak ditemukan. Pastikan direktur sudah menandatangani dokumen.');
        }

        // Normalize path untuk response
        $normalizedPath = $this->normalizePath($signedFile);
        
        // Log untuk debugging
        Log::info("Serving signed surat pernyataan", [
            'pengajuan_id' => $pengajuan->id,
            'signed_file' => $signedFile,
            'normalized_path' => $normalizedPath,
            'file_exists' => file_exists(storage_path('app/public/' . $normalizedPath))
        ]);

        return response()->file(storage_path('app/public/' . $normalizedPath));
    }

    /**
     * Mencari file signed dengan pattern yang lebih canggih
     */
    private function findSignedDocumentByPattern(int $pengajuanId, string $type): ?string
    {
        // Cari di folder signed_documents
        $signedFolder = 'signed_documents';
        if (!Storage::disk('public')->exists($signedFolder)) {
            return null;
        }

        $files = Storage::disk('public')->files($signedFolder);
        
        // Pattern pencarian yang lebih fleksibel
        $patterns = [
            // Pattern utama: ID_type_name_timestamp.pdf
            "/^{$pengajuanId}_{$type}_.*\.pdf$/i",
            // Pattern alternatif: yang mengandung ID dan type
            "/.*{$pengajuanId}.*{$type}.*\.pdf$/i",
            // Pattern untuk file yang mengandung type dan ID di posisi manapun
            "/.*{$type}.*{$pengajuanId}.*\.pdf$/i",
        ];

        foreach ($patterns as $pattern) {
            foreach ($files as $file) {
                $basename = basename($file);
                if (preg_match($pattern, $basename)) {
                    Log::info("Found signed file with pattern", [
                        'pattern' => $pattern,
                        'file' => $file,
                        'basename' => $basename
                    ]);
                    return $file;
                }
            }
        }

        return null;
    }

    /**
     * Normalize path untuk menghindari masalah path
     */
    private function normalizePath(string $path): string
    {
        // Hilangkan leading slash dan storage prefix
        $normalized = ltrim($path, '/');
        
        // Hilangkan prefix storage/ jika ada
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }
        
        // Pastikan menggunakan forward slash
        $normalized = str_replace('\\', '/', $normalized);
        
        return $normalized;
    }

    // Generate Rekap PDF per pengajuan
    public function rekapPdf(PengajuanHki $pengajuan)
    {
        // 1. Generate summary page HTML->PDF and save temp
        $summaryPdfContents = Pdf::loadView('admin.rekap_pdf', [
            'pengajuan' => $pengajuan,
            'dokumen' => $this->getDokumenJson($pengajuan),
            'documents' => $this->buildDocumentsArray($pengajuan)
        ])->output();

        $tempDir = storage_path('app/temp');
        if(!file_exists($tempDir)) mkdir($tempDir, 0775, true);
        $summaryPath = $tempDir.'/summary_'.$pengajuan->id.'_'.time().'.pdf';
        file_put_contents($summaryPath, $summaryPdfContents);

        // 2. Prepare document paths ordered: karya, surat_pengalihan (signed), surat_pernyataan (signed), ktp
        $docPaths = [];
        $dokumenJson = $this->getDokumenJson($pengajuan);

        // Helper inline
        $pushIfPdfExists = function($relative) use (&$docPaths, &$pushIfPdfExists){
            // Support array of files
            if(is_array($relative)){
                foreach($relative as $rel){ $pushIfPdfExists($rel); }
                return;
            }
            if(!$relative) return;

            // if already full path on filesystem, push directly
            if(file_exists($relative) && pathinfo($relative, PATHINFO_EXTENSION)){
                $full = $relative;
                $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
                if($ext!=='pdf'){
                    $pdfPath=$this->convertImageToPdf($full);
                    if($pdfPath) $docPaths[]=$pdfPath;
                } else { $docPaths[]=$full; }
                return;
            }

            $relative = ltrim($relative, '/');
            if(!Storage::disk('public')->exists($relative)){
                // mungkin URL /storage/xxx atau absolute URL
                if(str_starts_with($relative, 'storage/')){
                    $relative = substr($relative, strlen('storage/'));
                } elseif(filter_var($relative, FILTER_VALIDATE_URL)){
                    $maybeRel = $this->storageUrlToRelative($relative);
                    if($maybeRel){ $relative = $maybeRel; }
                }
            }

            if(Storage::disk('public')->exists($relative)){
                $full = storage_path('app/public/'.$relative);
                // Ensure pdf; if not convert image to pdf
                $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
                if($ext !== 'pdf'){
                    $pdfPath = $this->convertImageToPdf($full);
                    if($pdfPath) $docPaths[] = $pdfPath;
                } else {
                    $docPaths[] = $full;
                }
            }
        };

        // file karya (could be link or storage)
        if($pengajuan->file_karya){
            $karyaRel = ltrim($pengajuan->file_karya, '/');
            $pushIfPdfExists($karyaRel);
        }

        // surat pengalihan signed; fallback heuristik nama berpattern
        $signedPengalihan = $dokumenJson['signed']['surat_pengalihan']
            ?? $this->searchSignedFile($pengajuan->id,'surat_pengalihan');
        if(!$signedPengalihan){
            $signedPengalihan = $this->searchSignedFile($pengajuan->id,'surat_pengalihan');
        }
        if(!$signedPengalihan && isset($dokumenJson['surat_pengalihan']) && str_contains($dokumenJson['surat_pengalihan'], '_surat_pengalihan_')){
            $signedPengalihan = $dokumenJson['surat_pengalihan'];
        }
        $pushIfPdfExists($signedPengalihan);

        // surat pernyataan signed; fallback heuristik nama berpattern
        $signedPernyataan = $dokumenJson['signed']['surat_pernyataan']
            ?? $this->searchSignedFile($pengajuan->id,'surat_pernyataan');
        if(!$signedPernyataan){
            $signedPernyataan = $this->searchSignedFile($pengajuan->id,'surat_pernyataan');
        }
        if(!$signedPernyataan && isset($dokumenJson['surat_pernyataan']) && str_contains($dokumenJson['surat_pernyataan'], '_surat_pernyataan_')){
            $signedPernyataan = $dokumenJson['surat_pernyataan'];
        }
        $pushIfPdfExists($signedPernyataan);

        // ktp (could be array?)
        $pushIfPdfExists($dokumenJson['ktp'] ?? null);

        // 3. Merge using FPDI
        $mergedPath = $tempDir.'/rekap_'.$pengajuan->id.'_'.time().'.pdf';
        $this->mergePdfFiles(array_merge([$summaryPath], $docPaths), $mergedPath);

        return response()->download($mergedPath)->deleteFileAfterSend(true);
    }

    private function getDokumenJson(PengajuanHki $pengajuan)
    {
        return is_string($pengajuan->file_dokumen_pendukung)
            ? json_decode($pengajuan->file_dokumen_pendukung, true)
            : ($pengajuan->file_dokumen_pendukung ?? []);
    }

    private function buildDocumentsArray(PengajuanHki $pengajuan): array
    {
        $dokumen = $this->getDokumenJson($pengajuan);
        $preferSigned = true;
        return [
            'file_karya' => [
                'label' => 'File Karya Ciptaan',
                'file_info' => $this->getFileInfoFromFileKarya($pengajuan->file_karya),
            ],
            'surat_pengalihan' => [
                'label' => 'Surat Pengalihan (Signed)',
                'file_info' => ($dokumen['signed']['surat_pengalihan'] ?? ($this->searchSignedFile($pengajuan->id,'surat_pengalihan'))) ? $this->getFileInfo($dokumen['signed']['surat_pengalihan'] ?? $this->searchSignedFile($pengajuan->id,'surat_pengalihan')) : null,
            ],
            'surat_pernyataan' => [
                'label' => 'Surat Pernyataan (Signed)',
                'file_info' => ($dokumen['signed']['surat_pernyataan'] ?? ($this->searchSignedFile($pengajuan->id,'surat_pernyataan'))) ? $this->getFileInfo($dokumen['signed']['surat_pernyataan'] ?? $this->searchSignedFile($pengajuan->id,'surat_pernyataan')) : null,
            ],
            'ktp' => [
                'label' => 'KTP Pencipta',
                'file_info' => $this->getFileInfoFromDokumen($dokumen,'ktp',$preferSigned),
            ],
        ];
    }

    private function convertImageToPdf(string $imagePath): ?string
    {
        try {
            $pdf = new \TCPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->Image($imagePath, 10, 10, 190, 0, '', '', '', true);
            $tmp = storage_path('app/temp/img2pdf_'.uniqid().'.pdf');
            $pdf->Output($tmp, 'F');
            return $tmp;
        } catch(\Throwable $e){
            Log::error('Failed convert image to PDF: '.$e->getMessage());
            return null;
        }
    }

    private function mergePdfFiles(array $files, string $outputPath)
    {
        $pdf = new \setasign\Fpdi\Fpdi();
        foreach ($files as $path) {
            if(!file_exists($path)) continue;
            $pageCount = $pdf->setSourceFile($path);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $template = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($template);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($template);
            }
        }
        $pdf->Output($outputPath, 'F');
    }

    // Generate Rekap Excel per pengajuan
    public function rekapExcel(PengajuanHki $pengajuan)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new PengajuanHkiSingleExport($pengajuan),
            'Rekap_Pengajuan_'.$pengajuan->id.'.xlsx'
        );
    }

    private function storageUrlToRelative(string $url): ?string
    {
        $prefix = rtrim(Storage::url(''), '/'); // e.g. /storage
        if(strpos($url, $prefix) !== false){
            $parts = explode($prefix, $url, 2);
            $relative = ltrim($parts[1] ?? '', '/');
            return $relative ?: null;
        }
        return null;
    }

    private function searchSignedFile(int $pengajuanId, string $type): ?string
    {
        $files = Storage::disk('public')->files('signed_documents');
        foreach($files as $file){
            $basename = basename($file);
            $lower = strtolower($basename);
            if(str_contains($lower, (string)$pengajuanId) && str_contains($lower, strtolower($type))){
                return str_replace('\\', '/', $file); // normalize to forward slashes
            }
        }
        return null;
    }



    public function finalisasi(PengajuanHki $pengajuan)
    {
        if(auth()->user()->role !== 'admin'){
            abort(403);
        }

        if($pengajuan->status !== 'divalidasi'){
            return Redirect::back()->with('error','Pengajuan belum dalam status divalidasi.');
        }

        $pengajuan->update([
            'status' => 'menunggu_pembayaran',
            'tanggal_validasi' => now(),
            'catatan_admin' => 'Finalisasi oleh admin, menunggu pembayaran.'
        ]);

        // Notifikasi ke pemohon
        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Menunggu Pembayaran',
            'pesan' => 'Pengajuan HKI Anda telah selesai divalidasi dan menunggu pembayaran.',
            'status' => 'unread',
            'dibaca' => false
        ]);

        return Redirect::back()->with('success','Status pengajuan diubah menjadi menunggu pembayaran.');
    }

    public function konfirmasiPembayaran(PengajuanHki $pengajuan)
    {
        if(auth()->user()->role !== 'admin'){
            abort(403);
        }

        if($pengajuan->status !== 'menunggu_verifikasi_pembayaran'){
            return Redirect::back()->with('error','Pengajuan tidak dalam status menunggu verifikasi pembayaran.');
        }

        $pengajuan->update([
            'status' => 'disetujui',
            'catatan_admin' => 'Pembayaran telah diverifikasi dan disetujui oleh admin.'
        ]);

        // Notifikasi ke pemohon
        Notifikasi::create([
            'user_id' => $pengajuan->user_id,
            'pengajuan_hki_id' => $pengajuan->id,
            'judul' => 'Pembayaran Disetujui',
            'pesan' => 'Pembayaran Anda telah diverifikasi dan pengajuan HKI telah disetujui.'
        ]);

        return Redirect::back()->with('success','Pembayaran berhasil dikonfirmasi dan pengajuan disetujui.');
    }

    public function uploadSertifikat(Request $request, PengajuanHki $pengajuan)
    {
        if(auth()->user()->role !== 'admin'){
            abort(403);
        }

        if($pengajuan->status !== 'disetujui'){
            return Redirect::back()->with('error','Hanya pengajuan dengan status disetujui yang dapat diupload sertifikat.');
        }

        $request->validate([
            'sertifikat' => 'required|file|mimes:pdf|max:5120' // max 5MB
        ]);

        try {
            // Delete old certificate if exists
            if ($pengajuan->sertifikat && Storage::disk('public')->exists($pengajuan->sertifikat)) {
                Storage::disk('public')->delete($pengajuan->sertifikat);
            }

            // Store new certificate
            $file = $request->file('sertifikat');
            $filename = 'sertifikat_' . $pengajuan->id . '_' . time() . '.pdf';
            $path = $file->storeAs('certificates', $filename, 'public');

            // Update pengajuan
            $pengajuan->update([
                'sertifikat' => $path,
                'status' => 'selesai'
            ]);

            // Create notification
            Notifikasi::create([
                'user_id' => $pengajuan->user_id,
                'pengajuan_hki_id' => $pengajuan->id,
                'judul' => 'Sertifikat HKI Tersedia',
                'pesan' => 'Sertifikat HKI Anda telah tersedia dan dapat diunduh.'
            ]);

            return Redirect::back()->with('success', 'Sertifikat berhasil diupload dan pengajuan telah selesai.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Gagal mengupload sertifikat: ' . $e->getMessage());
        }
    }

    public function serveSertifikat(PengajuanHki $pengajuan)
    {
        if (!auth()->check()) {
            abort(401);
        }

        $user = auth()->user();
        $canAccess = $user->role === 'admin' || 
                     $user->role === 'direktur' || 
                     $user->id === $pengajuan->user_id;

        if (!$canAccess) {
            abort(403);
        }

        if (!$pengajuan->sertifikat || !Storage::disk('public')->exists($pengajuan->sertifikat)) {
            abort(404, 'Sertifikat tidak ditemukan');
        }

        $filename = 'Sertifikat_HKI_' . $pengajuan->id . '_' . str_replace(' ', '_', $pengajuan->user->name) . '.pdf';
        
        return response()->download(
            storage_path('app/public/' . $pengajuan->sertifikat),
            $filename
        );
    }
}