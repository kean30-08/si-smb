<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahun_ajarans = TahunAjaran::orderBy('created_at', 'desc')->get();
        return view('tahun_ajaran.index', compact('tahun_ajarans'));
    }

    public function create()
    {
        return view('tahun_ajaran.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi input mentah
        $request->validate([
            'tahun_awal' => 'required|numeric|min:2000|max:2099',
            'semester' => 'required|in:Ganjil,Genap'
        ]);

        // 2. Gabungkan format: "2026/2027 Ganjil"
        $tahun_akhir = $request->tahun_awal + 1;
        $format_tahun_ajaran = $request->tahun_awal . '/' . $tahun_akhir . ' ' . $request->semester;

        // 3. Masukkan kembali ke request untuk dicek duplikasinya
        $request->merge(['tahun_ajaran' => $format_tahun_ajaran]);

        $request->validate([
            'tahun_ajaran' => 'unique:tahun_ajarans,tahun_ajaran'
        ], [
            'tahun_ajaran.unique' => 'Tahun Ajaran sudah ada di dalam sistem. Tidak boleh duplikat!'
        ]);

        // 4. Simpan ke database
        TahunAjaran::create([
            'tahun_ajaran' => $format_tahun_ajaran,
            'status' => 'tidak aktif' // Default saat dibuat selalu tidak aktif
        ]);

        return redirect()->route('tahun_ajaran.index')->with('success', 'Tahun Ajaran baru berhasil ditambahkan.');
    }

    public function edit(TahunAjaran $tahun_ajaran)
    {
        return view('tahun_ajaran.edit', compact('tahun_ajaran'));
    }

    public function update(Request $request, TahunAjaran $tahun_ajaran)
    {
        $request->validate([
            'tahun_awal' => 'required|numeric|min:2000|max:2099',
            'semester' => 'required|in:Ganjil,Genap'
        ]);

        $tahun_akhir = $request->tahun_awal + 1;
        $format_tahun_ajaran = $request->tahun_awal . '/' . $tahun_akhir . ' ' . $request->semester;

        $request->merge(['tahun_ajaran' => $format_tahun_ajaran]);

        $request->validate([
            'tahun_ajaran' => 'unique:tahun_ajarans,tahun_ajaran,' . $tahun_ajaran->id
        ], [
            'tahun_ajaran.unique' => 'Tahun Ajaran "' . $format_tahun_ajaran . '" sudah ada di dalam sistem. Tidak boleh duplikat!'
        ]);

        $tahun_ajaran->update([
            'tahun_ajaran' => $format_tahun_ajaran
        ]);

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
            // 1. Matikan semua tahun ajaran terlebih dahulu
            TahunAjaran::query()->update(['status' => 'tidak aktif']);
            
            // 2. Aktifkan tahun ajaran yang dipilih
            $tahun_ajaran->update(['status' => 'aktif']);

            // 3. AUTO-MIGRASI SISWA (MURNI COPY, TANPA NAIK KELAS)
            $siswas = \App\Models\Siswa::where('status', 'aktif')->get();
            
            foreach ($siswas as $siswa) {
                // Cari riwayat pendaftaran terakhir siswa ini di semester/tahun SEBELUMNYA
                $lastNilai = \App\Models\NilaiKehadiran::where('siswa_id', $siswa->id)
                                ->where('tahun_ajaran_id', '!=', $tahun_ajaran->id) 
                                ->orderBy('id', 'desc')
                                ->first();
                
                // Ambil kelas terakhirnya (Jika tidak ada, set null)
                $kelasIdLama = $lastNilai ? $lastNilai->kelas_id : null;

                // Cek agar tidak mendaftar ganda jika tombol diklik berkali-kali
                $sudahDaftar = \App\Models\NilaiKehadiran::where('siswa_id', $siswa->id)
                                    ->where('tahun_ajaran_id', $tahun_ajaran->id)
                                    ->exists();

                if (!$sudahDaftar) {
                    // Daftarkan ke Tahun Ajaran yang diaktifkan dengan KELAS YANG SAMA
                    \App\Models\NilaiKehadiran::create([
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran_id' => $tahun_ajaran->id,
                        'kelas_id' => $kelasIdLama, // TETAP DI KELAS YANG SAMA
                        'total_poin' => 0 // Lembaran baru, poin kembali 0
                    ]);
                }
            }
        });

        return back()->with('success', 'Tahun Ajaran ' . $tahun_ajaran->tahun_ajaran . ' diaktifkan! Siswa berhasil dimigrasikan ke semester ini dengan kelas tetap.');
    }
}