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
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    //rute tab lain
    //agenda
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');

    //pengajar
    Route::get('/pengajar', [PengajarController::class, 'index'])->name('pengajar.index');

    //siswa
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');

    //kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');

    //materi
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');

    //laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
});

require __DIR__.'/auth.php';
