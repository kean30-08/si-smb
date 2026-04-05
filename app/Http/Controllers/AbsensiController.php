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
     * Menampilkan Halaman Kelola Absensi (Siswa & Pengajar dalam 1 Page)
     * Mendukung filter tanggal, kelas, pencarian, dan tab aktif.
     */
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::now()->toDateString());
        $kelas_id = $request->input('kelas_id');
        $type = $request->input('type', 'siswa'); 
        $search = $request->input('search'); 

        $kelas = Kelas::all();
        $agendas = Agenda::where('tanggal', $tanggal)->get();
        $agenda_ids = $agendas->pluck('id')->toArray();

        $penanggungJawab = $agendas->isNotEmpty() ? $agendas->first()->penanggungJawab : null;

        // TAB SISWA
        if ($type == 'siswa') {
            $siswas = Siswa::with('kelas')
                ->when($kelas_id, function($q, $kelas_id) { 
                    return $q->where('kelas_id', $kelas_id); 
                })
                ->when($search, function($q, $search) {
                    return $q->where('nama_lengkap', 'like', "%{$search}%")
                             ->orWhere('id', 'like', "%{$search}%")
                             ->orWhere('nis', 'like', "%{$search}%");
                })
                ->orderBy('nama_lengkap', 'asc')
                ->paginate(8)
                ->appends(['tanggal' => $tanggal, 'kelas_id' => $kelas_id, 'type' => 'siswa', 'search' => $search]);
            
            $absensis = Absensi::whereIn('agenda_id', $agenda_ids)->get();
            
            return view('absensi.index', compact('tanggal', 'kelas', 'kelas_id', 'agendas', 'siswas', 'absensis', 'type', 'search', 'penanggungJawab'));
        
        // TAB PENGAJAR
        } else {
            $pengajars = Pengajar::orderBy('nama_lengkap', 'asc')
                ->when($search, function($q, $search) {
                    return $q->where('nama_lengkap', 'like', "%{$search}%");
                })
                ->paginate(8)
                ->appends(['tanggal' => $tanggal, 'type' => 'pengajar', 'search' => $search]);
                
            $absensiPengajars = AbsensiPengajar::whereIn('agenda_id', $agenda_ids)->get();
            
            return view('absensi.index', compact('tanggal', 'kelas', 'kelas_id', 'agendas', 'pengajars', 'absensiPengajars', 'type', 'search', 'penanggungJawab'));
        }
    }

    /**
     * Memproses Absensi Manual KHUSUS PENGAJAR
     * Melakukan sinkronisasi kehadiran pada seluruh agenda di tanggal terpilih.
     */
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

        DB::transaction(function () use ($agendas, $request) {
            foreach ($agendas as $agenda) {
                $waktu = ($request->status == 'hadir') ? Carbon::now()->toTimeString() : null;

                AbsensiPengajar::updateOrCreate(
                    ['agenda_id' => $agenda->id, 'pengajar_id' => $request->pengajar_id],
                    ['status_kehadiran' => $request->status, 'waktu_hadir' => $waktu]
                );
            }
        });

        return back()->with('success', 'Status kehadiran Pengajar berhasil diperbarui!');
    }

    /**
     * Menampilkan Halaman Kamera Scanner untuk Pengajar
     */
    public function scanner()
    {
        $hari_ini = Carbon::now()->toDateString();
        $agendas = Agenda::where('tanggal', $hari_ini)->orderBy('waktu_mulai')->get();

        return view('absensi.scanner', compact('agendas', 'hari_ini'));
    }

    /**
     * Memproses Data dari Kamera (AJAX)
     * Mengonversi barcode menjadi data kehadiran otomatis untuk semua agenda hari ini.
     */
    public function prosesScan(Request $request)
    {
        $barcode = $request->barcode; 
        $hari_ini = Carbon::now()->toDateString();

        $parts = explode('-', $barcode);
        
        if (count($parts) != 2 || $parts[0] != 'SMB' || !is_numeric($parts[1])) {
            return response()->json([
                'success' => false, 
                'message' => 'Format Barcode tidak dikenali!'
            ]);
        }

        $siswa_id = $parts[1];
        $siswa = Siswa::find($siswa_id);

        if (!$siswa) {
            return response()->json([
                'success' => false, 
                'message' => 'Data Siswa tidak ditemukan di sistem!'
            ]);
        }

        $agendas = Agenda::where('tanggal', $hari_ini)->get();

        if ($agendas->isEmpty()) {
            return response()->json([
                'success' => false, 
                'message' => 'Tidak ada jadwal kegiatan untuk hari ini.'
            ]);
        }

        $absenTerisi = 0;

        DB::transaction(function () use ($agendas, $siswa, &$absenTerisi) {
            foreach ($agendas as $agenda) {
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

                if ($absen->wasRecentlyCreated) {
                    $absenTerisi++;
                }
            }
        });

        if ($absenTerisi > 0) {
            return response()->json([
                'success' => true, 
                'message' => $siswa->nama_lengkap . ' Berhasil Hadir!'
            ]);
        } else {
            return response()->json([
                'success' => true, 
                'message' => $siswa->nama_lengkap . ' sudah melakukan absensi hari ini.'
            ]);
        }
    }
    
    /**
     * Memproses Absensi Manual (Izin/Sakit/Alpa) dari Halaman Admin
     */
    public function updateManual(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,izin,sakit,alpa'
        ]);

        $agendas = Agenda::where('tanggal', $request->tanggal)->get();

        if ($agendas->isEmpty()) {
            return back()->with('error', 'Tidak ada jadwal pada tanggal tersebut.');
        }

        DB::transaction(function () use ($agendas, $request) {
            foreach ($agendas as $agenda) {
                $waktu = ($request->status == 'hadir') ? Carbon::now()->toTimeString() : null;

                Absensi::updateOrCreate(
                    [
                        'agenda_id' => $agenda->id,
                        'siswa_id' => $request->siswa_id
                    ],
                    [
                        'status_kehadiran' => $request->status,
                        'metode_absen' => 'manual', 
                        'waktu_hadir' => $waktu
                    ]
                );
            }
        });

        return back()->with('success', 'Status kehadiran siswa berhasil diperbarui!');
    }
}