<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\PengajarController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\LaporanController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    //rute tab lain
    // Rute untuk menampilkan jadwal per tanggal (Group)
    Route::get('/agenda', [App\Http\Controllers\AgendaController::class, 'index'])->name('agenda.index');

    //absensi
    Route::get('/absensi/scanner', [App\Http\Controllers\AbsensiController::class, 'scanner'])->name('absensi.scanner');
    Route::post('/absensi/scan-proses', [App\Http\Controllers\AbsensiController::class, 'prosesScan'])->name('absensi.prosesScan');
    // Nanti untuk halaman admin mengelola data absen manual
    Route::get('/absensi', [App\Http\Controllers\AbsensiController::class, 'index'])->name('absensi.index');
    // Rute untuk mengubah status absen secara manual (Izin/Sakit/Hadir/Alpa)
    Route::post('/absensi/manual', [App\Http\Controllers\AbsensiController::class, 'updateManual'])->name('absensi.manual');

    // Rute khusus untuk Detail, Tambah Detail, dan Broadcast PDF
    Route::get('/agenda/detail/{tanggal}', [App\Http\Controllers\AgendaController::class, 'showDate'])->name('agenda.showDate');
    Route::get('/agenda/detail/{tanggal}/tambah', [App\Http\Controllers\AgendaController::class, 'createDetail'])->name('agenda.createDetail');
    Route::post('/agenda/detail/store', [App\Http\Controllers\AgendaController::class, 'storeDetail'])->name('agenda.storeDetail');
    Route::post('/agenda/broadcast/{tanggal}', [App\Http\Controllers\AgendaController::class, 'broadcastPdf'])->name('agenda.broadcast');
    // Rute untuk mengunduh PDF ke perangkat (Download Manual)
    Route::get('/agenda/download/{tanggal}', [App\Http\Controllers\AgendaController::class, 'downloadPdf'])->name('agenda.download');

    // Rute bawaan (Create, Store, Edit, Update, Destroy)
    Route::get('/agenda/create', [App\Http\Controllers\AgendaController::class, 'create'])->name('agenda.create');
    Route::post('/agenda', [App\Http\Controllers\AgendaController::class, 'store'])->name('agenda.store');
    Route::get('/agenda/{agenda}/edit', [App\Http\Controllers\AgendaController::class, 'edit'])->name('agenda.edit');
    Route::put('/agenda/{agenda}', [App\Http\Controllers\AgendaController::class, 'update'])->name('agenda.update');
    Route::delete('/agenda/{agenda}', [App\Http\Controllers\AgendaController::class, 'destroy'])->name('agenda.destroy');

    //pengajar
    Route::get('/pengajar', [PengajarController::class, 'index'])->name('pengajar.index');
    Route::get('/pengajar/create', [PengajarController::class, 'create'])->name('pengajar.create');
    Route::post('/pengajar', [PengajarController::class, 'store'])->name('pengajar.store');
    Route::get('/pengajar/{pengajar}', [PengajarController::class, 'show'])->name('pengajar.show');
    Route::get('/pengajar/{pengajar}/edit', [PengajarController::class, 'edit'])->name('pengajar.edit');
    Route::put('/pengajar/{pengajar}', [PengajarController::class, 'update'])->name('pengajar.update');
    Route::delete('/pengajar/{pengajar}', [PengajarController::class, 'destroy'])->name('pengajar.destroy');

    //siswa
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::get('/siswa/{siswa}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/{siswa}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
    Route::get('/siswa/{siswa}', [SiswaController::class, 'show'])->name('siswa.show');

    //kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{kela}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{kela}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{kela}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    //materi
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::resource('materi', App\Http\Controllers\MateriController::class);

    //laporan
    Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/cetak-siswa', [App\Http\Controllers\LaporanController::class, 'cetakSiswa'])->name('laporan.cetakSiswa');
    Route::post('/laporan/cetak-agenda', [App\Http\Controllers\LaporanController::class, 'cetakAgenda'])->name('laporan.cetakAgenda');
});

require __DIR__.'/auth.php';
