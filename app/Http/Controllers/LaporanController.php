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
    /**
     * Menampilkan halaman utama menu laporan.
     */
    public function index()
    {
        $kelas = Kelas::all();
        // Halaman Index kini bersih, murni mengandalkan tanggal kalender
        return view('laporan.index', compact('kelas'));
    }

    /**
     * Menghasilkan laporan rekapitulasi keaktifan siswa dalam format PDF.
     */
    public function cetakKehadiranSiswa(Request $request)
    {
        ini_set('memory_limit', '256M');
        
        $mulai = $request->tanggal_mulai;
        $selesai = $request->tanggal_selesai;
        $kelas_id = $request->kelas_id;

        // 1. Dapatkan semua jadwal dalam rentang waktu
        $agendas = Agenda::whereBetween('tanggal', [$mulai, $selesai])
            ->orderBy('tanggal', 'asc')
            ->get();

        if ($agendas->isEmpty()) {
            return back()->with('error', 'Tidak ada data jadwal kegiatan pada rentang tanggal tersebut.');
        }

        // KUNCI UTAMA: Ambil otomatis ID Tahun Ajaran dari jadwal-jadwal tersebut
        $ta_ids = $agendas->pluck('tahun_ajaran_id')->unique()->toArray();

        // KELOMPOKKAN PER BULAN (Untuk Kolom PDF)
        $agendasPerBulan = $agendas->pluck('tanggal')->unique()->values()->groupBy(function($date) {
            return Carbon::parse($date)->format('Y-m'); 
        });

        // TAMBAHAN: Buat Peta Status Libur berdasarkan tanggal
       // TAMBAHAN: Buat Peta Status Libur & Ambil Nama Kegiatannya
        $agendaStatusMap = $agendas->where('is_libur', true)->pluck('nama_kegiatan', 'tanggal')->toArray();

        // 2. Filter Siswa yang BENAR-BENAR TERDAFTAR pada Tahun Ajaran tersebut (Mencegah data masa depan bocor)
        $historiQuery = HistoriSiswa::whereIn('tahun_ajaran_id', $ta_ids);
        if ($kelas_id != 'semua') {
             $historiQuery->where('kelas_id', $kelas_id);
        }
        $validSiswaIds = $historiQuery->pluck('siswa_id')->unique();

        // 3. Ambil data Siswa beserta absensinya
        $siswas = Siswa::with(['riwayatHistori' => function ($query) use ($ta_ids) {
            $query->whereIn('tahun_ajaran_id', $ta_ids);
        }, 'riwayatHistori.kelas', 'absensi' => function($q) use ($mulai, $selesai) {
            $q->whereHas('agenda', function($q2) use ($mulai, $selesai) {
                $q2->whereBetween('tanggal', [$mulai, $selesai]);
            });
        }, 'absensi.agenda'])->whereIn('id', $validSiswaIds)->get();

        // 4. Map data kehadiran menjadi format sederhana: [Tanggal => Status]
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

        // 5. URUTAN HALAMAN (Semua Tingkatan Digabungkan berurutan dari PG ke SMA)
        $urutanKelas = [
            'Kelas PG' => 1, 'Kelas TK A' => 2, 'Kelas TK B' => 3,
            'Kelas 1 SD' => 4, 'Kelas 2 SD' => 5, 'Kelas 3 SD' => 6, 'Kelas 4 SD' => 7, 'Kelas 5 SD' => 8, 'Kelas 6 SD' => 9,
            'Kelas 1 SMP' => 10, 'Kelas 2 SMP' => 11, 'Kelas 3 SMP' => 12,
            'Kelas 1 SMA' => 13, 'Kelas 2 SMA' => 14, 'Kelas 3 SMA' => 15,
        ];

        // Urutkan berdasarkan kelas_laporan terkecil, lalu abjad nama
        $siswas = $siswas->sortBy(function($siswa) use ($urutanKelas) {
            $namaKelas = $siswa->kelas_laporan;
            $urutan = str_pad($urutanKelas[$namaKelas] ?? 99, 2, '0', STR_PAD_LEFT);
            return $urutan . '-' . $siswa->nama_lengkap;
        })->values();

        // Dapatkan nama Tahun Ajaran secara dinamis untuk Kop Surat PDF
        $namaTAs = TahunAjaran::whereIn('id', $ta_ids)->pluck('tahun_ajaran')->toArray();
        $nama_ta = empty($namaTAs) ? 'Tahun Ajaran Tidak Diketahui' : implode(' & ', $namaTAs);

        // 6. Cetak PDF (Pastikan agendaStatusMap dikirim ke View)
        $pdf = Pdf::loadView('laporan.pdf_kehadiran_siswa', compact('agendasPerBulan', 'siswas', 'nama_ta', 'agendaStatusMap'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Laporan_Absensi_Siswa_Grid.pdf');
    }

    public function cetakAgenda(Request $request)
    {
        $mulai = $request->tanggal_mulai;
        $selesai = $request->tanggal_selesai;

        // Ambil Agenda Sesuai Tanggal Saja
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

        // Teks Tahun Ajaran Dinamis
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

        // 1. Buat Peta Status Libur & Ambil Nama Kegiatannya
        $agendaStatusMap = $agendas->where('is_libur', true)->pluck('nama_kegiatan', 'tanggal')->toArray();

        // 2. Kelompokkan Agenda Per Bulan (Untuk Kolom Grid PDF)
        $agendasPerBulan = $agendas->pluck('tanggal')->unique()->values()->groupBy(function($date) {
            return Carbon::parse($date)->format('Y-m'); 
        });

        // 3. Tarik semua pengajar dan mapping absensinya berdasarkan tanggal
        $pengajars = Pengajar::orderBy('nama_lengkap', 'asc')->get();

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

        // 4. Cari Kepala Sekolah yang Aktif untuk Tanda Tangan
        $kepalaSekolah = Pengajar::whereHas('jabatan', function($q) {
            $q->where('nama_jabatan', 'like', '%Kepala Sekolah%');
        })->where('status', 'aktif')->first();
        
        $namaKepalaSekolah = $kepalaSekolah ? $kepalaSekolah->nama_lengkap : '...............................';

        // 5. Teks Tahun Ajaran Dinamis
        $ta_ids = $agendas->pluck('tahun_ajaran_id')->unique()->toArray();
        $namaTAs = TahunAjaran::whereIn('id', $ta_ids)->pluck('tahun_ajaran')->toArray();
        $nama_ta = empty($namaTAs) ? '-' : implode(' & ', $namaTAs);
        
        $pdf = Pdf::loadView('laporan.pdf_pengajar', compact('pengajars', 'agendasPerBulan', 'agendaStatusMap', 'namaKepalaSekolah', 'nama_ta', 'mulai', 'selesai'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Laporan_Data_Kehadiran_Pengurus.pdf');
    }
}