<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PengajuanHkiController;
use App\Http\Controllers\ValidasiController;
use App\Http\Controllers\PersetujuanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotifikasiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Route untuk guest (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('home');
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// Route untuk user yang sudah login
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Route untuk pengajuan
    Route::resource('pengajuan', PengajuanHkiController::class);
    
    // Route untuk draft ciptaan
    Route::get('/draft', [PengajuanHkiController::class, 'draftIndex'])->name('draft.index');
    Route::post('/draft/store', [PengajuanHkiController::class, 'storeDraft'])->name('draft.store');
    Route::get('/draft/{pengajuan}/edit', [PengajuanHkiController::class, 'editDraft'])->name('draft.edit');
    Route::delete('/draft/{pengajuan}', [PengajuanHkiController::class, 'destroyDraft'])->name('draft.destroy');
    Route::post('/draft/{pengajuan}/submit', [PengajuanHkiController::class, 'submitDraft'])->name('draft.submit');
    Route::patch('/draft/{pengajuan}/update', [PengajuanHkiController::class, 'updateDraft'])->name('draft.update');
    Route::delete('/draft/{pengajuan}/delete-file', [PengajuanHkiController::class, 'deleteDraftFile'])->name('draft.delete_file');

    
    
    // Route untuk validasi (admin P3M dan direktur)
    Route::middleware('auth')->group(function () {
        Route::get('validasi', [ValidasiController::class, 'index'])->name('validasi.index');
        Route::get('validasi/{id}', [ValidasiController::class, 'show'])->name('validasi.show');
        Route::post('validasi/{id}', [ValidasiController::class, 'validasi'])->name('validasi.validasi');
        Route::put('validasi/{pengajuan}/finalize', [ValidasiController::class, 'finalize'])->name('validasi.finalize');
        Route::get('validasi/{pengajuan}/signature/{documentType}', [ValidasiController::class, 'showSignatureEditor'])->name('validasi.signature.editor');
        Route::post('validasi/{pengajuan}/signature/{documentType}/apply', [ValidasiController::class, 'applyOverlay'])->name('validasi.signature.apply');
    });

    // Route untuk persetujuan direktur & admin (read-only)
    Route::middleware('role:direktur,admin')->group(function () {
        Route::get('persetujuan', [PersetujuanController::class, 'index'])->name('persetujuan.index');
        Route::get('persetujuan/{id}', [PersetujuanController::class, 'show'])->name('persetujuan.show');
        Route::get('persetujuan/{id}/validation-wizard', [PersetujuanController::class, 'showValidationWizard'])->name('persetujuan.validation.wizard');
        Route::get('persetujuan/{pengajuan}/signature/{documentType}', [PersetujuanController::class, 'showSignatureEditor'])->name('persetujuan.signature.editor');
        Route::post('persetujuan/{pengajuan}/signature/{documentType}/apply', [PersetujuanController::class, 'applyOverlay'])->name('persetujuan.signature.apply');
        Route::put('persetujuan/{id}/approve', [PersetujuanController::class, 'approve'])->name('persetujuan.approve');
        Route::put('persetujuan/{id}/reject', [PersetujuanController::class, 'reject'])->name('persetujuan.reject');
        Route::put('persetujuan/{id}/update-file', [PersetujuanController::class, 'updateFile'])->name('persetujuan.updateFile');
        Route::post('persetujuan/bulk-approve', [PersetujuanController::class, 'bulkApprove'])->name('persetujuan.bulkApprove');
        Route::post('persetujuan/bulk-reject', [PersetujuanController::class, 'bulkReject'])->name('persetujuan.bulkReject');
    });

    // Preview dokumen dapat diakses oleh admin & direktur (dan owner pengaju jika perlu) : auth saja
    Route::get('persetujuan/{pengajuan}/preview/{documentType}', [PersetujuanController::class, 'previewDocument'])
        ->middleware('auth')
        ->name('persetujuan.preview');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Notifikasi Routes
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/mark-as-read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.markAsRead');
    Route::post('/notifikasi/mark-all-as-read', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.markAllAsRead');
    Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy'])->name('notifikasi.destroy');

    // Route untuk admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/pengajuan', [\App\Http\Controllers\AdminController::class, 'pengajuan'])->name('pengajuan');
        Route::get('/pengajuan/{pengajuan}', [\App\Http\Controllers\AdminController::class, 'show'])->name('pengajuan.show');
        Route::post('/pengajuan/{pengajuan}/billing', [\App\Http\Controllers\AdminController::class, 'setBillingCode'])->name('pengajuan.setBilling');
        Route::get('/rekap', [\App\Http\Controllers\AdminController::class, 'rekap'])->name('rekap');
        Route::get('/surat-pengalihan/{pengajuan}', [\App\Http\Controllers\SuratController::class, 'pengalihan'])->name('surat.pengalihan');
        Route::get('/surat-pernyataan/{pengajuan}', [\App\Http\Controllers\SuratController::class, 'pernyataan'])->name('surat.pernyataan');
        Route::get('/pengajuan/{pengajuan}/bukti', [\App\Http\Controllers\AdminController::class, 'downloadBukti'])->name('pengajuan.bukti');
        Route::get('/pengajuan/{pengajuan}/rekap-pdf', [\App\Http\Controllers\AdminController::class, 'rekapPdf'])->name('pengajuan.rekapPdf');
        Route::get('/pengajuan/{pengajuan}/rekap-excel', [\App\Http\Controllers\AdminController::class, 'rekapExcel'])->name('pengajuan.rekapExcel');
        // Routes untuk melihat surat yang sudah ditandatangani
        Route::get('/pengajuan/{pengajuan}/surat-pengalihan-signed', [\App\Http\Controllers\AdminController::class, 'viewSignedSuratPengalihan'])->name('pengajuan.suratPengalihanSigned');
        Route::get('/pengajuan/{pengajuan}/surat-pernyataan-signed', [\App\Http\Controllers\AdminController::class, 'viewSignedSuratPernyataan'])->name('pengajuan.suratPernyataanSigned');
        Route::get('/pengajuan/{pengajuan}/finalisasi', [\App\Http\Controllers\AdminController::class, 'finalisasi'])->name('pengajuan.finalisasi');
        Route::get('/pengajuan/{pengajuan}/konfirmasi-pembayaran', [\App\Http\Controllers\AdminController::class, 'konfirmasiPembayaran'])->name('pengajuan.konfirmasiPembayaran');
        Route::post('/pengajuan/{pengajuan}/upload-sertifikat', [\App\Http\Controllers\AdminController::class, 'uploadSertifikat'])->name('pengajuan.uploadSertifikat');
    });

    // Route untuk document signature overlay
    Route::middleware('auth')->prefix('document-signature')->name('document-signature.')->group(function () {
        Route::get('/{pengajuan}', [\App\Http\Controllers\DocumentSignatureController::class, 'index'])->name('index');
        Route::get('/{pengajuan}/{documentType}', [\App\Http\Controllers\DocumentSignatureController::class, 'show'])->name('show');
        Route::post('/{pengajuan}/{documentType}/apply', [\App\Http\Controllers\DocumentSignatureController::class, 'applyOverlay'])->name('apply');
    });

    // Debug route for file testing (remove in production)
    Route::get('/debug/file-test/{pengajuan}/{documentType}', function($pengajuanId, $documentType) {
        $pengajuan = \App\Models\PengajuanHki::findOrFail($pengajuanId);
        $dokumen = is_string($pengajuan->file_dokumen_pendukung) 
            ? json_decode($pengajuan->file_dokumen_pendukung, true) 
            : ($pengajuan->file_dokumen_pendukung ?? []);
        
        $documentPath = $dokumen[$documentType] ?? null;
        
        return response()->json([
            'pengajuan_id' => $pengajuanId,
            'document_type' => $documentType,
            'document_path' => $documentPath,
            'file_exists_storage' => $documentPath ? \Illuminate\Support\Facades\Storage::disk('public')->exists($documentPath) : false,
            'file_exists_filesystem' => $documentPath ? file_exists(storage_path('app/public/' . $documentPath)) : false,
            'storage_url' => $documentPath ? \Illuminate\Support\Facades\Storage::url($documentPath) : null,
            'full_path' => $documentPath ? storage_path('app/public/' . $documentPath) : null,
            'file_size' => $documentPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($documentPath) ? \Illuminate\Support\Facades\Storage::disk('public')->size($documentPath) : null,
            'storage_disk_config' => config('filesystems.disks.public'),
            'app_url' => config('app.url')
        ]);
    })->name('debug.file.test')->middleware('auth');

    // Route untuk menyimpan tanda tangan digital pengguna
    Route::post('/signature/save', [\App\Http\Controllers\SignatureController::class, 'save'])->name('signature.save');
    // Route untuk menghapus tanda tangan digital pengguna
    Route::delete('/signature/delete', [\App\Http\Controllers\SignatureController::class, 'delete'])->name('signature.delete');

    // Route untuk pembayaran
    Route::middleware(['auth', 'role:dosen,mahasiswa'])->group(function(){
        Route::get('/pembayaran/{pengajuan}', [\App\Http\Controllers\PembayaranController::class, 'form'])->name('pembayaran.form');
        Route::post('/pembayaran/{pengajuan}', [\App\Http\Controllers\PembayaranController::class, 'submit'])->name('pembayaran.submit');
        Route::get('/pembayaran/{pengajuan}/pay', [\App\Http\Controllers\PembayaranController::class, 'pay'])->name('pembayaran.pay');
        Route::get('/sertifikat/{pengajuan}/download', [\App\Http\Controllers\PembayaranController::class, 'downloadCertificate'])->name('sertifikat.download');
    });

    // Route untuk serve bukti pembayaran (accessible by owner and admin)
    Route::get('/bukti-pembayaran/{pengajuan}', [\App\Http\Controllers\PembayaranController::class, 'serveBuktiPembayaran'])->name('bukti.serve')->middleware('auth');

    // Routes untuk sertifikat
    Route::post('/sertifikat/upload/{pengajuan}', [\App\Http\Controllers\PembayaranController::class, 'uploadSertifikat'])->name('sertifikat.upload')->middleware('auth');
    Route::get('/sertifikat/{pengajuan}', [\App\Http\Controllers\AdminController::class, 'serveSertifikat'])->name('sertifikat.serve')->middleware('auth');

    Route::get('/pembayaran', [\App\Http\Controllers\PembayaranController::class, 'index'])->name('pembayaran.index');

    // Route untuk admin status update and destroy
    Route::middleware(['auth','role:admin'])->group(function(){
        Route::post('/pengajuan/{pengajuan}/status', [\App\Http\Controllers\AdminController::class, 'updateStatus'])->name('pengajuan.updateStatus');
        Route::delete('/pengajuan/{pengajuan}', [\App\Http\Controllers\AdminController::class, 'destroy'])->name('pengajuan.destroy');
        Route::get('/admin/bukti-pembayaran/{pengajuan}', [\App\Http\Controllers\AdminController::class, 'downloadBukti'])->name('admin.bukti.download');
    });

    Route::get('/d/{hash}', [\App\Http\Controllers\DocumentController::class, 'serve'])->name('dokumen.serve');

    // Route untuk serve signed documents
    Route::get('/signed-document/{pengajuan}/{documentType}', function(\App\Models\PengajuanHki $pengajuan, $documentType) {
        // Validasi akses
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
        
        // Ambil path file signed
        $dokumen = json_decode($pengajuan->file_dokumen_pendukung, true) ?? [];
        $signedPath = $dokumen['signed'][$documentType] ?? null;
        
        if (!$signedPath || !\Illuminate\Support\Facades\Storage::disk('public')->exists($signedPath)) {
            abort(404, 'File signed tidak ditemukan');
        }
        
        // Serve file dengan nama yang user-friendly
        $timestamp = now()->format('Ymd_His');
        $pengajuName = str_replace(' ', '_', $pengajuan->user->name);
        $displayName = $pengajuan->id . '_' . $documentType . '_' . $pengajuName . '_signed.pdf';
        
        return response()->download(
            storage_path('app/public/' . $signedPath), 
            $displayName
        );
    })->name('signed.document.serve');

});