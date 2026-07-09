<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class TahunAjaranController extends Controller
{
    public function index()
    {
        // Akan otomatis mengurutkan: 2026/2027 Ganjil -> 2025/2026 Genap -> 2025/2026 Ganjil
        $tahun_ajarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        return view('tahun_ajaran.index', compact('tahun_ajarans'));
    }

    public function create() { return view('tahun_ajaran.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_awal' => 'required|numeric|min:2000|max:2099',
            'semester' => 'required|in:Ganjil,Genap'
        ]);

        $tahun_akhir = $request->tahun_awal + 1;
        $format_tahun_ajaran = $request->tahun_awal . '/' . $tahun_akhir . ' ' . $request->semester;
        $request->merge(['tahun_ajaran' => $format_tahun_ajaran]);
        $request->validate(['tahun_ajaran' => 'unique:tahun_ajarans,tahun_ajaran']);

        TahunAjaran::create([
            'tahun_ajaran' => $format_tahun_ajaran,
            'status' => 'tidak aktif'
        ]);

        return redirect()->route('tahun_ajaran.index')->with('success', 'Tahun Ajaran baru berhasil ditambahkan.');
    }

    public function edit(TahunAjaran $tahun_ajaran) { return view('tahun_ajaran.edit', compact('tahun_ajaran')); }

    public function update(Request $request, TahunAjaran $tahun_ajaran)
    {
        $request->validate([
            'tahun_awal' => 'required|numeric|min:2000|max:2099',
            'semester' => 'required|in:Ganjil,Genap'
        ]);

        $tahun_akhir = $request->tahun_awal + 1;
        $format_tahun_ajaran = $request->tahun_awal . '/' . $tahun_akhir . ' ' . $request->semester;
        $request->merge(['tahun_ajaran' => $format_tahun_ajaran]);
        $request->validate(['tahun_ajaran' => 'unique:tahun_ajarans,tahun_ajaran,' . $tahun_ajaran->id]);

        $tahun_ajaran->update(['tahun_ajaran' => $format_tahun_ajaran]);
        return redirect()->route('tahun_ajaran.index')->with('success', 'Tahun Ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahun_ajaran)
    {
        if ($tahun_ajaran->status == 'aktif') {
            return back()->with('error', 'Tidak dapat menghapus Tahun Ajaran yang sedang Aktif!');
        }
        $tahun_ajaran->delete();
        return redirect()->route('tahun_ajaran.index')->with('success', 'Tahun Ajaran berhasil dihapus.');
    }

    public function aktifkan(TahunAjaran $tahun_ajaran)
    {
        DB::transaction(function () use ($tahun_ajaran) {
            TahunAjaran::query()->update(['status' => 'tidak aktif']);
            $tahun_ajaran->update(['status' => 'aktif']);

            $siswas = \App\Models\Siswa::where('status', 'aktif')->get();
            $namaTaBaru = $tahun_ajaran->tahun_ajaran; 
            $tahunAwalBaru = (int) substr($namaTaBaru, 0, 4); 
            
            // Peta Naik Kelas Lanjutan (Menambahkan TK A dan TK B)
            $urutanKelas = [
                'Kelas PG' => 'Kelas TK A',
                'Kelas TK A' => 'Kelas TK B',
                'Kelas TK B' => 'Kelas 1 SD',
                'Kelas 1 SD' => 'Kelas 2 SD',
                'Kelas 2 SD' => 'Kelas 3 SD',
                'Kelas 3 SD' => 'Kelas 4 SD',
                'Kelas 4 SD' => 'Kelas 5 SD',
                'Kelas 5 SD' => 'Kelas 6 SD',
                'Kelas 6 SD' => 'Kelas 1 SMP',
                'Kelas 1 SMP' => 'Kelas 2 SMP',
                'Kelas 2 SMP' => 'Kelas 3 SMP',
                'Kelas 3 SMP' => 'Kelas 1 SMA',
                'Kelas 1 SMA' => 'Kelas 2 SMA',
                'Kelas 2 SMA' => 'Kelas 3 SMA'
            ];

            foreach ($siswas as $siswa) {
                // Cari histori MASA LALU terdekat berdasarkan urutan nama TA
                $pastHistori = \App\Models\HistoriSiswa::with(['tahunAjaran', 'kelas'])
                                ->where('siswa_id', $siswa->id)
                                ->whereHas('tahunAjaran', function($q) use ($namaTaBaru) {
                                    $q->where('tahun_ajaran', '<', $namaTaBaru);
                                })
                                ->get()
                                ->sortByDesc(function($h) { return $h->tahunAjaran->tahun_ajaran; })
                                ->first();
                
                $kelasIdBaru = null; 

                if ($pastHistori) {
                    $kelasIdLama = $pastHistori->kelas_id;
                    $namaTaLama = $pastHistori->tahunAjaran->tahun_ajaran;
                    $tahunAwalLama = (int) substr($namaTaLama, 0, 4);
                    
                    $kelasIdBaru = $kelasIdLama; // Default: Kelas Tetap (jika hanya beda semester)
                    
                    // Logika Eksekusi Naik Kelas jika Beda Tahun Awal
                    if ($tahunAwalBaru > $tahunAwalLama) {
                        $namaKelasLama = $pastHistori->kelas->nama_kelas ?? '';
                        if (array_key_exists($namaKelasLama, $urutanKelas)) {
                            $namaKelasBaru = $urutanKelas[$namaKelasLama];
                            $kelasBaruObj = \App\Models\Kelas::where('nama_kelas', $namaKelasBaru)->first();
                            if ($kelasBaruObj) {
                                $kelasIdBaru = $kelasBaruObj->id;
                            }
                        }
                    }
                } else {
                    continue; 
                }

                $sudahDaftar = \App\Models\HistoriSiswa::where('siswa_id', $siswa->id)
                                    ->where('tahun_ajaran_id', $tahun_ajaran->id)
                                    ->exists();

                if (!$sudahDaftar && $kelasIdBaru) {
                    \App\Models\HistoriSiswa::create([
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran_id' => $tahun_ajaran->id,
                        'kelas_id' => $kelasIdBaru,
                    ]);
                }
            }
        });

        return back()->with('success', 'Tahun Ajaran ' . $tahun_ajaran->tahun_ajaran . ' diaktifkan! Siswa berhasil dimigrasikan.');
    }
}