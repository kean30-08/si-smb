<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Pengajar;
use App\Models\Agenda;
use App\Models\Absensi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama dengan ringkasan statistik,
     * grafik kehadiran, dan leaderboard siswa teladan.
     */
    public function index(Request $request)
    {
        // Pengaturan rentang waktu filter
        $rentang_bulan = $request->input('rentang', 1);
        $start_date = Carbon::now()->subMonths($rentang_bulan - 1)->startOfMonth()->toDateString();
        $end_date = Carbon::now()->endOfMonth()->toDateString();

        // 1. KARTU RINGKASAN (KPI)
        $total_siswa = Siswa::where('status', 'aktif')->count();
        $total_pengajar = Pengajar::count();
        
        // Menghitung jadwal unik (berdasarkan tanggal) untuk statistik keaktifan
        $total_agenda_harian = Agenda::distinct('tanggal')->count('tanggal');

        // Total jadwal khusus dalam periode filter untuk perhitungan persentase leaderboard
        $total_jadwal_period = Agenda::whereBetween('tanggal', [$start_date, $end_date])
                                     ->distinct('tanggal')
                                     ->count('tanggal');

        // 2. DATA GRAFIK & REKAPITULASI (Sesuai Rentang Bulan)
        $agendas_per_hari = Agenda::whereBetween('tanggal', [$start_date, $end_date])
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
            
            // Mengambil jumlah siswa unik per status kehadiran dalam satu hari
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
            
            // Perhitungan Alpa: Total siswa aktif - (Hadir + Izin + Sakit)
            $alpa_count = max(0, $total_siswa - ($hadir_count + $izin_count + $sakit_count));

            $data_hadir[] = $hadir_count;
            $data_izin[]  = $izin_count;
            $data_sakit[] = $sakit_count;
            $data_alpa[]  = $alpa_count;
        }

        // Statistik total untuk ringkasan di bawah grafik
        $total_izin_period  = array_sum($data_izin);
        $total_sakit_period = array_sum($data_sakit);
        $total_alpa_period  = array_sum($data_alpa);

        // 3. LEADERBOARD SISWA TELADAN (POIN & KEDISIPLINAN)
        // Eager loading relasi kelas untuk efisiensi query
        $siswas = Siswa::with('kelas')->where('status', 'aktif')->get();

        foreach ($siswas as $siswa) {
            // Mengambil riwayat absensi dalam periode filter
            $absensi_history = Absensi::where('siswa_id', $siswa->id)
                ->whereHas('agenda', function($q) use ($start_date, $end_date) {
                    $q->whereBetween('tanggal', [$start_date, $end_date]);
                })
                ->with('agenda')
                ->get()
                ->groupBy(fn($item) => $item->agenda->tanggal);

            $hadir = 0; $izin = 0; $sakit = 0;
            $total_detik_kedatangan = 0; 
            $jumlah_scan = 0;
            
            foreach ($absensi_history as $tanggal => $records) {
                // Mengambil status harian dari agenda pertama pada hari tersebut
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

            // Kalkulasi skor keaktifan
            $siswa->poin_keaktifan = ($hadir * 100) + ($izin * 10) + ($sakit * 10);
            $siswa->persentase = $total_jadwal_period > 0 ? round(($hadir / $total_jadwal_period) * 100) : 0;
            
            // Tie-breaker: Kecepatan waktu hadir (rata-rata detik sejak tengah malam)
            $siswa->rata_rata_waktu_hadir = $jumlah_scan > 0 ? ($total_detik_kedatangan / $jumlah_scan) : 9999999; 
        }

        // Proses pengurutan Leaderboard (Sorting: Poin Tinggi > Waktu Pagi > Nama Abjad)
        $top_siswas = $siswas->sort(function ($a, $b) {
            if ($a->poin_keaktifan == $b->poin_keaktifan) {
                if ($a->rata_rata_waktu_hadir == $b->rata_rata_waktu_hadir) {
                    return strcmp($a->nama_lengkap, $b->nama_lengkap);
                }
                return $a->rata_rata_waktu_hadir <=> $b->rata_rata_waktu_hadir;
            }
            return $b->poin_keaktifan <=> $a->poin_keaktifan;
        })->take(5)->values();

        return view('dashboard', compact(
            'total_siswa', 'total_pengajar', 'total_agenda_harian', 
            'label_grafik', 'data_hadir', 'data_izin', 'data_sakit', 'data_alpa',
            'total_izin_period', 'total_sakit_period', 'total_alpa_period',
            'top_siswas', 'rentang_bulan'
        ));
    }
}