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
            'nama_kelas' => 'required|unique:kelas,nama_kelas',
        ], [
            'nama_kelas.unique' => 'Kelas sudah dibuat, silakan gunakan nama lain.',
            'nama_kelas.required' => 'Nama kelas wajib diisi.'
        ]);

        Kelas::create($request->all());

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan!');
    }

    // 4. FORM EDIT KELAS
    public function edit(Kelas $kelas) // Perhatikan: Laravel kadang otomatis pakai $kela untuk singular dari $kelas
    {
        return view('kelas.edit', compact('kelas'));
    }

    // 5. UPDATE DATA
    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas,'.$kelas->id,
        ],[
            'nama_kelas.unique' => 'Kelas sudah dibuat, silakan gunakan nama lain.',
            'nama_kelas.required' => 'Nama kelas wajib diisi.'
        ]);

        $kelas->update($request->all());

        return redirect()->route('kelas.index')->with('success', 'Nama kelas berhasil diperbarui!');
    }

    // 6. HAPUS DATA
    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus!');
    }
}