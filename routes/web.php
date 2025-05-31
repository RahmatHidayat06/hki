<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PengajuanHkiController;
use App\Http\Controllers\ValidasiController;
use App\Http\Controllers\PersetujuanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotifikasiController;
use Illuminate\Support\Facades\Route;

// Route untuk guest (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
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
    
    // Route untuk validasi (admin P3M)
    Route::middleware('role:admin_p3m')->group(function () {
        Route::get('validasi', [ValidasiController::class, 'index'])->name('validasi.index');
        Route::get('validasi/{id}', [ValidasiController::class, 'show'])->name('validasi.show');
        Route::post('validasi/{id}', [ValidasiController::class, 'validasi'])->name('validasi.validasi');
    });
    
    // Route untuk persetujuan (direktur)
    Route::middleware('role:direktur')->group(function () {
        Route::get('persetujuan', [PersetujuanController::class, 'index'])->name('persetujuan.index');
        Route::get('persetujuan/{id}', [PersetujuanController::class, 'show'])->name('persetujuan.show');
        Route::post('persetujuan/{id}', [PersetujuanController::class, 'store'])->name('persetujuan.store');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Notifikasi Routes
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/mark-as-read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.markAsRead');
    Route::post('/notifikasi/mark-all-as-read', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.markAllAsRead');
    Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy'])->name('notifikasi.destroy');
});