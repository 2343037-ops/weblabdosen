<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalController;

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
