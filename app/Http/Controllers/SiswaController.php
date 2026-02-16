<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;

class SiswaController extends Controller
{
public function index()
{
    // Ambil data siswa, urutkan terbaru, dan kasih pagination 10 per halaman
    $siswas = Siswa::with('kelas')->latest()->paginate(10);
    
    return view('siswa.index', compact('siswas'));
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
}
