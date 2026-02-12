<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
// Import Controller Anda
use App\Http\Controllers\GuestController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. HALAMAN PENGUNJUNG (Public) ---
// Halaman Landing utama
Route::get('/', [GuestController::class, 'index'])->name('guest.landing');

// Alur Pengisian Buku Tamu
Route::get('/isi-tamu', [GuestController::class, 'formKunjungan'])->name('guest.form');
Route::post('/simpan-tamu', [GuestController::class, 'storeKunjungan'])->name('guest.store');

// Fitur Auto-fill (Cek NIM/NIP via Ajax)
Route::get('/cek-pengunjung', [GuestController::class, 'checkVisitor'])->name('guest.check');
Route::get('/kunjungan/konfirmasi/{id}', [GuestController::class, 'halamanKonfirmasi'])->name('guest.konfirmasi');

// Alur Survey (Hanya bisa diakses setelah isi buku tamu)
Route::get('/survey/{id}', [GuestController::class, 'formSurvey'])->name('guest.survey');
Route::post('/survey-simpan/{id}', [GuestController::class, 'storeSurvey'])->name('guest.survey.store');


// --- 2. AUTHENTICATION ---
Auth::routes(['register' => false]);

// --- 3. HALAMAN ADMINISTRATOR & KETUA JURUSAN ---
Route::middleware(['auth'])->group(function () {
    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Data Kunjungan
    Route::get('/admin/kunjungan', [DashboardController::class, 'kunjungan'])->name('admin.kunjungan');
    Route::post('/admin/kunjungan', [DashboardController::class, 'storeKunjungan'])->name('admin.kunjungan.store');
    Route::put('/admin/kunjungan/{id}', [DashboardController::class, 'updateKunjungan'])->name('admin.kunjungan.update');
    Route::delete('/admin/kunjungan/{id}', [DashboardController::class, 'destroyKunjungan'])->name('admin.kunjungan.destroy'); // Pastikan route ini ada untuk hapus kunjungan

    // === PERBAIKAN DI SINI (Data Survey) ===
    // Mengubah name menjadi admin.survey.index agar sesuai dengan form search di View
    Route::get('/admin/survey', [DashboardController::class, 'survey'])->name('admin.survey'); 
    // Menambahkan route update
    Route::put('/admin/survey/{id}', [DashboardController::class, 'updateSurvey'])->name('admin.survey.update');
    // Menambahkan route delete (destroy)
    Route::delete('/admin/survey/{id}', [DashboardController::class, 'destroySurvey'])->name('admin.survey.destroy');

    // Rute untuk menampilkan data pengunjung
    Route::get('/admin/pengunjung', [DashboardController::class, 'pengunjung'])->name('admin.pengunjung');

    // TAMBAHKAN DUA BARIS INI:
    Route::put('/admin/pengunjung/{id}', [DashboardController::class, 'updatePengunjung'])->name('admin.pengunjung.update');
    Route::delete('/admin/pengunjung/{id}', [DashboardController::class, 'destroyPengunjung'])->name('admin.pengunjung.destroy');

    // Data Pengunjung (Master)
    // Route::get('/admin/pengunjung', [DashboardController::class, 'pengunjung'])->name('admin.pengunjung');

    // // Master Data Keperluan
    // Route::get('/admin/master/keperluan', [DashboardController::class, 'masterKeperluan'])->name('admin.keperluan');
    // Route::post('/admin/master/keperluan', [DashboardController::class, 'storeKeperluan'])->name('admin.keperluan.store');

    // Menampilkan halaman master keperluan
    Route::get('/admin/master/keperluan', [DashboardController::class, 'masterKeperluan'])->name('admin.keperluan');

    // Tambahkan rute aksi ini:
    Route::post('/admin/master/keperluan', [DashboardController::class, 'storeKeperluan'])->name('admin.keperluan.store');
    Route::put('/admin/master/keperluan/{id}', [DashboardController::class, 'updateKeperluan'])->name('admin.keperluan.update');
    Route::delete('/admin/master/keperluan/{id}', [DashboardController::class, 'destroyKeperluan'])->name('admin.keperluan.destroy');

    // Laporan
    Route::get('/admin/laporan', [DashboardController::class, 'laporan'])->name('admin.laporan');
    Route::post('/admin/laporan/export', [DashboardController::class, 'exportLaporan'])->name('admin.laporan.export');
    
    // Data User (Hanya Admin)
    Route::middleware(['role:Administrator'])->group(function () {
        Route::get('/admin/users', [DashboardController::class, 'users'])->name('admin.users');
        Route::post('/admin/users', [DashboardController::class, 'storeUser'])->name('admin.users.store');
        Route::put('/admin/users/{id}', [DashboardController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/{id}', [DashboardController::class, 'destroyUser'])->name('admin.users.destroy');
    });

    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});