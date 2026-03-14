<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;

class SiswaController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');
    $status = $request->input('status');
    $kelas_id = $request->input('kelas_id');

    $kelas = Kelas::all();
    
    // Pindahkan pengecekan isAdmin ke sini agar bisa dikirim ke partial view
    $isAdmin = !\App\Models\Pengajar::where('user_id', auth()->id())->exists();

    $siswas = Siswa::with('kelas')
        ->when($search, function ($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                ->orWhere('nis', 'like', "%{$search}%");
            });
        })
        ->when($status, function ($query, $status) {
            return $query->where('status', $status);
        })
        ->when($kelas_id, function ($query, $kelas_id) {
            return $query->where('kelas_id', $kelas_id);
        })
        ->latest()
        ->paginate(10)
        ->appends([
            'search' => $search, 
            'status' => $status, 
            'kelas_id' => $kelas_id
        ]); 

    // LOGIKA AJAX
    if ($request->ajax()) {
        // Jika request dari JavaScript, render tabelnya saja
        return view('siswa.partials._table', compact('siswas', 'isAdmin'))->render();
    }

    // Jika load halaman biasa, tampilkan halaman penuh
    return view('siswa.index', compact('siswas', 'kelas', 'isAdmin'));
}

    // Tambahkan fungsi baru ini untuk menampilkan detail individu
    public function show(Siswa $siswa)
    {
        return view('siswa.show', compact('siswa'));
    }
    public function create()
    {
        // Kita butuh data kelas untuk dropdown "Pilih Kelas"
        $kelas = Kelas::all();
        return view('siswa.create', compact('kelas'));
    }
    public function store(Request $request)
    {
        // 1. Validasi Data (Biar gak asal isi)
        $request->validate([
            'nama_lengkap' => 'required',
            'nis' => 'required|unique:siswas,nis', // NIS gak boleh kembar
            'jenis_kelamin' => 'required',
        ]);

        // 2. Simpan ke Database
        Siswa::create($request->all());

        // 3. Kembali ke Halaman Index dengan Pesan Sukses
        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil ditambahkan!');
    }
    public function edit(Siswa $siswa)
    {
        // Kita butuh data kelas lagi untuk dropdown
        $kelas = Kelas::all();
        return view('siswa.edit', compact('siswa', 'kelas'));
    }

    // 2. FUNGSI UPDATE DATA KE DATABASE
    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            // Validasi unik NIS, tapi KECUALIKAN siswa yang sedang diedit ini
            'nis' => 'required|unique:siswas,nis,'.$siswa->id, 
            'jenis_kelamin' => 'required',
        ]);

        // Update semua data sesuai input
        $siswa->update($request->all());

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    // 3. FUNGSI HAPUS DATA
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil dihapus!');
    }
// Fungsi untuk mencetak Kartu Pelajar (ID Card)
    public function cetakKartu(Siswa $siswa)
    {
        // Memastikan relasi kelas terbawa
        $siswa->load('kelas'); 
        
        return view('siswa.partials.cetak_kartu', compact('siswa'));
    }
}
