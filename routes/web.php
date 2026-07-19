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
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\PemberitahuanController;   

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// =======================================================
// RUTE PUBLIK (BISA DIAKSES SISWA TANPA LOGIN)
// =======================================================
Route::get('/pemberitahuan', [PemberitahuanController::class, 'index'])->name('pemberitahuan.index');
Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
Route::get('/agenda/download/{tanggal}', [AgendaController::class, 'downloadPdf'])->name('agenda.download');
Route::get('/pendaftaran', [\App\Http\Controllers\PendaftaranController::class, 'create'])->name('pendaftaran.create');
Route::post('/pendaftaran', [\App\Http\Controllers\PendaftaranController::class, 'store'])->name('pendaftaran.store');
//Materi Public
// Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
// Route::get('/materi/show/{materi}', [MateriController::class, 'show'])->name('materi.show');

// =======================================================
// RUTE UMUM UNTUK SEMUA ROLE (ADMIN & PENGAJAR BEBAS AKSES)
// =======================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/dashboard/peringkat', [DashboardController::class, 'peringkatDetail'])->name('dashboard.peringkat');
    
    // --- OTP PROFIL ---
    Route::post('/profile/send-otp', [ProfileController::class, 'sendOtp'])->name('profile.sendOtp');
    Route::post('/profile/verify-otp', [ProfileController::class, 'verifyOtp'])->name('profile.verifyOtp');
    
    // === ABSENSI ===
    Route::get('/absensi/scanner', [AbsensiController::class, 'scanner'])->name('absensi.scanner');
    Route::post('/absensi/scan-proses', [AbsensiController::class, 'prosesScan'])->name('absensi.prosesScan');
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/manual', [AbsensiController::class, 'updateManual'])->name('absensi.manual');
    Route::post('/absensi/manual-pengajar', [AbsensiController::class, 'updateManualPengajar'])->name('absensi.manualPengajar');
// Rute untuk fitur Input Grid Cepat
Route::get('/grid', [App\Http\Controllers\AbsensiController::class, 'gridInput'])->name('absensi.grid');
Route::post('/grid', [App\Http\Controllers\AbsensiController::class, 'storeGrid'])->name('absensi.storeGrid');
    // -------------------------------------------------------
    // SEMUA MODUL CRUD (FULL AKSES)
    // -------------------------------------------------------

    // === PENGAJAR ===
    Route::get('/pengajar', [PengajarController::class, 'index'])->name('pengajar.index');
    Route::get('/pengajar/create', [PengajarController::class, 'create'])->name('pengajar.create');
    Route::post('/pengajar', [PengajarController::class, 'store'])->name('pengajar.store');
    Route::get('/pengajar/{pengajar}/edit', [PengajarController::class, 'edit'])->name('pengajar.edit');
    Route::put('/pengajar/{pengajar}', [PengajarController::class, 'update'])->name('pengajar.update');
    Route::delete('/pengajar/{pengajar}', [PengajarController::class, 'destroy'])->name('pengajar.destroy');
    Route::get('/pengajar/{pengajar}', [PengajarController::class, 'show'])->name('pengajar.show');
    Route::get('/pengajar/{pengajar}/histori', [App\Http\Controllers\PengajarController::class, 'histori'])->name('pengajar.histori');
    
    // === KELAS ===
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/create', [KelasController::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    // RUTE BARU DITAMBAHKAN DI SINI (Di atas rute {kelas}/edit)
    Route::get('/kelas-histori', [KelasController::class, 'histori'])->name('kelas.histori'); 
    Route::get('/kelas-histori/rincian', [KelasController::class, 'rincianHistori'])->name('kelas.histori.rincian');
    Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    // === TAHUN AJARAN ===
    // Index dibiarkan terbuka agar pengajar tetap bisa melihat daftar tahun ajaran (opsional)
    Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index'])->name('tahun_ajaran.index');
    
    // Proteksi Rute CRUD khusus untuk Admin menggunakan Middleware AdminOnly milik Anda
    Route::middleware([\App\Http\Middleware\AdminOnly::class])->group(function () {
        Route::get('/tahun-ajaran/create', [TahunAjaranController::class, 'create'])->name('tahun_ajaran.create');
        Route::post('/tahun-ajaran', [TahunAjaranController::class, 'store'])->name('tahun_ajaran.store');
        Route::get('/tahun-ajaran/{tahun_ajaran}/edit', [TahunAjaranController::class, 'edit'])->name('tahun_ajaran.edit');
        Route::put('/tahun-ajaran/{tahun_ajaran}', [TahunAjaranController::class, 'update'])->name('tahun_ajaran.update');
        Route::delete('/tahun-ajaran/{tahun_ajaran}', [TahunAjaranController::class, 'destroy'])->name('tahun_ajaran.destroy');
        Route::patch('/tahun-ajaran/{tahun_ajaran}/aktifkan', [TahunAjaranController::class, 'aktifkan'])->name('tahun_ajaran.aktifkan');
    });

    // === LAPORAN ===
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/cetak-kehadiran-siswa', [LaporanController::class, 'cetakKehadiranSiswa'])->name('laporan.cetakKehadiranSiswa');
    Route::post('/laporan/cetak-agenda', [LaporanController::class, 'cetakAgenda'])->name('laporan.cetakAgenda');
    Route::post('/laporan/cetak-pengajar', [LaporanController::class, 'cetakPengajar'])->name('laporan.cetakPengajar');

    // === LAPORAN INSENTIF===
    Route::resource('laporan_insentif', App\Http\Controllers\LaporanInsentifController::class);
