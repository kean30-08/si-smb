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

        // CEK HAK AKSES
        $isPic = $selectedAgenda ? $this->isAuthorizedPic($selectedAgenda) : false;

        // TAB SISWA
        if ($type == 'siswa') {
            $siswas = Siswa::with('nilaiKehadiranAktif.kelas')
                ->when($kelas_id, function($query, $kelas_id) { 
                    return $query->whereHas('nilaiKehadiranAktif', function($q) use ($kelas_id) {
                        $q->where('kelas_id', $kelas_id);
                    });
                })
                ->when($search, function($q, $search) {
                    return $q->where('nama_lengkap', 'like', "%{$search}%")
                             ->orWhere('nis', 'like', "%{$search}%");
                })
                ->orderBy('nama_lengkap', 'asc')
                ->paginate(15)
                ->appends(['tanggal' => $tanggal, 'kelas_id' => $kelas_id, 'type' => 'siswa', 'search' => $search]);
            
            // Ambil absensi berdasarkan jangkar harian
            $absensis = $agenda_id ? Absensi::where('agenda_id', $agenda_id)->get() : collect();
            
            return view('absensi.index', compact('tanggal', 'kelas', 'kelas_id', 'agenda_id', 'selectedAgenda', 'siswas', 'absensis', 'type', 'search', 'penanggungJawab', 'isPic'));
        
        // TAB PENGAJAR
        } else {
            $pengajars = Pengajar::orderBy('nama_lengkap', 'asc')
                ->when($search, function($q, $search) {
                    return $q->where('nama_lengkap', 'like', "%{$search}%");
                })
                ->paginate(15)
                ->appends(['tanggal' => $tanggal, 'type' => 'pengajar', 'search' => $search]);
                
            $absensiPengajars = $agenda_id ? AbsensiPengajar::where('agenda_id', $agenda_id)->get() : collect();
            
            return view('absensi.index', compact('tanggal', 'kelas', 'kelas_id', 'agenda_id', 'selectedAgenda', 'pengajars', 'absensiPengajars', 'type', 'search', 'penanggungJawab', 'isPic'));
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
            return back()->with('error', 'Akses Ditolak! Anda bukan PIC yang ditugaskan untuk hari ini.');
        }

        $waktu = ($request->status == 'hadir') ? Carbon::now()->toTimeString() : null;

        AbsensiPengajar::updateOrCreate(
            ['agenda_id' => $request->agenda_id, 'pengajar_id' => $request->pengajar_id],
            ['status_kehadiran' => $request->status, 'waktu_hadir' => $waktu]
        );

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
            return back()->with('error', 'Akses Ditolak! Anda bukan PIC yang ditugaskan untuk hari ini.');
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
}