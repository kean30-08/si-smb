<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Agenda;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Pengajar;
use App\Models\AbsensiPengajar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    /**
     * Menampilkan Halaman Kelola Absensi (BERBASIS TANGGAL HARIAN)
     */
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::now()->toDateString());
        $kelas_id = $request->input('kelas_id');
        $type = $request->input('type', 'siswa'); 
        $search = $request->input('search'); 

        $kelas = Kelas::all();
        
        // Karena sekarang absensi berbasis Harian (Tanggal), kita jadikan kegiatan pertama 
        // di hari tersebut sebagai "Jangkar" penyimpanan database.
        $selectedAgenda = Agenda::with('penanggungJawab')->where('tanggal', $tanggal)->orderBy('waktu_mulai', 'asc')->first();
        $agenda_id = $selectedAgenda ? $selectedAgenda->id : null;

        $penanggungJawab = $selectedAgenda ? $selectedAgenda->penanggungJawab : collect();

        // CEK HAK AKSES & STATUS LIBUR
        $isPic = true; 
        $isLibur = $selectedAgenda ? $selectedAgenda->is_libur : false; 

        // VARIABEL UNTUK MENAMPUNG RINGKASAN DATA
        $summary = [
            'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'total' => 0, 'nama_kelas' => 'Semua Kelas'
        ];

        // TAB SISWA
        if ($type == 'siswa') {
            // Ambil ID Tahun Ajaran dari agenda yang sedang dibuka
            $agendaTaId = $selectedAgenda ? $selectedAgenda->tahun_ajaran_id : null;

            if ($agendaTaId) {
                // 1. Cari ID siswa yang BENAR-BENAR terdaftar pada Tahun Ajaran (masa lalu/kini) tersebut
                $historiQuery = \App\Models\HistoriSiswa::where('tahun_ajaran_id', $agendaTaId);
                
                // Tangkap Info Kelas untuk Judul Ringkasan
                if ($kelas_id) {
                    $historiQuery->where('kelas_id', $kelas_id);
                    $kelasInfo = Kelas::find($kelas_id);
                    $summary['nama_kelas'] = $kelasInfo ? $kelasInfo->nama_kelas : 'Semua Kelas';
                }

                $validSiswaIds = $historiQuery->pluck('siswa_id');
                $summary['total'] = $validSiswaIds->count();

                // 2. Tarik data siswa, dan muat relasi historinya KHUSUS untuk TA tersebut
                $siswas = Siswa::with(['riwayatHistori' => function ($q) use ($agendaTaId) {
                        $q->where('tahun_ajaran_id', $agendaTaId);
                    }, 'riwayatHistori.kelas'])
                    ->whereIn('id', $validSiswaIds) // KUNCI UTAMA: Coret murid yang tidak ada di daftar valid
                    ->when($search, function($q, $search) {
                        return $q->where(function($query) use ($search) {
                            $query->where('nama_lengkap', 'like', "%{$search}%")
                                  ->orWhere('nis', 'like', "%{$search}%");
                        });
                    })
                    ->orderBy('nama_lengkap', 'asc')
                    ->paginate(15)
                    ->appends(['tanggal' => $tanggal, 'kelas_id' => $kelas_id, 'type' => 'siswa', 'search' => $search]);
                
                $absensis = $agenda_id ? Absensi::where('agenda_id', $agenda_id)->get() : collect();

                // 3. Kalkulasi Angka Ringkasan Absensi Berdasarkan Filter Saat Ini
                $absensiValid = $absensis->whereIn('siswa_id', $validSiswaIds->toArray());
                $summary['hadir'] = $absensiValid->where('status_kehadiran', 'hadir')->count();
                $summary['izin'] = $absensiValid->where('status_kehadiran', 'izin')->count();
                $summary['sakit'] = $absensiValid->where('status_kehadiran', 'sakit')->count();
                $summary['alpa'] = $summary['total'] - ($summary['hadir'] + $summary['izin'] + $summary['sakit']);

            } else {
                // Jika tidak ada jadwal/agenda di tanggal tersebut, kembalikan paginasi kosong
                $siswas = Siswa::whereRaw('1 = 0')->paginate(15);
                $absensis = collect();
            }
            
            return view('absensi.index', compact('tanggal', 'kelas', 'kelas_id', 'agenda_id', 'selectedAgenda', 'siswas', 'absensis', 'type', 'search', 'penanggungJawab', 'isPic', 'isLibur', 'summary'));
            
        // TAB PENGAJAR
        } else {
            $pengajars = Pengajar::orderBy('nama_lengkap', 'asc')
                ->when($search, function($q, $search) {
                    return $q->where('nama_lengkap', 'like', "%{$search}%");
                })
                ->paginate(15)
                ->appends(['tanggal' => $tanggal, 'type' => 'pengajar', 'search' => $search]);
                
            $absensiPengajars = $agenda_id ? AbsensiPengajar::where('agenda_id', $agenda_id)->get() : collect();

            // Kalkulasi Angka Ringkasan untuk Pengajar
            $summary['nama_kelas'] = 'Semua Pengajar / Pengurus';
            $summary['total'] = Pengajar::count();
            $summary['hadir'] = $absensiPengajars->where('status_kehadiran', 'hadir')->count();
            $summary['izin'] = $absensiPengajars->where('status_kehadiran', 'izin')->count();
            $summary['sakit'] = $absensiPengajars->where('status_kehadiran', 'sakit')->count();
            $summary['alpa'] = $summary['total'] - ($summary['hadir'] + $summary['izin'] + $summary['sakit']);
            
            return view('absensi.index', compact('tanggal', 'kelas', 'kelas_id', 'agenda_id', 'selectedAgenda', 'pengajars', 'absensiPengajars', 'type', 'search', 'penanggungJawab', 'isPic', 'isLibur', 'summary'));
        }
    }

    /**
     * Memproses Absensi Manual KHUSUS PENGAJAR
     */
    public function updateManualPengajar(Request $request)
    {
        $request->validate([
            'pengajar_id' => 'required',
            'agenda_id' => 'required', 
            'status' => 'required|in:hadir,izin,sakit,alpa'
        ]);

        $agenda = Agenda::find($request->agenda_id);
        
        if (!$agenda) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Data agenda tidak ditemukan!']);
            return back()->with('error', 'Data agenda tidak ditemukan!');
        }

        if ($agenda->is_libur) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Absensi ditutup! Hari Libur.']);
            return back()->with('error', 'Absensi ditutup! Tanggal ini telah ditetapkan sebagai Hari Libur.');
        }

        $waktu = ($request->status == 'hadir') ? Carbon::now()->toTimeString() : null;

        AbsensiPengajar::updateOrCreate(
            ['agenda_id' => $request->agenda_id, 'pengajar_id' => $request->pengajar_id],
            ['status_kehadiran' => $request->status, 'waktu_hadir' => $waktu]
        );

        // JIKA REQUEST BERASAL DARI AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $request->status,
                'waktu_hadir' => $waktu,
                'tanggal' => Carbon::now()->format('d/m/Y'),
                'metode' => 'Manual'
            ]);
        }

        return back()->with('success', 'Status kehadiran Pengajar berhasil diperbarui!');
    }

    /**
     * Memproses Absensi Manual (Izin/Sakit/Alpa) dari Halaman Admin untuk Siswa
     */
    public function updateManual(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required',
            'agenda_id' => 'required', 
            'status' => 'required|in:hadir,izin,sakit,alpa'
        ]);

        $agenda = Agenda::find($request->agenda_id);
        
        if (!$agenda) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Data agenda tidak ditemukan!']);
            return back()->with('error', 'Data agenda tidak ditemukan!');
        }

        if ($agenda->is_libur) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Absensi ditutup! Hari Libur.']);
            return back()->with('error', 'Absensi ditutup! Tanggal ini telah ditetapkan sebagai Hari Libur.');
        }

        $waktu = ($request->status == 'hadir') ? Carbon::now()->toTimeString() : null;

        Absensi::updateOrCreate(
            [
                'agenda_id' => $request->agenda_id,
                'siswa_id' => $request->siswa_id
            ],
            [
                'status_kehadiran' => $request->status,
                'metode_absen' => 'manual', 
                'waktu_hadir' => $waktu
            ]
        );

        // JIKA REQUEST BERASAL DARI AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $request->status,
                'waktu_hadir' => $waktu,
                'tanggal' => Carbon::now()->format('d/m/Y'),
                'metode' => 'Manual'
            ]);
        }

        return back()->with('success', 'Status kehadiran siswa berhasil diperbarui!');
    }

    /**
     * Menampilkan Halaman Kamera Scanner untuk Siswa
     */
    public function scanner(Request $request)
    {
        $agenda_id = $request->agenda_id;
        $agenda = Agenda::with('penanggungJawab')->find($agenda_id);

        if (!$agenda) {
            return redirect()->route('absensi.index')->with('error', 'Tidak ada data kegiatan pada tanggal ini!');
        }

        return view('absensi.scanner', compact('agenda'));
    }

    /**
     * Memproses Data dari Kamera (AJAX)
     */
    public function prosesScan(Request $request)
    {
        $barcode = $request->barcode; 
        $agenda_id = $request->agenda_id; 

        $agenda = Agenda::with('penanggungJawab')->find($agenda_id);
        if (!$agenda) {
            return response()->json(['success' => false, 'message' => 'Jangkar data tidak valid!']);
        }

        if ($agenda->is_libur) {
            return response()->json(['success' => false, 'message' => 'Hari Libur! Fitur Scanner dinonaktifkan.']);
        }

        $parts = explode('-', $barcode);
        
        if (count($parts) != 2 || $parts[0] != 'SMB' || !is_numeric($parts[1])) {
            return response()->json(['success' => false, 'message' => 'Format Barcode tidak dikenali!']);
        }

        $siswa_id = $parts[1];
        $siswa = Siswa::find($siswa_id);

        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Data Siswa tidak ditemukan di sistem!']);
        }

        $absen = Absensi::where('agenda_id', $agenda->id)->where('siswa_id', $siswa->id)->first();

        if ($absen && $absen->status_kehadiran == 'hadir') {
            return response()->json([
                'success' => true, 
                'message' => $siswa->nama_lengkap . ' sudah melakukan absensi hari ini.'
            ]);
        }

        Absensi::updateOrCreate(
            ['agenda_id' => $agenda->id, 'siswa_id' => $siswa->id],
            [
                'waktu_hadir' => Carbon::now()->toTimeString(),
                'status_kehadiran' => 'hadir',
                'metode_absen' => 'barcode'
            ]
        );

        return response()->json([
            'success' => true, 
            'message' => $siswa->nama_lengkap . ' Berhasil Hadir!'
        ]);
    }

    // ========================================================
    // FITUR BARU: INPUT ABSENSI FORMAT GRID
    // ========================================================
    public function gridInput(Request $request)
    {
        $bulanVal = $request->input('bulan', date('Y-m'));
        [$year, $month] = explode('-', $bulanVal);

        $agendas = \App\Models\Agenda::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->orderBy('tanggal', 'asc')
            ->get();

        $ta_ids = $agendas->pluck('tahun_ajaran_id')->filter()->unique()->toArray();

        $siswas = collect();
        if (!empty($ta_ids)) {
            $validSiswaIds = \App\Models\HistoriSiswa::whereIn('tahun_ajaran_id', $ta_ids)->pluck('siswa_id')->unique();
            
            $siswas = \App\Models\Siswa::with(['riwayatHistori' => function ($query) use ($ta_ids) {
                $query->whereIn('tahun_ajaran_id', $ta_ids);
            }, 'riwayatHistori.kelas', 'absensi' => function($q) use ($agendas) {
                $q->whereIn('agenda_id', $agendas->pluck('id'));
            }])->whereIn('id', $validSiswaIds)->get();
        }

        foreach ($siswas as $siswa) {
            $histori = $siswa->riwayatHistori->first();
            $siswa->kelas_laporan = $histori && $histori->kelas ? $histori->kelas->nama_kelas : 'Tanpa Kelas';
            
            $map = [];
            foreach ($siswa->absensi as $absen) {
                $map[$absen->agenda_id] = $absen->status_kehadiran;
            }
            $siswa->absen_map = $map;
        }

        $urutanKelas = [
            'Kelas PG' => 1, 'Kelas TK A' => 2, 'Kelas TK B' => 3,
            'Kelas 1 SD' => 4, 'Kelas 2 SD' => 5, 'Kelas 3 SD' => 6, 'Kelas 4 SD' => 7, 'Kelas 5 SD' => 8, 'Kelas 6 SD' => 9,
            'Kelas 1 SMP' => 10, 'Kelas 2 SMP' => 11, 'Kelas 3 SMP' => 12,
            'Kelas 1 SMA' => 13, 'Kelas 2 SMA' => 14, 'Kelas 3 SMA' => 15,
        ];

        $siswas = $siswas->sortBy(function($siswa) use ($urutanKelas) {
            $namaKelas = $siswa->kelas_laporan;
            $urutan = str_pad($urutanKelas[$namaKelas] ?? 99, 2, '0', STR_PAD_LEFT);
            return $urutan . '-' . $siswa->nama_lengkap;
        })->values();

        $siswaPerKelas = $siswas->groupBy('kelas_laporan');

        $absensiPengajars = \App\Models\AbsensiPengajar::whereIn('agenda_id', $agendas->pluck('id'))->get();
        $absenPengajarMap = [];
        foreach ($absensiPengajars as $ap) {
            $absenPengajarMap[$ap->pengajar_id][$ap->agenda_id] = $ap->status_kehadiran;
        }

        $pengajars = \App\Models\Pengajar::with('jabatan')->where('status', 'aktif')->orderBy('nama_lengkap', 'asc')->get();
        foreach ($pengajars as $p) {
            $p->absen_map = $absenPengajarMap[$p->id] ?? [];
        }

        return view('absensi.grid', compact('agendas', 'siswaPerKelas', 'pengajars', 'bulanVal'));
    }

    public function storeGrid(Request $request)
    {
        $bulanVal = $request->bulan;
        [$year, $month] = explode('-', $bulanVal);

        $agendas = \App\Models\Agenda::whereYear('tanggal', $year)->whereMonth('tanggal', $month)->get();
        
        $kehadiran = $request->input('kehadiran', []); 
        $validSiswaIds = $request->input('siswa_ids', []); 
        $kehadiran_pengajar = $request->input('kehadiran_pengajar', []);
        $validPengajarIds = $request->input('pengajar_ids', []);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($agendas as $agenda) {
                if ($agenda->is_libur) continue; 

                foreach ($validSiswaIds as $siswa_id) {
                    $status = isset($kehadiran[$agenda->id][$siswa_id]) ? 'hadir' : 'alpa';
                    $absenExisting = \App\Models\Absensi::where('agenda_id', $agenda->id)->where('siswa_id', $siswa_id)->first();

                    if ($absenExisting) {
                        $waktuHadir = ($status == 'hadir' && $absenExisting->status_kehadiran != 'hadir') ? now()->format('H:i:s') : $absenExisting->waktu_hadir;
                        if($status != 'hadir') $waktuHadir = null;

                        $absenExisting->update(['status_kehadiran' => $status, 'waktu_hadir' => $waktuHadir]);
                    } else {
                        \App\Models\Absensi::create([
                            'agenda_id' => $agenda->id, 'siswa_id' => $siswa_id,
                            'status_kehadiran' => $status, 'metode_absen' => 'manual',
                            'waktu_hadir' => $status == 'hadir' ? now()->format('H:i:s') : null
                        ]);
                    }
                }

                foreach ($validPengajarIds as $p_id) {
                    $status_p = isset($kehadiran_pengajar[$agenda->id][$p_id]) ? 'hadir' : 'alpa';
                    $absenPExisting = \App\Models\AbsensiPengajar::where('agenda_id', $agenda->id)->where('pengajar_id', $p_id)->first();

                    if ($absenPExisting) {
                        $waktuHadirP = ($status_p == 'hadir' && $absenPExisting->status_kehadiran != 'hadir') ? now()->format('H:i:s') : $absenPExisting->waktu_hadir;
                        if($status_p != 'hadir') $waktuHadirP = null;

                        $absenPExisting->update(['status_kehadiran' => $status_p, 'waktu_hadir' => $waktuHadirP]);
                    } else {
                        \App\Models\AbsensiPengajar::create([
                            'agenda_id' => $agenda->id, 'pengajar_id' => $p_id,
                            'status_kehadiran' => $status_p,
                            'waktu_hadir' => $status_p == 'hadir' ? now()->format('H:i:s') : null
                        ]);
                    }
                }
            }
            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Data absensi 1 bulan penuh berhasil disimpan!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}