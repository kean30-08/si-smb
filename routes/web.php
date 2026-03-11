<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\PengajarController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AbsensiController; // Saya tambahkan import ini agar rapi
use App\Http\Controllers\DashboardController; // Saya tambahkan import ini agar rapi

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ==========================================================
// 1. RUTE KHUSUS ADMIN (DITARUH DI ATAS AGAR CREATE TIDAK TERTUMPUK)
// ==========================================================
Route::middleware(['auth', \App\Http\Middleware\AdminOnly::class])->group(function () {

    // Agenda broadcast
    Route::post('/agenda/broadcast/{tanggal}', [AgendaController::class, 'broadcastPdf'])->name('agenda.broadcast');

    // Pengajar (Create, Store, Edit, Update, Destroy ditaruh di sini)
    Route::get('/pengajar/create', [PengajarController::class, 'create'])->name('pengajar.create');
    Route::post('/pengajar', [PengajarController::class, 'store'])->name('pengajar.store');
    Route::get('/pengajar/{pengajar}/edit', [PengajarController::class, 'edit'])->name('pengajar.edit');
    Route::put('/pengajar/{pengajar}', [PengajarController::class, 'update'])->name('pengajar.update');
    Route::delete('/pengajar/{pengajar}', [PengajarController::class, 'destroy'])->name('pengajar.destroy');

    // Siswa (Create ditaruh di atas)
    Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::get('/siswa/{siswa}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/{siswa}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
    Route::get('/siswa/{siswa}/cetak-kartu', [SiswaController::class, 'cetakKartu'])->name('siswa.cetakKartu');

    // Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{kela}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{kela}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{kela}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/cetak-kehadiran-siswa', [LaporanController::class, 'cetakKehadiranSiswa'])->name('laporan.cetakKehadiranSiswa');
    Route::post('/laporan/cetak-agenda', [LaporanController::class, 'cetakAgenda'])->name('laporan.cetakAgenda');
    Route::post('/laporan/cetak-pengajar', [LaporanController::class, 'cetakPengajar'])->name('laporan.cetakPengajar');

    // Materi
    Route::get('/materi/create', [MateriController::class, 'create'])->name('materi.create');
    Route::post('/materi', [MateriController::class, 'store'])->name('materi.store');
    Route::get('/materi/{materi}/edit', [MateriController::class, 'edit'])->name('materi.edit');
    Route::put('/materi/{materi}', [MateriController::class, 'update'])->name('materi.update');
    Route::delete('/materi/{materi}', [MateriController::class, 'destroy'])->name('materi.destroy');
});


// ==========================================================
// 2. RUTE UMUM UNTUK SEMUA ROLE (ADMIN & PENGAJAR)
// ==========================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Agenda
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
    Route::get('/agenda/detail/{tanggal}', [AgendaController::class, 'showDate'])->name('agenda.showDate');
    Route::get('/agenda/detail/{tanggal}/tambah', [AgendaController::class, 'createDetail'])->name('agenda.createDetail');
    Route::post('/agenda/detail/store', [AgendaController::class, 'storeDetail'])->name('agenda.storeDetail');
    Route::get('/agenda/download/{tanggal}', [AgendaController::class, 'downloadPdf'])->name('agenda.download');
    Route::get('/agenda/create', [AgendaController::class, 'create'])->name('agenda.create');
    Route::post('/agenda', [AgendaController::class, 'store'])->name('agenda.store');
    Route::get('/agenda/{agenda}/edit', [AgendaController::class, 'edit'])->name('agenda.edit');
    Route::put('/agenda/{agenda}', [AgendaController::class, 'update'])->name('agenda.update');
    Route::delete('/agenda/{agenda}', [AgendaController::class, 'destroy'])->name('agenda.destroy');

    // Absensi
    Route::get('/absensi/scanner', [AbsensiController::class, 'scanner'])->name('absensi.scanner');
    Route::post('/absensi/scan-proses', [AbsensiController::class, 'prosesScan'])->name('absensi.prosesScan');
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/manual', [AbsensiController::class, 'updateManual'])->name('absensi.manual');
    Route::post('/absensi/manual-pengajar', [AbsensiController::class, 'updateManualPengajar'])->name('absensi.manualPengajar');

    // Siswa (Index & Show ditaruh di bawah agar tidak konflik)
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/{siswa}', [SiswaController::class, 'show'])->name('siswa.show');

    // Pengajar (Index & Show ditaruh di bawah agar tidak konflik)
    Route::get('/pengajar', [PengajarController::class, 'index'])->name('pengajar.index');
    Route::get('/pengajar/{pengajar}', [PengajarController::class, 'show'])->name('pengajar.show');

    // Materi
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
});

require __DIR__.'/auth.php';