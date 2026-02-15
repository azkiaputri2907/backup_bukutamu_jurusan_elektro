<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. HALAMAN PENGUNJUNG (Public) ---
Route::get('/', [GuestController::class, 'index'])->name('guest.index');

// Alur Pengisian Buku Tamu
Route::get('/isi-tamu', [GuestController::class, 'formKunjungan'])->name('guest.form');
Route::post('/simpan-tamu', [GuestController::class, 'storeKunjungan'])->name('guest.store');

// Fitur Auto-fill (Cek NIM/NIP via Ajax)
Route::get('/guest/check-pengunjung', [GuestController::class, 'check'])->name('guest.check');
Route::get('/kunjungan/konfirmasi/{id}', [GuestController::class, 'halamanKonfirmasi'])->name('guest.konfirmasi');

// Alur Survey
Route::get('/survey/{id}', [GuestController::class, 'formSurvey'])->name('guest.survey');
Route::post('/survey-simpan/{id}', [GuestController::class, 'storeSurvey'])->name('guest.survey.store');


// --- 2. AUTHENTICATION ---
Auth::routes(['register' => false]);


// --- 3. HALAMAN ADMINISTRATOR & KETUA JURUSAN ---
// PERBAIKAN: Bungkus dengan middleware role agar yang bukan Admin/Ketua tidak bisa masuk
Route::middleware(['auth', 'role:Administrator,Ketua Jurusan'])->group(function () {
    
    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Data Kunjungan
    Route::get('/admin/kunjungan', [DashboardController::class, 'kunjungan'])->name('admin.kunjungan');
    Route::post('/admin/kunjungan', [DashboardController::class, 'storeKunjungan'])->name('admin.kunjungan.store');
    Route::put('/admin/kunjungan/{id}', [DashboardController::class, 'updateKunjungan'])->name('admin.kunjungan.update');
    Route::delete('/admin/kunjungan/{id}', [DashboardController::class, 'destroyKunjungan'])->name('admin.kunjungan.destroy');

    // Data Survey
    Route::get('/admin/survey', [DashboardController::class, 'survey'])->name('admin.survey'); 
    Route::put('/admin/survey/{id}', [DashboardController::class, 'updateSurvey'])->name('admin.survey.update');
    Route::delete('/admin/survey/{id}', [DashboardController::class, 'destroySurvey'])->name('admin.survey.destroy');

    // Data Pengunjung
    Route::get('/admin/pengunjung', [DashboardController::class, 'pengunjung'])->name('admin.pengunjung');
    Route::put('/admin/pengunjung/{id}', [DashboardController::class, 'updatePengunjung'])->name('admin.pengunjung.update');
    Route::delete('/admin/pengunjung/{id}', [DashboardController::class, 'destroyPengunjung'])->name('admin.pengunjung.destroy');

    // Laporan
    Route::get('/admin/laporan', [DashboardController::class, 'laporan'])->name('admin.laporan');
    Route::post('/admin/laporan/export', [DashboardController::class, 'exportLaporan'])->name('admin.laporan.export');
    
    // --- KHUSUS ADMINISTRATOR (Ketua Jurusan tidak bisa akses ini) ---
    Route::middleware(['role:Administrator'])->group(function () {
        // Data User
        Route::get('/admin/users', [DashboardController::class, 'users'])->name('admin.users');
        Route::post('/admin/users', [DashboardController::class, 'storeUser'])->name('admin.users.store');
        Route::put('/admin/users/{id}', [DashboardController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/{id}', [DashboardController::class, 'destroyUser'])->name('admin.users.destroy');

        // Master Data Keperluan (Dipindah ke sini karena hanya Admin yang atur master)
        Route::get('/admin/master/keperluan', [DashboardController::class, 'masterKeperluan'])->name('admin.keperluan');
        Route::post('/admin/master/keperluan', [DashboardController::class, 'storeKeperluan'])->name('admin.keperluan.store');
        Route::put('/admin/master/keperluan/{id}', [DashboardController::class, 'updateKeperluan'])->name('admin.keperluan.update');
        Route::delete('/admin/master/keperluan/{id}', [DashboardController::class, 'destroyKeperluan'])->name('admin.keperluan.destroy');
    });

    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});