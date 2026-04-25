<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NilaiKehadiran;
use App\Models\TahunAjaran;
use App\Models\Kelas;

class NilaiKehadiranController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunAjaran::where('status', 'aktif')->first();
        
        if (!$tahunAktif) {
            return redirect()->route('dashboard')->with('error', 'Belum ada Tahun Ajaran yang aktif. Silakan set Tahun Ajaran terlebih dahulu.');
        }

        $kelas_id = $request->input('kelas_id');
        $search = $request->input('search'); // Tangkap inputan pencarian
        $daftarKelas = Kelas::all();

        // Ambil data nilai hanya untuk tahun ajaran yang sedang aktif
        $nilai_siswas = NilaiKehadiran::with(['siswa', 'kelas'])
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->when($kelas_id, function ($query, $kelas_id) {
                return $query->where('kelas_id', $kelas_id);
            })
            // Tambahkan Filter Pencarian Berdasarkan Relasi Siswa (Nama / NIS)
            ->when($search, function ($query, $search) {
                return $query->whereHas('siswa', function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->paginate(10) 
            // Sertakan 'search' di appends agar tidak hilang saat pindah halaman
            ->appends(['kelas_id' => $kelas_id, 'search' => $search]); 

        return view('nilai_kehadiran.index', compact('nilai_siswas', 'tahunAktif', 'daftarKelas', 'kelas_id', 'search'));
    }

    public function edit(NilaiKehadiran $nilai_kehadiran)
    {
        // Load relasi agar data siswa bisa ditampilkan
        $nilai_kehadiran->load('siswa');
        $kelas = Kelas::all();
        
        return view('nilai_kehadiran.edit', compact('nilai_kehadiran', 'kelas'));
    }

    public function update(Request $request, NilaiKehadiran $nilai_kehadiran)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'total_poin' => 'required|integer|min:0'
        ]);

        $nilai_kehadiran->update([
            'kelas_id' => $request->kelas_id,
            'total_poin' => $request->total_poin
        ]);

        return redirect()->route('nilai_kehadiran.index')->with('success', 'Data nilai dan kelas siswa berhasil diperbarui.');
    }

    // Fungsi Destroy tidak disarankan untuk tabel ini karena riwayat data sangat penting,
    // data akan hilang otomatis jika data Siswa dihapus (karena cascade).
}