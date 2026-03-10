<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Agenda;
use App\Models\Pengajar;
use App\Models\Absensi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        $kelas = Kelas::all();
        return view('laporan.index', compact('kelas'));
    }

    // 1. LAPORAN REKAPITULASI KEAKTIFAN SISWA (APRESIASI)
    public function cetakKehadiranSiswa(Request $request)
    {
        ini_set('memory_limit', '256M');
        
        $mulai = $request->tanggal_mulai;
        $selesai = $request->tanggal_selesai;
        $kelas_id = $request->kelas_id;

        // 1. Cari total JADWAL (TANGGAL UNIK) pada rentang waktu tersebut
        $total_jadwal = Agenda::whereBetween('tanggal', [$mulai, $selesai])
                              ->distinct('tanggal')
                              ->count('tanggal');

        // Ambil data siswa
        $siswas = Siswa::with('kelas')
            ->when($kelas_id != 'semua', function($q) use ($kelas_id) {
                return $q->where('kelas_id', $kelas_id);
            })->get();

        // Hitung statistik per siswa
        foreach ($siswas as $siswa) {
            
            // Ambil absensi siswa, join dengan agenda agar tahu tanggalnya
            $absensi_siswa = Absensi::where('siswa_id', $siswa->id)
                ->whereHas('agenda', function($q) use ($mulai, $selesai) {
                    $q->whereBetween('tanggal', [$mulai, $selesai]);
                })
                ->with('agenda')
                ->get();

            // Kelompokkan absensi berdasarkan Tanggal (Jadi 2 kegiatan di hari yg sama = 1 grup)
            $absensi_per_hari = $absensi_siswa->groupBy(function($item) {
                return $item->agenda->tanggal;
            });

            $hadir = 0; $izin = 0; $sakit = 0;
            
            // Cek status kehadiran harian
            foreach ($absensi_per_hari as $tanggal => $records) {
                // Karena dalam 1 hari statusnya seragam, kita cukup ambil status dari record pertama
                $status_hari_ini = $records->first()->status_kehadiran;
                
                if ($status_hari_ini == 'hadir') $hadir++;
                elseif ($status_hari_ini == 'izin') $izin++;
                elseif ($status_hari_ini == 'sakit') $sakit++;
            }

            // Masukkan ke properti siswa
            $siswa->total_hadir = $hadir;
            $siswa->total_izin = $izin;
            $siswa->total_sakit = $sakit;
            
            // Alpa = Total Jadwal dikurangi hari dimana dia Hadir/Izin/Sakit
            // Ini otomatis meng-alpa-kan siswa di tanggal yang datanya kosong/belum di-scan
            $siswa->total_alpa = $total_jadwal - ($hadir + $izin + $sakit);
            if ($siswa->total_alpa < 0) $siswa->total_alpa = 0; // Jaga-jaga agar tidak minus

            // Hitung Persentase berdasarkan Total Jadwal
            $siswa->persentase = $total_jadwal > 0 ? round(($hadir / $total_jadwal) * 100) : 0;
        }

        // Urutkan dari persentase tertinggi ke terendah
        $siswas = $siswas->sortByDesc('persentase')->values(); // Gunakan ->values() agar nomor urut (index) rapi
        $nama_kelas = $kelas_id == 'semua' ? 'Semua Kelas' : Kelas::find($kelas_id)->nama_kelas;

        $pdf = Pdf::loadView('laporan.pdf_kehadiran_siswa', compact('siswas', 'mulai', 'selesai', 'nama_kelas'));
        return $pdf->stream('Laporan_Apresiasi_Siswa.pdf');
    }

    // 2. LAPORAN AGENDA & STATISTIK KEHADIRAN
    public function cetakAgenda(Request $request)
    {
        $mulai = $request->tanggal_mulai;
        $selesai = $request->tanggal_selesai;

        $agendas = Agenda::whereBetween('tanggal', [$mulai, $selesai])
                         ->orderBy('tanggal', 'asc')
                         ->orderBy('waktu_mulai', 'asc')
                         ->get();

        // Hitung berapa siswa yang hadir di setiap acara
        foreach ($agendas as $agenda) {
            $agenda->jumlah_hadir = Absensi::where('agenda_id', $agenda->id)->where('status_kehadiran', 'hadir')->count();
        }

        $pdf = Pdf::loadView('laporan.pdf_agenda', compact('agendas', 'mulai', 'selesai'));
        return $pdf->stream('Laporan_Statistik_Agenda.pdf');
    }

    // 3. LAPORAN DATA & KEHADIRAN PENGURUS/PENGAJAR
    public function cetakPengajar(Request $request)
    {
        $mulai = $request->tanggal_mulai;
        $selesai = $request->tanggal_selesai;

        // Cari total JADWAL (TANGGAL UNIK) pada rentang waktu tersebut
        $total_jadwal = Agenda::whereBetween('tanggal', [$mulai, $selesai])
                              ->distinct('tanggal')
                              ->count('tanggal');

        // Ambil data semua pengajar
        $pengajars = Pengajar::orderBy('nama_lengkap', 'asc')->get();

        // Hitung statistik per pengajar
        foreach ($pengajars as $pengajar) {
            
            // Ambil absensi pengajar, join dengan agenda agar tahu tanggalnya
            $absensi_pengajar = \App\Models\AbsensiPengajar::where('pengajar_id', $pengajar->id)
                ->whereHas('agenda', function($q) use ($mulai, $selesai) {
                    $q->whereBetween('tanggal', [$mulai, $selesai]);
                })
                ->with('agenda')
                ->get();

            // Kelompokkan absensi berdasarkan Tanggal
            $absensi_per_hari = $absensi_pengajar->groupBy(function($item) {
                return $item->agenda->tanggal;
            });

            $hadir = 0; $izin = 0; $sakit = 0;
            
            // Cek status kehadiran harian
            foreach ($absensi_per_hari as $tanggal => $records) {
                $status_hari_ini = $records->first()->status_kehadiran;
                
                if ($status_hari_ini == 'hadir') $hadir++;
                elseif ($status_hari_ini == 'izin') $izin++;
                elseif ($status_hari_ini == 'sakit') $sakit++;
            }

            // Masukkan ke properti pengajar
            $pengajar->total_hadir = $hadir;
            $pengajar->total_izin = $izin;
            $pengajar->total_sakit = $sakit;
            
            // Alpa = Total Jadwal dikurangi hari dimana dia Hadir/Izin/Sakit
            $pengajar->total_alpa = $total_jadwal - ($hadir + $izin + $sakit);
            if ($pengajar->total_alpa < 0) $pengajar->total_alpa = 0; // Mencegah minus

            // Hitung Persentase
            $pengajar->persentase = $total_jadwal > 0 ? round(($hadir / $total_jadwal) * 100) : 0;
        }

        // Urutkan dari persentase kehadiran tertinggi
        $pengajars = $pengajars->sortByDesc('persentase')->values();

        $pdf = Pdf::loadView('laporan.pdf_pengajar', compact('pengajars', 'mulai', 'selesai'));
        return $pdf->stream('Laporan_Data_Kehadiran_Pengurus.pdf');
    }
}