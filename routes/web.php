<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KaryawanController; // Karyawan (employee-facing)
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KaryawanController as AdminKaryawanController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
use App\Http\Controllers\Admin\GajiController as AdminGajiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->isKaryawan()) {
            return redirect()->route('karyawan.dashboard');
        }
    }
    return redirect()->route('login');
})->name('home');


// Authentication Routes
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// Karyawan Routes
Route::middleware(['auth', 'karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('dashboard', [KaryawanController::class, 'dashboard'])->name('dashboard');
    Route::post('presensi/masuk', [KaryawanController::class, 'presensiMasuk'])->name('presensi.masuk');
    Route::post('presensi/pulang', [KaryawanController::class, 'presensiPulang'])->name('presensi.pulang');
    Route::get('riwayat-absensi', [KaryawanController::class, 'riwayatAbsensi'])->name('riwayat.absensi');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Kelola Karyawan (Admin)
    Route::resource('karyawan', AdminKaryawanController::class)->except(['show']);

    // Rekap Absensi (Admin)
    Route::get('absensi', [AdminAbsensiController::class, 'index'])->name('absensi.index');
    Route::get('absensi/export', [AdminAbsensiController::class, 'exportExcel'])->name('absensi.export'); // Tambahan


    // Penggajian (Admin)
    Route::get('gaji', [AdminGajiController::class, 'index'])->name('gaji.index');
    Route::get('gaji/hitung', [AdminGajiController::class, 'showHitungForm'])->name('gaji.show_hitung_form');
    Route::post('gaji/hitung', [AdminGajiController::class, 'hitungDanSimpanGaji'])->name('gaji.hitung');
    Route::get('gaji/{gaji}/slip', [AdminGajiController::class, 'cetakSlip'])->name('gaji.slip');
    Route::post('gaji/{gaji}/update-status', [AdminGajiController::class, 'updateStatusPembayaran'])->name('gaji.update_status'); // Tambahan
});