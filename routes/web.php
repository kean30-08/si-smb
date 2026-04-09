<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\PengajarController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RefleksiController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// =======================================================
// RUTE PUBLIK (BISA DIAKSES SISWA TANPA LOGIN)
// =======================================================

// Agenda (View & Download)
Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
Route::get('/agenda/detail/{tanggal}', [AgendaController::class, 'showDate'])->name('agenda.showDate');
Route::get('/agenda/download/{tanggal}', [AgendaController::class, 'downloadPdf'])->name('agenda.download');

// Materi (View Only)
Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
Route::get('/materi/show/{materi}', [MateriController::class, 'show'])->name('materi.show');

// Refleksi Form
Route::get('/refleksi/{tanggal}', [RefleksiController::class, 'create'])->name('refleksi.create');
Route::post('/refleksi/{tanggal}', [RefleksiController::class, 'store'])->name('refleksi.store');


// =======================================================
// RUTE KHUSUS ADMIN & KEPALA SEKOLAH
// =======================================================
Route::middleware(['auth', \App\Http\Middleware\AdminOnly::class])->group(function () {

    // === AGENDA (Create, Update, Delete, Broadcast) ===
    // PASTIKAN NAMA RUTE SESUAI DAN TIDAK BENTROK
    Route::get('/admin/agenda/create', [AgendaController::class, 'create'])->name('agenda.create');
    Route::post('/admin/agenda/store', [AgendaController::class, 'store'])->name('agenda.store');
    Route::get('/admin/agenda/{agenda}/edit', [AgendaController::class, 'edit'])->name('agenda.edit');
    Route::put('/admin/agenda/{agenda}', [AgendaController::class, 'update'])->name('agenda.update');
    Route::delete('/admin/agenda/{agenda}', [AgendaController::class, 'destroy'])->name('agenda.destroy');
    Route::delete('/admin/agenda/date/{tanggal}', [AgendaController::class, 'destroyDate'])->name('agenda.destroyDate');
    
    // Fitur Tambahan Agenda
    Route::post('/admin/agenda/broadcast/{tanggal}', [AgendaController::class, 'broadcastPdf'])->name('agenda.broadcast');
    Route::get('/admin/agenda/detail/{tanggal}/tambah', [AgendaController::class, 'createDetail'])->name('agenda.createDetail');
    Route::post('/admin/agenda/detail/store', [AgendaController::class, 'storeDetail'])->name('agenda.storeDetail');
    Route::put('/admin/agenda/detail/{tanggal}/update-pic', [AgendaController::class, 'updatePic'])->name('agenda.updatePic');


    // === PENGAJAR ===
    Route::get('/pengajar/create', [PengajarController::class, 'create'])->name('pengajar.create');
    Route::post('/pengajar', [PengajarController::class, 'store'])->name('pengajar.store');
    Route::get('/pengajar/{pengajar}/edit', [PengajarController::class, 'edit'])->name('pengajar.edit');
    Route::put('/pengajar/{pengajar}', [PengajarController::class, 'update'])->name('pengajar.update');
    Route::delete('/pengajar/{pengajar}', [PengajarController::class, 'destroy'])->name('pengajar.destroy');


    // === SISWA ===
    Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::get('/siswa/{siswa}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/{siswa}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
    Route::get('/siswa/{siswa}/cetak-kartu', [SiswaController::class, 'cetakKartu'])->name('siswa.cetakKartu');


    // === KELAS ===
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');


    // === LAPORAN ===
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/cetak-kehadiran-siswa', [LaporanController::class, 'cetakKehadiranSiswa'])->name('laporan.cetakKehadiranSiswa');
    Route::post('/laporan/cetak-agenda', [LaporanController::class, 'cetakAgenda'])->name('laporan.cetakAgenda');
    Route::post('/laporan/cetak-pengajar', [LaporanController::class, 'cetakPengajar'])->name('laporan.cetakPengajar');


    // === MATERI (CRUD) ===
    Route::get('/admin/materi/create', [MateriController::class, 'create'])->name('materi.create');
    Route::post('/admin/materi/store', [MateriController::class, 'store'])->name('materi.store');
    Route::get('/admin/materi/{materi}/edit', [MateriController::class, 'edit'])->name('materi.edit');
    Route::put('/admin/materi/{materi}', [MateriController::class, 'update'])->name('materi.update');
    Route::delete('/admin/materi/{materi}', [MateriController::class, 'destroy'])->name('materi.destroy');
});


// =======================================================
// RUTE UMUM UNTUK SEMUA ROLE (HANYA VIEW & DOWNLOAD)
// =======================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // === AGENDA REFLEKSI (Read Only) ===
    Route::get('/agenda/detail/{tanggal}/refleksi', [RefleksiController::class, 'index'])->name('refleksi.index');
    Route::get('/refleksi/detail/{id}', [RefleksiController::class, 'show'])->name('refleksi.show');

    // === ABSENSI ===
    Route::get('/absensi/scanner', [AbsensiController::class, 'scanner'])->name('absensi.scanner');
    Route::post('/absensi/scan-proses', [AbsensiController::class, 'prosesScan'])->name('absensi.prosesScan');
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/manual', [AbsensiController::class, 'updateManual'])->name('absensi.manual');
    Route::post('/absensi/manual-pengajar', [AbsensiController::class, 'updateManualPengajar'])->name('absensi.manualPengajar');

    // === SISWA (View) ===
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/{siswa}', [SiswaController::class, 'show'])->name('siswa.show');

    // === PENGAJAR (View) ===
    Route::get('/pengajar', [PengajarController::class, 'index'])->name('pengajar.index');
    Route::get('/pengajar/{pengajar}', [PengajarController::class, 'show'])->name('pengajar.show');
});

require __DIR__.'/auth.php';