<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    // 1. TAMPILKAN DAFTAR KELAS
    public function index()
    {
        $kelas = Kelas::all(); // Ambil semua data
        return view('kelas.index', compact('kelas'));
    }

    // 2. FORM TAMBAH KELAS
    public function create()
    {
        return view('kelas.create');
    }

    // 3. SIMPAN DATA BARU
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas', // Nama kelas gak boleh kembar
        ]);

        Kelas::create($request->all());

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan!');
    }

    // 4. FORM EDIT KELAS
    public function edit(Kelas $kela) // Perhatikan: Laravel kadang otomatis pakai $kela untuk singular dari $kelas
    {
        return view('kelas.edit', compact('kela'));
    }

    // 5. UPDATE DATA
    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas,'.$kela->id,
        ]);

        $kela->update($request->all());

        return redirect()->route('kelas.index')->with('success', 'Nama kelas berhasil diperbarui!');
    }

    // 6. HAPUS DATA
    public function destroy(Kelas $kela)
    {
        $kela->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus!');
    }
}