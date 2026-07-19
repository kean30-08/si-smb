<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Agenda;
use App\Models\Pengajar;
use App\Models\Absensi;
use App\Models\AbsensiPengajar;
use App\Models\TahunAjaran; 
use App\Models\HistoriSiswa; 
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        $kelas = Kelas::all();
        return view('laporan.index', compact('kelas'));
    }

    public function cetakKehadiranSiswa(Request $request)
    {
        ini_set('memory_limit', '256M');
        
        $mulai = $request->tanggal_mulai;
        $selesai = $request->tanggal_selesai;
        $kelas_id = $request->kelas_id;

        $agendas = Agenda::whereBetween('tanggal', [$mulai, $selesai])
            ->orderBy('tanggal', 'asc')
            ->get();

        if ($agendas->isEmpty()) {
            return back()->with('error', 'Tidak ada data jadwal kegiatan pada rentang tanggal tersebut.');
        }

        $ta_ids = $agendas->pluck('tahun_ajaran_id')->unique()->toArray();

        $agendasPerBulan = $agendas->pluck('tanggal')->unique()->values()->groupBy(function($date) {
            return Carbon::parse($date)->format('Y-m'); 
        });

        $agendaStatusMap = $agendas->where('is_libur', true)->pluck('nama_kegiatan', 'tanggal')->toArray();

        $historiQuery = HistoriSiswa::whereIn('tahun_ajaran_id', $ta_ids);
        if ($kelas_id != 'semua') {
             $historiQuery->where('kelas_id', $kelas_id);
        }
        $validSiswaIds = $historiQuery->pluck('siswa_id')->unique();

        $siswas = Siswa::with(['riwayatHistori' => function ($query) use ($ta_ids) {
            $query->whereIn('tahun_ajaran_id', $ta_ids);
        }, 'riwayatHistori.kelas', 'absensi' => function($q) use ($mulai, $selesai) {
            $q->whereHas('agenda', function($q2) use ($mulai, $selesai) {
                $q2->whereBetween('tanggal', [$mulai, $selesai]);
            });
        }, 'absensi.agenda'])->whereIn('id', $validSiswaIds)->get();

        foreach ($siswas as $siswa) {
            $historiLaporan = $siswa->riwayatHistori->first();
            $siswa->kelas_laporan = $historiLaporan && $historiLaporan->kelas ? $historiLaporan->kelas->nama_kelas : '-';
            
            $absenMap = [];
            foreach ($siswa->absensi as $absen) {
                if ($absen->agenda) {
                    $absenMap[$absen->agenda->tanggal] = $absen->status_kehadiran;
                }
            }
            $siswa->absen_map = $absenMap;
        }

        $urutanKelas = [
            'Kelas PG' => 1, 'Kelas TK A' => 2, 'Kelas TK B' => 3,
            'Kelas 1 SD' => 4, 'Kelas 2 SD' => 5, 'Kelas 3 SD' => 6, 'Kelas 4 SD' => 7, 'Kelas 5 SD' => 8, 'Kelas 6 SD' => 9,
            'Kelas 1 SMP' => 10, 'Kelas 2 SMP' => 11, 'Kelas 3 SMP' => 12,
            'Kelas 1 SMA' => 13, 'Kelas 2 SMA' => 14, 'Kelas 3 SMA' => 15,
        ];

        // MENGGABUNGKAN SEMUA SISWA MENJADI SATU DAFTAR PANJANG BERURUTAN (TIDAK DIPISAH ARRAY)
        $siswas = $siswas->sortBy(function($siswa) use ($urutanKelas) {
            $namaKelas = $siswa->kelas_laporan;
            $urutan = str_pad($urutanKelas[$namaKelas] ?? 99, 2, '0', STR_PAD_LEFT);
            return $urutan . '-' . $siswa->nama_lengkap;
        })->values();

        // MENGHITUNG TOTAL HADIR PER KELAS SEBELUM DIKIRIM KE VIEW
        $rekapHadirPerKelas = [];
        foreach ($siswas as $siswa) {
            $kls = $siswa->kelas_laporan;
            if (!isset($rekapHadirPerKelas[$kls])) {
                $rekapHadirPerKelas[$kls] = [];
            }
            if (isset($siswa->absen_map)) {
                foreach ($siswa->absen_map as $tgl => $status) {
                    if (!isset($rekapHadirPerKelas[$kls][$tgl])) {
                        $rekapHadirPerKelas[$kls][$tgl] = 0;
                    }
                    if ($status == 'hadir') {
                        $rekapHadirPerKelas[$kls][$tgl]++;
                    }
                }
            }
        }

        $namaTAs = TahunAjaran::whereIn('id', $ta_ids)->pluck('tahun_ajaran')->toArray();
        $nama_ta = empty($namaTAs) ? 'Tahun Ajaran Tidak Diketahui' : implode(' & ', $namaTAs);

        // VARIABEL siswas DIKIRIM KE VIEW
        $pdf = Pdf::loadView('laporan.pdf_kehadiran_siswa', compact('agendasPerBulan', 'siswas', 'nama_ta', 'agendaStatusMap', 'rekapHadirPerKelas'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Laporan_Absensi_Siswa_Grid.pdf');
    }

    public function cetakAgenda(Request $request)
    {
        $mulai = $request->tanggal_mulai;
        $selesai = $request->tanggal_selesai;

        $agendas = Agenda::whereBetween('tanggal', [$mulai, $selesai])
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        if ($agendas->isEmpty()) {
            return back()->with('error', 'Tidak ada data jadwal kegiatan pada rentang tanggal tersebut.');
        }

        foreach ($agendas as $agenda) {
            $agenda->jumlah_hadir = Absensi::where('agenda_id', $agenda->id)
                ->where('status_kehadiran', 'hadir')
                ->count();
        }

        $ta_ids = $agendas->pluck('tahun_ajaran_id')->unique()->toArray();
        $namaTAs = TahunAjaran::whereIn('id', $ta_ids)->pluck('tahun_ajaran')->toArray();
        $nama_ta = empty($namaTAs) ? '-' : implode(' & ', $namaTAs);

        $admin = auth()->user();

        $pdf = Pdf::loadView('laporan.pdf_agenda', compact('agendas', 'mulai', 'selesai', 'admin', 'nama_ta'));
        return $pdf->stream('Laporan_Statistik_Agenda.pdf');
    }

   public function cetakPengajar(Request $request)
    {
        $mulai = $request->tanggal_mulai;
        $selesai = $request->tanggal_selesai;

        $agendas = Agenda::whereBetween('tanggal', [$mulai, $selesai])->orderBy('tanggal', 'asc')->get();

        if ($agendas->isEmpty()) {
            return back()->with('error', 'Tidak ada data jadwal kegiatan pada rentang tanggal tersebut.');
        }

        $agendaStatusMap = $agendas->where('is_libur', true)->pluck('nama_kegiatan', 'tanggal')->toArray();

        $agendasPerBulan = $agendas->pluck('tanggal')->unique()->values()->groupBy(function($date) {
            return Carbon::parse($date)->format('Y-m'); 
        });

        // 1. Ambil semua ID Agenda pada bulan laporan ini
        $agendaIds = $agendas->pluck('id')->toArray();
        
        // 2. Cari ID pengajar (aktif maupun tidak) yang pernah diabsen pada agenda-agenda di bulan ini
        $pengajarPernahAbsen = \App\Models\AbsensiPengajar::whereIn('agenda_id', $agendaIds)
                                ->pluck('pengajar_id')
                                ->toArray();

        // 3. Tarik data pengajar: Yang AKTIF, ditambah yang TIDAK AKTIF tapi masuk dalam array $pengajarPernahAbsen
        $pengajars = Pengajar::where('status', 'aktif')
            ->orWhereIn('id', $pengajarPernahAbsen)
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // 4. Ambil semua absensi pengajar: Yang AKTIF, ditambah yang TIDAK AKTIF tapi masuk dalam array $pengajarPernahAbsen

        foreach ($pengajars as $pengajar) {
            $absensi_pengajar = AbsensiPengajar::where('pengajar_id', $pengajar->id)
                ->whereHas('agenda', function($q) use ($mulai, $selesai) {
                    $q->whereBetween('tanggal', [$mulai, $selesai]);
                })
                ->with('agenda')
                ->get();

            $absenMap = [];
            foreach ($absensi_pengajar as $absen) {
                if ($absen->agenda) {
                    $absenMap[$absen->agenda->tanggal] = $absen->status_kehadiran;
                }
            }
            $pengajar->absen_map = $absenMap;
        }

        $kepalaSekolah = Pengajar::whereHas('jabatan', function($q) {
            $q->where('nama_jabatan', 'like', '%Kepala Sekolah%');
        })->where('status', 'aktif')->first();
        
        $namaKepalaSekolah = $kepalaSekolah ? $kepalaSekolah->nama_lengkap : '...............................';

        $ta_ids = $agendas->pluck('tahun_ajaran_id')->unique()->toArray();
        $namaTAs = TahunAjaran::whereIn('id', $ta_ids)->pluck('tahun_ajaran')->toArray();
        $nama_ta = empty($namaTAs) ? '-' : implode(' & ', $namaTAs);
        
        $pdf = Pdf::loadView('laporan.pdf_pengajar', compact('pengajars', 'agendasPerBulan', 'agendaStatusMap', 'namaKepalaSekolah', 'nama_ta', 'mulai', 'selesai'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Laporan_Data_Kehadiran_Pengurus.pdf');
    }
}