Route::get('/laporan_insentif/{id}/download', [App\Http\Controllers\LaporanInsentifController::class, 'download'])->name('laporan_insentif.download');

    // === PEMBERITAHUAN ===
    Route::get('/pemberitahuan/create', [PemberitahuanController::class, 'create'])->name('pemberitahuan.create');
    Route::post('/pemberitahuan/store', [PemberitahuanController::class, 'store'])->name('pemberitahuan.store');
    Route::get('/pemberitahuan/{pemberitahuan}/edit', [PemberitahuanController::class, 'edit'])->name('pemberitahuan.edit');
    Route::put('/pemberitahuan/{pemberitahuan}', [PemberitahuanController::class, 'update'])->name('pemberitahuan.update');
    Route::delete('/pemberitahuan/{pemberitahuan}', [PemberitahuanController::class, 'destroy'])->name('pemberitahuan.destroy');

    // === AGENDA ===
    Route::get('/agenda/detail/{tanggal}', [AgendaController::class, 'showDate'])->name('agenda.showDate');
    Route::get('/agenda/create', [AgendaController::class, 'create'])->name('agenda.create');
    Route::post('/agenda/store', [AgendaController::class, 'store'])->name('agenda.store');
    Route::delete('/agenda/date/{tanggal}', [AgendaController::class, 'destroyDate'])->name('agenda.destroyDate');
    Route::put('/agenda/detail/{tanggal}/update-pic', [AgendaController::class, 'updatePic'])->name('agenda.updatePic');

    // === MATERI ===
    // Route::get('/materi/create', [MateriController::class, 'create'])->name('materi.create');
    // Route::post('/materi/store', [MateriController::class, 'store'])->name('materi.store');
    // Route::get('/materi/{materi}/edit', [MateriController::class, 'edit'])->name('materi.edit');
    // Route::put('/materi/{materi}', [MateriController::class, 'update'])->name('materi.update');
    // Route::delete('/materi/{materi}', [MateriController::class, 'destroy'])->name('materi.destroy');

    // === SISWA ===
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/create', [SiswaController::class, 'create'])->name('siswa.create');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::get('/siswa/cetak-massal', [SiswaController::class, 'cetakMassal'])->name('siswa.cetakMassal');
    Route::get('/siswa/{siswa}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/{siswa}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
    Route::get('/siswa/{siswa}/cetak-kartu', [SiswaController::class, 'cetakKartu'])->name('siswa.cetakKartu');
    Route::get('/siswa/{siswa}/histori', [SiswaController::class, 'histori'])->name('siswa.histori');
    Route::get('/siswa/ulang-tahun', [App\Http\Controllers\SiswaController::class, 'ulangTahun'])->name('siswa.ulangTahun');
    // TAMBAHKAN DUA RUTE INI
    Route::get('/siswa/cetak-barcode-baru', [App\Http\Controllers\SiswaController::class, 'cetakBarcodeBaru'])->name('siswa.cetakBarcodeBaru');
    Route::put('/histori-siswa/{id}', [SiswaController::class, 'updateHistori'])->name('histori_siswa.update');
    Route::delete('/histori-siswa/{id}', [SiswaController::class, 'destroyHistori'])->name('histori_siswa.destroy');
Route::get('/siswa/cetak-barcode-massal', [\App\Http\Controllers\SiswaController::class, 'cetakBarcodeMassal'])->name('siswa.cetakBarcodeMassal');
Route::get('/siswa/{siswa}/cetak-barcode', [\App\Http\Controllers\SiswaController::class, 'cetakBarcode'])->name('siswa.cetakBarcode');
Route::get('/siswa/cetak-kartu-baru', [\App\Http\Controllers\SiswaController::class, 'cetakKartuBaru'])->name('siswa.cetakKartuBaru');
Route::get('/siswa/{siswa}', [SiswaController::class, 'show'])->name('siswa.show');
    // rute pendaftaran
    Route::get('/kelola-pendaftaran', [\App\Http\Controllers\PendaftaranController::class, 'index'])->name('kelola_pendaftaran.index');
Route::post('/kelola-pendaftaran/{id}/terima', [\App\Http\Controllers\PendaftaranController::class, 'terima'])->name('kelola_pendaftaran.terima');
Route::post('/kelola-pendaftaran/{id}/tolak', [\App\Http\Controllers\PendaftaranController::class, 'tolak'])->name('kelola_pendaftaran.tolak');
Route::get('/kelola-pendaftaran/{id}', [\App\Http\Controllers\PendaftaranController::class, 'show'])->name('kelola_pendaftaran.show');
});

// PERBAIKAN: Rute PUBLIK `show` diletakkan paling bawah di luar middleware Auth
Route::get('/pemberitahuan/{pemberitahuan}', [PemberitahuanController::class, 'show'])->name('pemberitahuan.show');


require __DIR__.'/auth.php';

Route::get('/storage/{path}', function ($path) {
    $filePath = base_path('storage/app/public/' . $path);
    if (file_exists($filePath)) {
        $mimeType = mime_content_type($filePath);
        if (str_starts_with($mimeType, 'image/')) {
            return response()->file($filePath, ['Content-Type' => $mimeType, 'Cache-Control' => 'public, max-age=86400']);
        }
        return response()->download($filePath);
    }
    return "<div><h2 style='color:red;'>Pencarian File Gagal!</h2></div>";
})->where('path', '.*');

Route::get('/link-storage', function () {
    $targetFolder = base_path('storage/app/public');
    $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
    if (!file_exists($linkFolder)) {
        symlink($targetFolder, $linkFolder);
        return "Symlink berhasil dibuat!";
    }
    return "Symlink sudah ada.";
});