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
    public function index(Request $request)
    {
        $rentang_bulan = $request->input('rentang', 1);

        $start_date = Carbon::now()->subMonths($rentang_bulan - 1)->startOfMonth()->toDateString();
        $end_date = Carbon::now()->endOfMonth()->toDateString();

        // 1. KARTU RINGKASAN (KPI)
        $total_siswa = Siswa::where('status', 'aktif')->count();
        $total_pengajar = Pengajar::count();
        
        // Menghitung SEMUA jadwal di database tanpa terpengaruh filter tanggal
        $total_agenda_harian = Agenda::distinct('tanggal')->count('tanggal');

        // Total Jadwal (khusus dalam rentang waktu terpilih) untuk perhitungan persentase
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
            
            // Hitung status harian per siswa unik
            $hadir_count = Absensi::whereIn('agenda_id', $agenda_ids)->where('status_kehadiran', 'hadir')->distinct('siswa_id')->count('siswa_id');
            $izin_count = Absensi::whereIn('agenda_id', $agenda_ids)->where('status_kehadiran', 'izin')->distinct('siswa_id')->count('siswa_id');
            $sakit_count = Absensi::whereIn('agenda_id', $agenda_ids)->where('status_kehadiran', 'sakit')->distinct('siswa_id')->count('siswa_id');
            
            // Alpa = Total Siswa Aktif dikurangi yang Hadir, Izin, dan Sakit
            $alpa_count = $total_siswa - ($hadir_count + $izin_count + $sakit_count);
            if($alpa_count < 0) $alpa_count = 0;

            $data_hadir[] = $hadir_count;
            $data_izin[] = $izin_count;
            $data_sakit[] = $sakit_count;
            $data_alpa[] = $alpa_count;
        }

        // Hitung Total Sakit, Izin, Alpa untuk ditampilkan di bawah grafik
        $total_izin_period = array_sum($data_izin);
        $total_sakit_period = array_sum($data_sakit);
        $total_alpa_period = array_sum($data_alpa);

        // 3. LEADERBOARD SISWA TELADAN DENGAN SISTEM POIN & KEDISIPLINAN WAKTU
        $siswas = Siswa::with('kelas')->where('status', 'aktif')->get();

        foreach ($siswas as $siswa) {
            $absensi_hari_ini = Absensi::where('siswa_id', $siswa->id)
                ->whereHas('agenda', function($q) use ($start_date, $end_date) {
                    $q->whereBetween('tanggal', [$start_date, $end_date]);
                })
                ->with('agenda')
                ->get()
                ->groupBy(function($item) { return $item->agenda->tanggal; });

            $hadir = 0; $izin = 0; $sakit = 0;
            $total_detik_kedatangan = 0; // Untuk menghitung rata-rata kedatangan
            $jumlah_scan = 0;
            
            foreach ($absensi_hari_ini as $tanggal => $records) {
                $status = $records->first()->status_kehadiran;
                $waktu_hadir = $records->first()->waktu_hadir;

                if ($status == 'hadir') {
                    $hadir++;
                    // Jika ada jam kehadirannya, ubah ke detik dari tengah malam
                    if ($waktu_hadir) {
                        $total_detik_kedatangan += Carbon::parse($waktu_hadir)->secondsSinceMidnight();
                        $jumlah_scan++;
                    }
                }
                elseif ($status == 'izin') $izin++;
                elseif ($status == 'sakit') $sakit++;
            }

            $siswa->poin_keaktifan = ($hadir * 100) + ($izin * 10) + ($sakit * 10);
            $siswa->persentase = $total_jadwal_period > 0 ? round(($hadir / $total_jadwal_period) * 100) : 0;
            
            // Hitung rata-rata waktu kedatangan (semakin kecil angkanya, semakin pagi dia datang)
            // Jika dia tidak pernah hadir/scan, beri angka sangat besar agar ditaruh di bawah
            $siswa->rata_rata_waktu_hadir = $jumlah_scan > 0 ? ($total_detik_kedatangan / $jumlah_scan) : 9999999; 
        }

        // URUTKAN SISWA (SORTING CANGGIH)
        $top_siswas = $siswas->sort(function ($a, $b) {
            // Jika Poin Keaktifan SAMA PERSIS (Misal sama-sama 500)
            if ($a->poin_keaktifan == $b->poin_keaktifan) {
                // TIE-BREAKER: Siapa yang rata-rata kedatangannya lebih PAGI (detik lebih kecil)?
                if ($a->rata_rata_waktu_hadir == $b->rata_rata_waktu_hadir) {
                    // Jika datangnya juga sama-sama persis di detik yang sama (sangat jarang), baru pakai Abjad
                    return strcmp($a->nama_lengkap, $b->nama_lengkap);
                }
                return $a->rata_rata_waktu_hadir <=> $b->rata_rata_waktu_hadir; // Sortir dari yang terkecil (Paling pagi)
            }
            // Jika poin beda, urutkan murni dari Poin Tertinggi
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