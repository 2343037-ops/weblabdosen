<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\AdminController;

// Halaman Publik (Mahasiswa — tanpa login)
Route::get('/', [DashboardController::class, 'publicPage'])->name('public');
Route::get('/api/dosen-status', [DashboardController::class, 'apiStatus']);

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dosen (auth required)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/update-status', [DashboardController::class, 'updateStatus'])->name('dosen.updateStatus');
    Route::post('/update-profil', [DashboardController::class, 'updateProfil'])->name('dosen.updateProfil');

    // Jadwal Mingguan
    Route::post('/jadwal-mingguan', [JadwalController::class, 'storeMingguan'])->name('jadwal.mingguan.store');
    Route::put('/jadwal-mingguan/{id}', [JadwalController::class, 'updateMingguan'])->name('jadwal.mingguan.update');
    Route::delete('/jadwal-mingguan/{id}', [JadwalController::class, 'destroyMingguan'])->name('jadwal.mingguan.destroy');

    // Jadwal Akan Datang
    Route::post('/jadwal-akan-datang', [JadwalController::class, 'storeAkanDatang'])->name('jadwal.akan-datang.store');
    Route::put('/jadwal-akan-datang/{id}', [JadwalController::class, 'updateAkanDatang'])->name('jadwal.akan-datang.update');
    Route::delete('/jadwal-akan-datang/{id}', [JadwalController::class, 'destroyAkanDatang'])->name('jadwal.akan-datang.destroy');

    // Jadwal Dadakan
    Route::post('/jadwal-dadakan', [JadwalController::class, 'storeDadakan'])->name('jadwal.dadakan.store');
    Route::put('/jadwal-dadakan/{id}', [JadwalController::class, 'updateDadakan'])->name('jadwal.dadakan.update');
    Route::delete('/jadwal-dadakan/{id}', [JadwalController::class, 'destroyDadakan'])->name('jadwal.dadakan.destroy');
});

// Admin (auth + admin required)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/dosen', [AdminController::class, 'storeDosen'])->name('admin.dosen.store');
    Route::put('/dosen/{id}', [AdminController::class, 'updateDosen'])->name('admin.dosen.update');
    Route::delete('/dosen/{id}', [AdminController::class, 'destroyDosen'])->name('admin.dosen.destroy');

    // Kelola Jadwal Dosen oleh Admin
    Route::get('/dosen/{id}/jadwal', [AdminController::class, 'manageJadwal'])->name('admin.dosen.jadwal');
    
    // Admin Jadwal Mingguan
    Route::post('/dosen/{id}/jadwal-mingguan', [AdminController::class, 'storeMingguan'])->name('admin.jadwal.mingguan.store');
    Route::put('/dosen/{id}/jadwal-mingguan/{jadwalId}', [AdminController::class, 'updateMingguan'])->name('admin.jadwal.mingguan.update');
    Route::delete('/dosen/{id}/jadwal-mingguan/{jadwalId}', [AdminController::class, 'destroyMingguan'])->name('admin.jadwal.mingguan.destroy');

    // Admin Jadwal Akan Datang
    Route::post('/dosen/{id}/jadwal-akan-datang', [AdminController::class, 'storeAkanDatang'])->name('admin.jadwal.akan-datang.store');
    Route::put('/dosen/{id}/jadwal-akan-datang/{jadwalId}', [AdminController::class, 'updateAkanDatang'])->name('admin.jadwal.akan-datang.update');
    Route::delete('/dosen/{id}/jadwal-akan-datang/{jadwalId}', [AdminController::class, 'destroyAkanDatang'])->name('admin.jadwal.akan-datang.destroy');

    // Admin Jadwal Dadakan
    Route::post('/dosen/{id}/jadwal-dadakan', [AdminController::class, 'storeDadakan'])->name('admin.jadwal.dadakan.store');
    Route::put('/dosen/{id}/jadwal-dadakan/{jadwalId}', [AdminController::class, 'updateDadakan'])->name('admin.jadwal.dadakan.update');
    Route::delete('/dosen/{id}/jadwal-dadakan/{jadwalId}', [AdminController::class, 'destroyDadakan'])->name('admin.jadwal.dadakan.destroy');
});
