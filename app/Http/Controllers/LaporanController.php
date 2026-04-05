<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Agenda;
use App\Models\Pengajar;
use App\Models\Absensi;
use App\Models\AbsensiPengajar;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman utama menu laporan.
     */
    public function index()
    {
        $kelas = Kelas::all();
        return view('laporan.index', compact('kelas'));
    }

    /**
     * Menghasilkan laporan rekapitulasi keaktifan siswa dalam format PDF.
     * Mengkalkulasi statistik kehadiran berdasarkan rentang tanggal unik.
     */
    public function cetakKehadiranSiswa(Request $request)
    {
        ini_set('memory_limit', '256M');
        
        $mulai = $request->tanggal_mulai;
        $selesai = $request->tanggal_selesai;
        $kelas_id = $request->kelas_id;

        $total_jadwal = Agenda::whereBetween('tanggal', [$mulai, $selesai])
                               ->distinct('tanggal')
                               ->count('tanggal');

        $siswas = Siswa::with('kelas')
            ->when($kelas_id != 'semua', function($q) use ($kelas_id) {
                return $q->where('kelas_id', $kelas_id);
            })->get();

        foreach ($siswas as $siswa) {
            $absensi_siswa = Absensi::where('siswa_id', $siswa->id)
                ->whereHas('agenda', function($q) use ($mulai, $selesai) {
                    $q->whereBetween('tanggal', [$mulai, $selesai]);
                })
                ->with('agenda')
                ->get();

            $absensi_per_hari = $absensi_siswa->groupBy(function($item) {
                return $item->agenda->tanggal;
            });

            $hadir = 0; $izin = 0; $sakit = 0;
            
            foreach ($absensi_per_hari as $tanggal => $records) {
                $status_hari_ini = $records->first()->status_kehadiran;
                
                if ($status_hari_ini == 'hadir') $hadir++;
                elseif ($status_hari_ini == 'izin') $izin++;
                elseif ($status_hari_ini == 'sakit') $sakit++;
            }

            $siswa->total_hadir = $hadir;
            $siswa->total_izin = $izin;
            $siswa->total_sakit = $sakit;
            $siswa->total_alpa = max(0, $total_jadwal - ($hadir + $izin + $sakit));
            $siswa->persentase = $total_jadwal > 0 ? round(($hadir / $total_jadwal) * 100) : 0;
        }

        $siswas = $siswas->sortByDesc('persentase')->values();
        $nama_kelas = $kelas_id == 'semua' ? 'Semua Kelas' : Kelas::find($kelas_id)->nama_kelas;

        // TAMBAHAN: Ambil data user yang sedang login (Admin)
        $admin = auth()->user();

        $pdf = Pdf::loadView('laporan.pdf_kehadiran_siswa', compact('siswas', 'mulai', 'selesai', 'nama_kelas', 'admin'));
        return $pdf->stream('Laporan_Apresiasi_Siswa.pdf');
    }

    public function cetakAgenda(Request $request)
    {
        $mulai = $request->tanggal_mulai;
        $selesai = $request->tanggal_selesai;

        $agendas = Agenda::whereBetween('tanggal', [$mulai, $selesai])
                         ->orderBy('tanggal', 'asc')
                         ->orderBy('waktu_mulai', 'asc')
                         ->get();

        foreach ($agendas as $agenda) {
            $agenda->jumlah_hadir = Absensi::where('agenda_id', $agenda->id)
                                           ->where('status_kehadiran', 'hadir')
                                           ->count();
        }

        // TAMBAHAN: Ambil data user yang sedang login (Admin)
        $admin = auth()->user();

        $pdf = Pdf::loadView('laporan.pdf_agenda', compact('agendas', 'mulai', 'selesai', 'admin'));
        return $pdf->stream('Laporan_Statistik_Agenda.pdf');
    }

    public function cetakPengajar(Request $request)
    {
        $mulai = $request->tanggal_mulai;
        $selesai = $request->tanggal_selesai;

        $total_jadwal = Agenda::whereBetween('tanggal', [$mulai, $selesai])
                               ->distinct('tanggal')
                               ->count('tanggal');

        $pengajars = Pengajar::orderBy('nama_lengkap', 'asc')->get();

        foreach ($pengajars as $pengajar) {
            $absensi_pengajar = AbsensiPengajar::where('pengajar_id', $pengajar->id)
                ->whereHas('agenda', function($q) use ($mulai, $selesai) {
                    $q->whereBetween('tanggal', [$mulai, $selesai]);
                })
                ->with('agenda')
                ->get();

            $absensi_per_hari = $absensi_pengajar->groupBy(function($item) {
                return $item->agenda->tanggal;
            });

            $hadir = 0; $izin = 0; $sakit = 0;
            
            foreach ($absensi_per_hari as $tanggal => $records) {
                $status_hari_ini = $records->first()->status_kehadiran;
                if ($status_hari_ini == 'hadir') $hadir++;
                elseif ($status_hari_ini == 'izin') $izin++;
                elseif ($status_hari_ini == 'sakit') $sakit++;
            }

            $pengajar->total_hadir = $hadir;
            $pengajar->total_izin = $izin;
            $pengajar->total_sakit = $sakit;
            
            $pengajar->total_alpa = max(0, $total_jadwal - ($hadir + $izin + $sakit));
            $pengajar->persentase = $total_jadwal > 0 ? round(($hadir / $total_jadwal) * 100) : 0;
        }

        $pengajars = $pengajars->sortByDesc('persentase')->values();

        // TAMBAHAN: Ambil data user yang sedang login (Admin)
        $admin = auth()->user();

        $pdf = Pdf::loadView('laporan.pdf_pengajar', compact('pengajars', 'mulai', 'selesai', 'admin'));
        return $pdf->stream('Laporan_Data_Kehadiran_Pengurus.pdf');
    }
}