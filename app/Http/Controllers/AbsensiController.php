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
     * Fungsi Bantuan untuk mengecek apakah user adalah PIC atau Admin
     */
    private function isAuthorizedPic($agenda)
    {
        $user = auth()->user();
        
        // Admin & Kepala Sekolah bebas mengakses semuanya
        if ($user->isAdmin()) return true;

        $pengajar = $user->pengajar;
        if (!$pengajar) return false;

        // Cek apakah ID pengajar yang sedang login ada di dalam daftar PIC agenda ini
        return $agenda->penanggungJawab->contains('id', $pengajar->id);
    }

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
        $isPic = $selectedAgenda ? $this->isAuthorizedPic($selectedAgenda) : false;
        $isLibur = $selectedAgenda ? $selectedAgenda->is_libur : false; // DEKLARASI DI SINI

        // TAB SISWA
        if ($type == 'siswa') {
            // Ambil ID Tahun Ajaran dari agenda yang sedang dibuka
            $agendaTaId = $selectedAgenda ? $selectedAgenda->tahun_ajaran_id : null;

            if ($agendaTaId) {
                // 1. Cari ID siswa yang BENAR-BENAR terdaftar pada Tahun Ajaran (masa lalu/kini) tersebut
                $historiQuery = \App\Models\HistoriSiswa::where('tahun_ajaran_id', $agendaTaId);
                if ($kelas_id) {
                    $historiQuery->where('kelas_id', $kelas_id);
                }
                $validSiswaIds = $historiQuery->pluck('siswa_id');

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
            } else {
                // Jika tidak ada jadwal/agenda di tanggal tersebut, kembalikan paginasi kosong
                $siswas = Siswa::whereRaw('1 = 0')->paginate(15);
            }
            
            $absensis = $agenda_id ? Absensi::where('agenda_id', $agenda_id)->get() : collect();
            
            return view('absensi.index', compact('tanggal', 'kelas', 'kelas_id', 'agenda_id', 'selectedAgenda', 'siswas', 'absensis', 'type', 'search', 'penanggungJawab', 'isPic', 'isLibur'));
            
        // TAB PENGAJAR
        } else {
            $pengajars = Pengajar::orderBy('nama_lengkap', 'asc')
                ->when($search, function($q, $search) {
                    return $q->where('nama_lengkap', 'like', "%{$search}%");
                })
                ->paginate(15)
                ->appends(['tanggal' => $tanggal, 'type' => 'pengajar', 'search' => $search]);
                
            $absensiPengajars = $agenda_id ? AbsensiPengajar::where('agenda_id', $agenda_id)->get() : collect();
            
            return view('absensi.index', compact('tanggal', 'kelas', 'kelas_id', 'agenda_id', 'selectedAgenda', 'pengajars', 'absensiPengajars', 'type', 'search', 'penanggungJawab', 'isPic', 'isLibur'));
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
        if (!$agenda || !$this->isAuthorizedPic($agenda)) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Akses Ditolak! Anda bukan PIC.']);
            return back()->with('error', 'Akses Ditolak! Anda bukan PIC yang ditugaskan untuk hari ini.');
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
        if (!$agenda || !$this->isAuthorizedPic($agenda)) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => 'Akses Ditolak! Anda bukan PIC.']);
            return back()->with('error', 'Akses Ditolak! Anda bukan PIC yang ditugaskan untuk hari ini.');
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

        if (!$this->isAuthorizedPic($agenda)) {
            return redirect()->route('absensi.index')->with('error', 'Akses Ditolak! Fitur Scanner hanya bisa dibuka oleh PIC Absensi.');
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

        // TAMBAHAN KEAMANAN LIBUR
        if ($agenda->is_libur) {
            return response()->json(['success' => false, 'message' => 'Hari Libur! Fitur Scanner dinonaktifkan.']);
        }

        // Lapis keamanan Scanner via AJAX
        if (!$this->isAuthorizedPic($agenda)) {
            return response()->json(['success' => false, 'message' => 'Akses Ditolak! Anda tidak memiliki izin memindai pada hari ini.']);
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
        // Default ke bulan saat ini jika tidak ada input
        $bulanVal = $request->input('bulan', date('Y-m'));
        [$year, $month] = explode('-', $bulanVal);

        // Ambil semua agenda (jadwal) di bulan tersebut
        $agendas = \App\Models\Agenda::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->orderBy('tanggal', 'asc')
            ->get();

        // Cari tahu TA mana saja yang berkaitan dengan jadwal di bulan ini
        $ta_ids = $agendas->pluck('tahun_ajaran_id')->filter()->unique()->toArray();

        $siswas = collect();
        if (!empty($ta_ids)) {
            // Ambil ID siswa yang terdaftar di kelas pada TA tersebut
            $validSiswaIds = \App\Models\HistoriSiswa::whereIn('tahun_ajaran_id', $ta_ids)->pluck('siswa_id')->unique();
            
            // Tarik data siswa beserta historinya dan absensinya (HANYA absensi di bulan ini)
            $siswas = \App\Models\Siswa::with(['riwayatHistori' => function ($query) use ($ta_ids) {
                $query->whereIn('tahun_ajaran_id', $ta_ids);
            }, 'riwayatHistori.kelas', 'absensi' => function($q) use ($agendas) {
                $q->whereIn('agenda_id', $agendas->pluck('id'));
            }])->whereIn('id', $validSiswaIds)->get();
        }

        // Petakan data agar mudah dibaca oleh View Blade
        foreach ($siswas as $siswa) {
            $histori = $siswa->riwayatHistori->first();
            $siswa->kelas_laporan = $histori && $histori->kelas ? $histori->kelas->nama_kelas : 'Tanpa Kelas';
            
            $map = [];
            foreach ($siswa->absensi as $absen) {
                $map[$absen->agenda_id] = $absen->status_kehadiran;
            }
            $siswa->absen_map = $map;
        }

        // Urutkan siswa persis seperti urutan di Laporan PDF
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

        // Kelompokkan berdasarkan kelas
       // Kelompokkan berdasarkan kelas
        $siswaPerKelas = $siswas->groupBy('kelas_laporan');

        // TAMBAHAN: Tarik Data Pengajar Aktif dan petakan absensinya
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
        
        // Data yang dicentang oleh user (Hanya yang dikirim oleh form)
        $kehadiran = $request->input('kehadiran', []); 
        
        // Array id siswa rahasia untuk tahu siapa saja yang TIDAK dicentang (supaya di-Alpa-kan)
        // Array id siswa rahasia untuk tahu siapa saja yang TIDAK dicentang (supaya di-Alpa-kan)
        $validSiswaIds = $request->input('siswa_ids', []); 
        
        // TAMBAHAN: Tangkap array centangan pengajar
        $kehadiran_pengajar = $request->input('kehadiran_pengajar', []);
        $validPengajarIds = $request->input('pengajar_ids', []);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($agendas as $agenda) {
                // Jangan utak-atik hari libur
                if ($agenda->is_libur) continue; 

                // 1. SIMPAN ABSENSI SISWA
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

                // 2. SIMPAN ABSENSI PENGAJAR
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