<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $tahunAktif = \App\Models\TahunAjaran::where('status', 'aktif')->first();
        
        // PERBAIKAN: Gunakan nama relasi 'historiSiswas' sesuai dengan yang ada di Model Kelas
        $kelas = \App\Models\Kelas::withCount(['historiSiswas as jumlah_siswa' => function($query) use ($tahunAktif) {
            if ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id)
                      ->whereHas('siswa', function($q) {
                          $q->where('status', 'aktif'); // Hanya hitung siswa yang statusnya aktif
                      });
            } else {
                $query->where('id', 0); // Jika tidak ada TA aktif, jangan hitung apapun
            }
        }])->get(); 
                
        return view('kelas.index', compact('kelas'));
    }

    public function create()
    {
        return view('kelas.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi Jenjang Baru (Memasukkan TK A dan TK B)
        $request->validate([
            'jenjang' => 'required|in:SD,SMP,SMA,TK A,TK B,PG'
        ]);

        // 2. Logika Pembuatan Nama Kelas & Limit Angka
        if (in_array($request->jenjang, ['TK A', 'TK B', 'PG'])) {
            $nama_kelas = 'Kelas ' . $request->jenjang;
        } else {
            $maxTingkat = $request->jenjang == 'SD' ? 6 : 3;
            
            $request->validate([
                'tingkat' => 'required|integer|min:1|max:' . $maxTingkat
            ], [
                'tingkat.required' => 'Nomor kelas wajib diisi untuk jenjang ' . $request->jenjang,
                'tingkat.min' => 'Nomor kelas minimal 1.',
                'tingkat.max' => 'Nomor kelas maksimal ' . $maxTingkat . ' untuk jenjang ' . $request->jenjang . '.',
            ]);
            
            $nama_kelas = 'Kelas ' . $request->tingkat . ' ' . $request->jenjang;
        }

        // 3. Validasi Keunikan Data di Database
        $request->merge(['nama_kelas' => $nama_kelas]);
        $request->validate([
            'nama_kelas' => 'unique:kelas,nama_kelas',
        ], [
            'nama_kelas.unique' => 'Kelas '. $nama_kelas .' sudah terdaftar dalam sistem.',
        ]);

        Kelas::create(['nama_kelas' => $nama_kelas]);

        return redirect()->route('kelas.index')->with('success', 'Kelas baru berhasil ditambahkan!');
    }

    public function edit(Kelas $kelas) 
    {
        return view('kelas.edit', compact('kelas'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'jenjang' => 'required|in:SD,SMP,SMA,TK A,TK B,PG'
        ]);

        if (in_array($request->jenjang, ['TK A', 'TK B', 'PG'])) {
            $nama_kelas = 'Kelas ' . $request->jenjang;
        } else {
            $maxTingkat = $request->jenjang == 'SD' ? 6 : 3;
            
            $request->validate([
                'tingkat' => 'required|integer|min:1|max:' . $maxTingkat
            ], [
                'tingkat.required' => 'Nomor kelas wajib diisi untuk jenjang ' . $request->jenjang,
                'tingkat.min' => 'Nomor kelas minimal 1.',
                'tingkat.max' => 'Nomor kelas maksimal ' . $maxTingkat . ' untuk jenjang ' . $request->jenjang . '.',
            ]);
            
            $nama_kelas = 'Kelas ' . $request->tingkat . ' ' . $request->jenjang;
        }

        $request->merge(['nama_kelas' => $nama_kelas]);
        $request->validate([
            'nama_kelas' => 'unique:kelas,nama_kelas,' . $kelas->id,
        ], [
            'nama_kelas.unique' => 'Kelas '. $nama_kelas .' sudah ada dalam basis data.',
        ]);

        $kelas->update(['nama_kelas' => $nama_kelas]);

        return redirect()->route('kelas.index')->with('success', 'Informasi kelas berhasil diperbarui!');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Entitas kelas berhasil dihapus dari sistem!');
    }
}