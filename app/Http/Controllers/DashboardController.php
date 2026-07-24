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

        // Ambil Semua Tahun Ajaran untuk Dropdown
        $tahunAktif = TahunAjaran::where('status', 'aktif')->first();
        $tahunAjarans = TahunAjaran::orderBy('created_at', 'desc')->get();

        // 1. TENTUKAN FILTER TAHUN AJARAN (Dari Dropdown Header)
        $selected_ta_id = $request->input('tahun_ajaran_id', 'semua');
        
        // // Jika baru pertama kali buka halaman, default ke Tahun Ajaran Aktif
        // if ($selected_ta_id === null && $tahunAktif) {
        //     $selected_ta_id = $tahunAktif->id;
        // }

        // Jika user memilih "Semua Tahun Ajaran", nilai filternya kita lepas (null)
        $filter_ta_id = ($selected_ta_id === 'semua') ? null : $selected_ta_id;

        // 2. TENTUKAN RENTANG WAKTU
        if ($filter_type == 'kustom') {
            $start_date = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
            $end_date = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        } else {
            $start_date = Carbon::now()->subMonths($rentang_bulan - 1)->startOfMonth()->toDateString();
            $end_date = Carbon::now()->endOfMonth()->toDateString();
        }

       
        // 3. KARTU RINGKASAN (KPI)
        // Filter total siswa dan pengajar maksimal sampai batas akhir rentang waktu ($end_date)
        $total_siswa = Siswa::where('status', 'aktif')
                            ->whereDate('created_at', '<=', $end_date)
                            ->count();
                            
        $total_pengajar = Pengajar::whereDate('created_at', '<=', $end_date)
                                  ->count();
        
        $total_agenda_harian = Agenda::when($filter_ta_id, function($q) use ($filter_ta_id) {
                                        return $q->where('tahun_ajaran_id', $filter_ta_id);
                                     })
                                     ->whereBetween('tanggal', [$start_date, $end_date])
                                     ->where('is_libur', 0) // TAMBAHAN: Buang hari libur dari KPI Total Agenda
                                     ->distinct('tanggal')
                                     ->count('tanggal');

        $total_jadwal_period = Agenda::when($filter_ta_id, function($q) use ($filter_ta_id) {
                                        return $q->where('tahun_ajaran_id', $filter_ta_id);
                                     })
                                     ->whereBetween('tanggal', [$start_date, $end_date])
                                     ->where('is_libur', 0) // TAMBAHAN: Buang hari libur dari pembagi persentase
                                     ->distinct('tanggal')
                                     ->count('tanggal');

        // 4. DATA GRAFIK & REKAPITULASI 
        $agendas_per_hari = Agenda::when($filter_ta_id, function($q) use ($filter_ta_id) {
                                    return $q->where('tahun_ajaran_id', $filter_ta_id);
                               })
                               ->whereBetween('tanggal', [$start_date, $end_date])
                               ->where('is_libur', 0) // TAMBAHAN: Buang hari libur dari data Grafik
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
            
            // HITUNG DINAMIS: Jumlah siswa aktif yang terdaftar HINGGA tanggal agenda ini berlangsung
            $siswa_aktif_saat_itu = Siswa::where('status', 'aktif')
                                         ->whereDate('created_at', '<=', $tanggal)
                                         ->count();

            // Rumus Alpa menggunakan total siswa pada HARI ITU, bukan total siswa saat ini
            $alpa_count = max(0, $siswa_aktif_saat_itu - ($hadir_count + $izin_count + $sakit_count));

            $data_hadir[] = $hadir_count;
            $data_izin[]  = $izin_count;
            $data_sakit[] = $sakit_count;
            $data_alpa[]  = $alpa_count;
        }

        $total_izin_period  = array_sum($data_izin);
        $total_sakit_period = array_sum($data_sakit);
        $total_alpa_period  = array_sum($data_alpa);

        // 5. LEADERBOARD SISWA TELADAN 
        $siswas = Siswa::with('historiAktif.kelas')->where('status', 'aktif')->get();

        foreach ($siswas as $siswa) {
            $absensi_history = Absensi::where('siswa_id', $siswa->id)
                ->whereHas('agenda', function($q) use ($start_date, $end_date, $filter_ta_id) {
                    $q->whereBetween('tanggal', [$start_date, $end_date])
                      ->when($filter_ta_id, function($query, $ta_id) {
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
                // TAMBAHAN: Cegat dan lewati jadwal Libur agar tidak dihitung
                if ($records->first()->agenda->is_libur) {
                    continue; 
                }

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

            $siswa->setAttribute('poin_keaktifan', ($hadir * 5) + ($izin * 1) + ($sakit * 1));
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
            'top_siswas', 'filter_type', 'rentang_bulan', 'start_date', 'end_date',
            'tahunAjarans', 'selected_ta_id'
        ));
    }
    
    public function peringkatDetail(Request $request)
    {
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $sort = $request->input('sort', 'desc'); // default: 'desc' (tertinggi)

        // =========================================================================
        // QUERY BUILDER: Hitung poin langsung di database agar bisa di-paginate
        // =========================================================================
        $query = \App\Models\Siswa::with('historiAktif.kelas')
            ->where('status', 'aktif'); // Opsional: Hanya tampilkan siswa yang aktif

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // Sub-query untuk filter tanggal dan filter BUKAN hari libur
        $absensiFilter = function ($q) use ($startDate, $endDate) {
            $q->whereHas('agenda', function ($agendaQuery) use ($startDate, $endDate) {
                // Syarat mutlak: Bukan hari libur
                $agendaQuery->where('is_libur', false);

                // Tambahan: Filter rentang tanggal jika diisi
                if ($startDate && $endDate) {
                    $agendaQuery->whereBetween('tanggal', [$startDate, $endDate]);
                }
            });
        };

        // Hitung masing-masing status kehadiran
        $query->withCount([
            'absensi as total_hadir' => function ($q) use ($absensiFilter) {
                $absensiFilter($q);
                $q->where('status_kehadiran', 'hadir');
            },
            'absensi as total_izin' => function ($q) use ($absensiFilter) {
                $absensiFilter($q);
                $q->where('status_kehadiran', 'izin');
            },
            'absensi as total_sakit' => function ($q) use ($absensiFilter) {
                $absensiFilter($q);
                $q->where('status_kehadiran', 'sakit');
            },
            'absensi as total_alpa' => function ($q) use ($absensiFilter) {
                $absensiFilter($q);
                $q->where('status_kehadiran', 'alpa');
            }
        ]);

        // Kalkulasi poin utama langsung di database (Hadir*5 + Sakit*1 + Izin*1)
        // (Rumus ini dibuat mentah (raw) agar bisa digunakan untuk sorting)
        $poinRawQuery = "
            (SELECT COALESCE(SUM(
                CASE 
                    WHEN status_kehadiran = 'hadir' THEN 5
                    WHEN status_kehadiran = 'izin' THEN 1
                    WHEN status_kehadiran = 'sakit' THEN 1
                    ELSE 0
                END
            ), 0)
            FROM absensis 
            INNER JOIN agendas ON absensis.agenda_id = agendas.id
            WHERE absensis.siswa_id = siswas.id AND agendas.is_libur = 0
        ";

        if ($startDate && $endDate) {
            $poinRawQuery .= " AND agendas.tanggal BETWEEN '$startDate' AND '$endDate'";
        }
        $poinRawQuery .= ")";

        // Tambahkan atribut poin_keaktifan ke query
        $query->select('siswas.*', \Illuminate\Support\Facades\DB::raw("$poinRawQuery as poin_keaktifan"));

        // Lakukan pengurutan (Sorting)
        if ($sort === 'asc') {
            $query->orderBy('poin_keaktifan', 'asc');
        } else {
            $query->orderBy('poin_keaktifan', 'desc');
        }

        // Terapkan Paginasi!
        $peringkat = $query->paginate(15)->appends($request->query());

        // Hitung persentase kehadiran (Setelah data ditarik/paginate)
        foreach ($peringkat as $siswa) {
            $total = $siswa->total_hadir + $siswa->total_izin + $siswa->total_sakit + $siswa->total_alpa;
            $siswa->persentase = $total > 0 ? round(($siswa->total_hadir / $total) * 100) : 0;
        }

        return view('dashboard.peringkat', compact('peringkat', 'search', 'startDate', 'endDate', 'sort'));
    }
}