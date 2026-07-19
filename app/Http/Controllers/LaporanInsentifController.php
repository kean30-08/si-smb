<?php

namespace App\Http\Controllers;

use App\Models\LaporanInsentif;
use App\Models\Agenda;
use App\Models\Siswa;
use App\Models\Pengajar;
use App\Models\TahunAjaran;
use App\Models\HistoriSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanInsentifController extends Controller
{
    public function index()
    {
        $laporans = LaporanInsentif::with('pengajar')->latest()->paginate(10);
        return view('laporan_insentif.index', compact('laporans'));
    }

    public function create()
    {
        return view('laporan_insentif.create'); 
    }

    public function store(Request $request)
    {
        // 1. Naikkan Limit agar PDF tidak gagal saat menyusun banyak gambar
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $request->validate([
            'bulan' => 'required',
            'ttd_pengajar' => 'required|image|mimes:jpeg,png,jpg|max:2048', // TAMBAHAN: Validasi maksimal 2MB
            'dokumentasi.*' => 'required|image|mimes:jpeg,png,jpg|max:5120' 
        ]);

        [$year, $month] = explode('-', $request->bulan);
        $namaBulan = Carbon::createFromFormat('Y-m', $request->bulan)->translatedFormat('F Y');

        // 2. Dapatkan Pengajar yang sedang login sebagai Penyusun
        $user = auth()->user();
        $pengajar = Pengajar::where('user_id', $user->id)->first();
        if (!$pengajar) return back()->with('error', 'Gagal! Akun Anda belum terikat dengan profil Pengajar.');

        // 3. Tarik Data Agenda
        $agendas = Agenda::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->orderBy('tanggal', 'asc')
            ->get();

        if ($agendas->isEmpty()) return back()->with('error', 'Tidak ada jadwal kegiatan (Agenda) pada bulan tersebut.');

        // Hitung jumlah peserta hadir per agenda
        foreach ($agendas as $agenda) {
            $agenda->jumlah_hadir = \App\Models\Absensi::where('agenda_id', $agenda->id)->where('status_kehadiran', 'hadir')->count();
        }

        $ta_ids = $agendas->pluck('tahun_ajaran_id')->filter()->unique()->toArray();
        $namaTAs = TahunAjaran::whereIn('id', $ta_ids)->pluck('tahun_ajaran')->toArray();
        $nama_ta = empty($namaTAs) ? '-' : implode(' & ', $namaTAs);
        
        $agendaStatusMap = $agendas->pluck('is_libur', 'tanggal')->toArray();

        // 4. Tarik Data Absensi Siswa
        $validSiswaIds = HistoriSiswa::whereIn('tahun_ajaran_id', $ta_ids)->pluck('siswa_id')->unique();
        $siswas = Siswa::with(['riwayatHistori' => function ($q) use ($ta_ids) {
            $q->whereIn('tahun_ajaran_id', $ta_ids);
        }, 'riwayatHistori.kelas', 'absensi' => function($q) use ($agendas) {
            $q->whereIn('agenda_id', $agendas->pluck('id'));
        }])->whereIn('id', $validSiswaIds)->get();

        foreach ($siswas as $siswa) {
            $histori = $siswa->riwayatHistori->first();
            $siswa->kelas_laporan = $histori && $histori->kelas ? $histori->kelas->nama_kelas : '-';
            $map = [];
            foreach ($siswa->absensi as $absen) { $map[$absen->agenda_id] = $absen->status_kehadiran; }
            $siswa->absen_map = $map;
        }

        $urutanKelas = ['Kelas PG' => 1, 'Kelas TK A' => 2, 'Kelas TK B' => 3, 'Kelas 1 SD' => 4, 'Kelas 2 SD' => 5, 'Kelas 3 SD' => 6, 'Kelas 4 SD' => 7, 'Kelas 5 SD' => 8, 'Kelas 6 SD' => 9, 'Kelas 1 SMP' => 10, 'Kelas 2 SMP' => 11, 'Kelas 3 SMP' => 12, 'Kelas 1 SMA' => 13, 'Kelas 2 SMA' => 14, 'Kelas 3 SMA' => 15];
        $siswas = $siswas->sortBy(function($s) use ($urutanKelas) {
            $namaKls = $s->kelas_laporan;
            return str_pad($urutanKelas[$namaKls] ?? 99, 2, '0', STR_PAD_LEFT) . '-' . $s->nama_lengkap;
        })->values();

        // 5. Tarik Data Absensi Guru (Hanya untuk Penyusun Laporan)
        $absenPengajars = \App\Models\AbsensiPengajar::where('pengajar_id', $pengajar->id)
            ->whereIn('agenda_id', $agendas->pluck('id'))
            ->get()->pluck('status_kehadiran', 'agenda_id')->toArray();

            // CARI KEPALA SEKOLAH AKTIF
        $kepalaSekolah = Pengajar::whereHas('jabatan', function($q) {
            $q->where('nama_jabatan', 'like', '%Kepala Sekolah%');
        })->where('status', 'aktif')->first();

        $namaKepalaSekolah = $kepalaSekolah ? $kepalaSekolah->nama_lengkap : '...............................';

        // 6. PROSES GAMBAR: Convert ke Base64 JPG dengan kompresi agar PDF tidak limit
        $base64Images = [];
        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                // Membaca string file secara raw
                $imgStr = file_get_contents($file->getRealPath());
                $img = @imagecreatefromstring($imgStr);
                
                if ($img !== false) {
                    $width = imagesx($img);
                    $height = imagesy($img);
                    
                    // Resize maksimal lebar 700px untuk menghemat ukuran PDF
                    $newWidth = 700;
                    $newHeight = floor($height * ($newWidth / $width));
                    
                    $tmpImg = imagecreatetruecolor($newWidth, $newHeight);
                    imagecopyresampled($tmpImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    
                    ob_start();
                    imagejpeg($tmpImg, null, 65); // Kompres kualitas 65% (Standar web/pdf ringan)
                    $imgData = ob_get_clean();
                    
                    $base64Images[] = 'data:image/jpeg;base64,' . base64_encode($imgData);
                    
                    imagedestroy($img);
                    imagedestroy($tmpImg);
                }
            }
        }

// 6.5. PROSES TANDA TANGAN (KEPSEK STATIS & PENGAJAR DINAMIS)
        
        // A. TTD Pengajar (Dari Upload)
        $base64TtdPengajar = '';
        if ($request->hasFile('ttd_pengajar')) {
            $ttdFile = $request->file('ttd_pengajar');
            $ttdExt = $ttdFile->getClientOriginalExtension();
            $base64TtdPengajar = 'data:image/' . $ttdExt . ';base64,' . base64_encode(file_get_contents($ttdFile->getRealPath()));
        }

        // B. TTD Kepala Sekolah (Statis dari public folder)
        $pathTtdKepsek = public_path('img/ttd_kepsek.jpg');
        $base64TtdKepsek = '';
        if (file_exists($pathTtdKepsek)) {
            $base64TtdKepsek = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($pathTtdKepsek));
        }

        // 7. Generate PDF
        $pdf = Pdf::loadView('laporan_insentif.pdf_template', compact(
            'namaBulan', 'year', 'pengajar', 'agendas', 'siswas', 'nama_ta', 'absenPengajars', 'base64Images', 'agendaStatusMap', 'namaKepalaSekolah', 'base64TtdPengajar', 'base64TtdKepsek'
        ));
        $pdf->setPaper('A4', 'portrait');
        
        // 8. Simpan ke Storage Lokal Server
        $fileName = 'Laporan_Insentif_' . str_replace(' ', '_', $pengajar->nama_lengkap) . '_' . $namaBulan . '_' . time() . '.pdf';
        $filePath = 'laporan_insentif/' . $fileName;
        
        Storage::disk('public')->put($filePath, $pdf->output());

        LaporanInsentif::create([
            'pengajar_id' => $pengajar->id,
            'bulan' => $month,
            'tahun' => $year,
            'file_path' => $filePath
        ]);

        return redirect()->route('laporan_insentif.index')->with('success', 'Dokumen Laporan Insentif berhasil dihasilkan dan diarsipkan.');
    }

    public function download($id)
    {
        $laporan = LaporanInsentif::findOrFail($id);
        if ($laporan->file_path && Storage::disk('public')->exists($laporan->file_path)) {
            return Storage::disk('public')->download($laporan->file_path);
        }
        return back()->with('error', 'File PDF tidak ditemukan di server.');
    }

    public function destroy($id)
    {
        $laporan = LaporanInsentif::findOrFail($id);
        if ($laporan->file_path && Storage::disk('public')->exists($laporan->file_path)) {
            Storage::disk('public')->delete($laporan->file_path);
        }
        $laporan->delete();
        return back()->with('success', 'Riwayat laporan insentif berhasil dihapus.');
    }
}