<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Pengajar;
use App\Models\Agenda;
use App\Models\Absensi;
use App\Models\TahunAjaran;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter_type = $request->input('filter_type', 'bulan'); // Default 'bulan'
        $rentang_bulan = $request->input('rentang', 1);

        $tahunAktif = TahunAjaran::where('status', 'aktif')->first();

        // LOGIKA KUNCI: Buka gembok Tahun Ajaran jika memakai Filter Kustom
        if ($filter_type == 'kustom') {
            $start_date = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
            $end_date = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
            
            // Atur menjadi null agar query tidak dibatasi oleh Tahun Ajaran aktif
            $tahun_ajaran_id = null; 
        } else {
            $start_date = Carbon::now()->subMonths($rentang_bulan - 1)->startOfMonth()->toDateString();
            $end_date = Carbon::now()->endOfMonth()->toDateString();
            
            // Kunci query hanya untuk Tahun Ajaran yang sedang aktif
            $tahun_ajaran_id = $tahunAktif ? $tahunAktif->id : 0; 
        }

        // 1. KARTU RINGKASAN (KPI)
        $total_siswa = Siswa::where('status', 'aktif')->count();
        $total_pengajar = Pengajar::count();
        
        $total_agenda_harian = Agenda::when($tahun_ajaran_id, function($q) use ($tahun_ajaran_id) {
                                        return $q->where('tahun_ajaran_id', $tahun_ajaran_id);
                                     })
                                     ->whereBetween('tanggal', [$start_date, $end_date])
                                     ->distinct('tanggal')
                                     ->count('tanggal');

        $total_jadwal_period = Agenda::when($tahun_ajaran_id, function($q) use ($tahun_ajaran_id) {
                                        return $q->where('tahun_ajaran_id', $tahun_ajaran_id);
                                     })
                                     ->whereBetween('tanggal', [$start_date, $end_date])
                                     ->distinct('tanggal')
                                     ->count('tanggal');

        // 2. DATA GRAFIK & REKAPITULASI 
        $agendas_per_hari = Agenda::when($tahun_ajaran_id, function($q) use ($tahun_ajaran_id) {
                                    return $q->where('tahun_ajaran_id', $tahun_ajaran_id);
                               })
                               ->whereBetween('tanggal', [$start_date, $end_date])
                               ->orderBy('tanggal', 'asc')
                               ->get()
                               ->groupBy('tanggal');
        
        $label_grafik = [];
        $data_hadir = [];
        $data_izin = [];
        $data_sakit = [];
        $data_alpa = [];

        foreach ($agendas_per_hari as $tanggal => $agendas) {
            $label_grafik[] = Carbon::parse($tanggal)->format('d M');
            $agenda_ids = $agendas->pluck('id')->toArray();
            
            $hadir_count = Absensi::whereIn('agenda_id', $agenda_ids)
                                ->where('status_kehadiran', 'hadir')
                                ->distinct('siswa_id')
                                ->count('siswa_id');

            $izin_count = Absensi::whereIn('agenda_id', $agenda_ids)
                                ->where('status_kehadiran', 'izin')
                                ->distinct('siswa_id')
                                ->count('siswa_id');

            $sakit_count = Absensi::whereIn('agenda_id', $agenda_ids)
                                 ->where('status_kehadiran', 'sakit')
                                 ->distinct('siswa_id')
                                 ->count('siswa_id');
            
            $alpa_count = max(0, $total_siswa - ($hadir_count + $izin_count + $sakit_count));

            $data_hadir[] = $hadir_count;
            $data_izin[]  = $izin_count;
            $data_sakit[] = $sakit_count;
            $data_alpa[]  = $alpa_count;
        }

        $total_izin_period  = array_sum($data_izin);
        $total_sakit_period = array_sum($data_sakit);
        $total_alpa_period  = array_sum($data_alpa);

        // 3. LEADERBOARD SISWA TELADAN 
        $siswas = Siswa::with('nilaiKehadiranAktif.kelas')->where('status', 'aktif')->get();

        foreach ($siswas as $siswa) {
            $absensi_history = Absensi::where('siswa_id', $siswa->id)
                ->whereHas('agenda', function($q) use ($start_date, $end_date, $tahun_ajaran_id) {
                    $q->whereBetween('tanggal', [$start_date, $end_date])
                      ->when($tahun_ajaran_id, function($query, $ta_id) {
                          return $query->where('tahun_ajaran_id', $ta_id);
                      });
                })
                ->with('agenda')
                ->get()
                ->groupBy(fn($item) => $item->agenda->tanggal);

            $hadir = 0; $izin = 0; $sakit = 0;
            $total_detik_kedatangan = 0; 
            $jumlah_scan = 0;
            
            foreach ($absensi_history as $tanggal => $records) {
                $status = $records->first()->status_kehadiran;
                $waktu_hadir = $records->first()->waktu_hadir;

                if ($status == 'hadir') {
                    $hadir++;
                    if ($waktu_hadir) {
                        $total_detik_kedatangan += Carbon::parse($waktu_hadir)->secondsSinceMidnight();
                        $jumlah_scan++;
                    }
                } elseif ($status == 'izin') {
                    $izin++;
                } elseif ($status == 'sakit') {
                    $sakit++;
                }
            }

            $siswa->setAttribute('poin_keaktifan', ($hadir * 100) + ($izin * 10) + ($sakit * 10));
            $siswa->setAttribute('persentase', $total_jadwal_period > 0 ? round(($hadir / $total_jadwal_period) * 100) : 0);
            $siswa->setAttribute('rata_rata_waktu_hadir', $jumlah_scan > 0 ? ($total_detik_kedatangan / $jumlah_scan) : 9999999); 
        }

        $top_siswas = $siswas->sort(function ($a, $b) {
            $poinA = $a->poin_keaktifan ?? 0;
            $poinB = $b->poin_keaktifan ?? 0;
            $waktuA = $a->rata_rata_waktu_hadir ?? 9999999;
            $waktuB = $b->rata_rata_waktu_hadir ?? 9999999;

            if ($poinA == $poinB) {
                if ($waktuA == $waktuB) {
                    return strcmp($a->nama_lengkap, $b->nama_lengkap);
                }
                return $waktuA <=> $waktuB;
            }
            return $poinB <=> $poinA;
        })->take(5)->values();

        return view('dashboard', compact(
            'total_siswa', 'total_pengajar', 'total_agenda_harian', 
            'label_grafik', 'data_hadir', 'data_izin', 'data_sakit', 'data_alpa',
            'total_izin_period', 'total_sakit_period', 'total_alpa_period',
            'top_siswas', 'filter_type', 'rentang_bulan', 'start_date', 'end_date'
        ));
    }
}