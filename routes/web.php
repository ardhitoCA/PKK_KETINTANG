<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard (if auth) or login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Universal Dashboard (disesuaikan otomatis sesuai role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // === HAK AKSES ANGGOTA ===
    Route::middleware('role:anggota,bendahara,ketua')->group(function () {
        Route::get('/pengajuan-barang', [BarangController::class, 'pengajuanForm'])->name('barang.pengajuan');
        Route::post('/pengajuan-barang', [BarangController::class, 'storePengajuan'])->name('barang.pengajuan.store');
        Route::get('/riwayat-saya', [BarangController::class, 'riwayatSaya'])->name('barang.riwayat_saya');
    });

    // === HAK AKSES KETUA PKK ===
    Route::middleware('role:ketua,bendahara')->group(function () {
        Route::get('/acc-barang', [BarangController::class, 'antreanAcc'])->name('barang.acc_antrean');
        Route::post('/acc-barang/{barang}', [BarangController::class, 'prosesAcc'])->name('barang.proses_acc');
        Route::post('/tolak-barang/{barang}', [BarangController::class, 'tolakAcc'])->name('barang.tolak_acc');
    });

    // === PENGATURAN QRIS TOKO (Khusus Ketua PKK) ===
    Route::middleware('role:ketua')->group(function () {
        Route::get('/pengaturan-qris', [PengaturanController::class, 'qrisIndex'])->name('pengaturan.qris');
        Route::post('/pengaturan-qris', [PengaturanController::class, 'updateQris'])->name('pengaturan.qris.update');
        Route::post('/pengaturan-qris/reset', [PengaturanController::class, 'resetQris'])->name('pengaturan.qris.reset');
    });

    // === HAK AKSES BENDAHARA (ADMIN) ===
    Route::middleware('role:bendahara')->group(function () {
        // Kasir Penjualan
        Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
        Route::post('/kasir/checkout', [KasirController::class, 'checkout'])->name('kasir.checkout');
        Route::get('/kasir/sukses/{transaksi}', [KasirController::class, 'sukses'])->name('kasir.sukses');

        // Kelola Stok Titipan
        Route::get('/kelola-stok', [BarangController::class, 'kelolaStok'])->name('barang.kelola_stok');
        Route::post('/kelola-stok/{barang}/tambah', [BarangController::class, 'tambahStok'])->name('barang.tambah_stok');

        // Retur Barang
        Route::get('/retur-barang', [BarangController::class, 'returIndex'])->name('barang.retur');
        Route::post('/retur-barang/{barang}', [BarangController::class, 'prosesRetur'])->name('barang.proses_retur');

        // Arus Kas Operasional
        Route::get('/kas', [KasController::class, 'index'])->name('kas.index');
        Route::get('/kas/tambah', [KasController::class, 'create'])->name('kas.create');
        Route::post('/kas', [KasController::class, 'store'])->name('kas.store');

        // Rekap Bagi Hasil UMKM
        Route::get('/rekap-bagi-hasil', [LaporanController::class, 'rekapBagiHasil'])->name('laporan.bagi_hasil');
        Route::post('/rekap-bagi-hasil/cairkan', [LaporanController::class, 'prosesCairkanUang'])->name('laporan.cairkan');
    });

    // === LAPORAN KEUANGAN (Ketua & Bendahara) ===
    Route::middleware('role:bendahara,ketua')->group(function () {
        Route::get('/laporan-keuangan', [LaporanController::class, 'laporanKeuangan'])->name('laporan.keuangan');
        Route::get('/laporan-keuangan/cetak', [LaporanController::class, 'cetakLaporan'])->name('laporan.cetak');
    });
});
