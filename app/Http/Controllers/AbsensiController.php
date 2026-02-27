<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Agenda;
use App\Models\Siswa;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    // Menampilkan Halaman Kelola Absensi untuk Admin
    // Menampilkan Halaman Kelola Absensi (Siswa & Pengajar dalam 1 Page)
    // Menampilkan Halaman Kelola Absensi (Siswa & Pengajar dalam 1 Page)
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::now()->toDateString());
        $kelas_id = $request->input('kelas_id');
        $type = $request->input('type', 'siswa'); // Default tab yang terbuka adalah 'siswa'
        $search = $request->input('search'); // Menangkap kata kunci pencarian

        $kelas = \App\Models\Kelas::all();
        $agendas = Agenda::where('tanggal', $tanggal)->get();
        $agenda_ids = $agendas->pluck('id')->toArray();

        // JIKA TAB SISWA YANG AKTIF
        if ($type == 'siswa') {
            $siswas = Siswa::with('kelas')
                ->when($kelas_id, function($q, $kelas_id) { 
                    return $q->where('kelas_id', $kelas_id); 
                })
                // Logika Pencarian Nama / NIS Siswa
                ->when($search, function($q, $search) {
                    return $q->where('nama_lengkap', 'like', "%{$search}%")
                             ->orWhere('id', 'like', "%{$search}%")
                             ->orWhere('nis', 'like', "%{$search}%");
                })
                ->orderBy('nama_lengkap', 'asc')
                ->paginate(8)
                // Pastikan filter search ikut tersimpan saat pindah halaman (pagination)
                ->appends(['tanggal' => $tanggal, 'kelas_id' => $kelas_id, 'type' => 'siswa', 'search' => $search]);
            
            $absensis = Absensi::whereIn('agenda_id', $agenda_ids)->get();
            
            return view('absensi.index', compact('tanggal', 'kelas', 'kelas_id', 'agendas', 'siswas', 'absensis', 'type', 'search'));
        
        // JIKA TAB PENGAJAR YANG AKTIF
        } else {
            $pengajars = \App\Models\Pengajar::orderBy('nama_lengkap', 'asc')
                // Logika Pencarian Nama Pengajar
                ->when($search, function($q, $search) {
                    return $q->where('nama_lengkap', 'like', "%{$search}%");
                })
                ->paginate(8)
                ->appends(['tanggal' => $tanggal, 'type' => 'pengajar', 'search' => $search]);
                
            $absensiPengajars = \App\Models\AbsensiPengajar::whereIn('agenda_id', $agenda_ids)->get();
            
            return view('absensi.index', compact('tanggal', 'kelas', 'kelas_id', 'agendas', 'pengajars', 'absensiPengajars', 'type', 'search'));
        }
    }

    // Memproses Absensi Manual KHUSUS PENGAJAR
    public function updateManualPengajar(Request $request)
    {
        $request->validate([
            'pengajar_id' => 'required',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpa'
        ]);

        $agendas = Agenda::where('tanggal', $request->tanggal)->get();

        if ($agendas->isEmpty()) {
            return back()->with('error', 'Tidak ada jadwal pada tanggal tersebut.');
        }

        foreach ($agendas as $agenda) {
            $waktu = ($request->status == 'hadir') ? Carbon::now()->toTimeString() : null;

            \App\Models\AbsensiPengajar::updateOrCreate(
                ['agenda_id' => $agenda->id, 'pengajar_id' => $request->pengajar_id],
                ['status_kehadiran' => $request->status, 'waktu_hadir' => $waktu]
            );
        }

        return back()->with('success', 'Status kehadiran Pengajar berhasil diperbarui!');
    }

    // 1. Menampilkan Halaman Kamera Scanner untuk Pengajar
    public function scanner()
    {
        // Ambil tanggal hari ini
        $hari_ini = Carbon::now()->toDateString();
        
        // Cek apakah ada kegiatan hari ini (untuk info di layar)
        $agendas = Agenda::where('tanggal', $hari_ini)->orderBy('waktu_mulai')->get();

        return view('absensi.scanner', compact('agendas', 'hari_ini'));
    }

    // 2. Memproses Data dari Kamera (AJAX - Tanpa Loading Halaman)
    public function prosesScan(Request $request)
    {
        $barcode = $request->barcode; // Contoh data yang masuk: "SMB-5"
        $hari_ini = Carbon::now()->toDateString();

        // Pecah teks barcode untuk mengambil ID Siswa
        $parts = explode('-', $barcode);
        
        // Validasi apakah format barcode benar (Harus diawali 'SMB-' dan diikuti angka)
        if (count($parts) != 2 || $parts[0] != 'SMB' || !is_numeric($parts[1])) {
            return response()->json([
                'success' => false, 
                'message' => 'Format Barcode tidak dikenali!'
            ]);
        }

        $siswa_id = $parts[1];

        // Cek apakah siswa ada di database
        $siswa = Siswa::find($siswa_id);
        if (!$siswa) {
            return response()->json([
                'success' => false, 
                'message' => 'Data Siswa tidak ditemukan di sistem!'
            ]);
        }

        // Cari semua agenda/jadwal HARI INI
        $agendas = Agenda::where('tanggal', $hari_ini)->get();

        if ($agendas->isEmpty()) {
            return response()->json([
                'success' => false, 
                'message' => 'Tidak ada jadwal kegiatan untuk hari ini.'
            ]);
        }

        $absenTerisi = 0;

        // KUNCI UTAMA: Looping semua agenda hari ini, dan absenkan siswa ini untuk semuanya!
        foreach ($agendas as $agenda) {
            // updateOrCreate akan mengecek: jika sudah absen, maka update waktunya. Jika belum, buat baru.
            $absen = Absensi::updateOrCreate(
                [
                    'agenda_id' => $agenda->id,
                    'siswa_id' => $siswa->id
                ],
                [
                    'waktu_hadir' => Carbon::now()->toTimeString(),
                    'status_kehadiran' => 'hadir',
                    'metode_absen' => 'barcode'
                ]
            );

            // Jika ini absen baru (baru saja diciptakan), tambah counter
            if ($absen->wasRecentlyCreated) {
                $absenTerisi++;
            }
        }

        // Pesan balasan ke HP Pengajar
        if ($absenTerisi > 0) {
            return response()->json([
                'success' => true, 
                'message' => $siswa->nama_lengkap . ' Berhasil Hadir!'
            ]);
        } else {
            // Jika wasRecentlyCreated false, berarti dia nge-scan 2 kali di hari yang sama
            return response()->json([
                'success' => true, 
                'message' => $siswa->nama_lengkap . ' sudah melakukan absensi hari ini.'
            ]);
        }
    }
    
    // Memproses Absensi Manual (Izin/Sakit/Alpa) dari Halaman Admin
    public function updateManual(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpa'
        ]);

        // Cari semua kegiatan pada tanggal tersebut
        $agendas = Agenda::where('tanggal', $request->tanggal)->get();

        if ($agendas->isEmpty()) {
            return back()->with('error', 'Tidak ada jadwal pada tanggal tersebut.');
        }

        // Loop dan update status ke semua kegiatan hari itu
        foreach ($agendas as $agenda) {
            
            // Atur waktu: Jika hadir manual, catat jam sekarang. Jika izin/sakit/alpa, kosongkan waktunya.
            $waktu = ($request->status == 'hadir') ? Carbon::now()->toTimeString() : null;

            Absensi::updateOrCreate(
                [
                    'agenda_id' => $agenda->id,
                    'siswa_id' => $request->siswa_id
                ],
                [
                    'status_kehadiran' => $request->status,
                    'metode_absen' => 'manual', // Tandai bahwa ini diinput manual oleh Admin
                    'waktu_hadir' => $waktu
                ]
            );
        }

        return back()->with('success', 'Status kehadiran siswa berhasil diperbarui!');
    }
}