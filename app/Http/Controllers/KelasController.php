<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Menampilkan daftar seluruh entitas kelas.
     */
    public function index()
    {
        $kelas = Kelas::all(); 
               
        return view('kelas.index', compact('kelas'));
    }

    /**
     * Menampilkan formulir pembuatan kelas baru.
     */
    public function create()
    {
        return view('kelas.create');
    }

    /**
     * Menyimpan data kelas baru ke dalam database.
     * Dilengkapi dengan validasi keunikan nama kelas.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas',
        ], [
            'nama_kelas.unique' => 'Identitas kelas tersebut sudah terdaftar dalam sistem.',
            'nama_kelas.required' => 'Atribut nama kelas wajib dilengkapi.'
        ]);

        // Proses mass assignment data kelas
        Kelas::create($request->all());

        return redirect()->route('kelas.index')->with('success', 'Entitas kelas baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan formulir untuk memperbarui data kelas.
     */
    public function edit(Kelas $kelas) 
    {
        return view('kelas.edit', compact('kelas'));
    }

    /**
     * Memperbarui informasi entitas kelas di database.
     */
    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas,' . $kelas->id,
        ], [
            'nama_kelas.unique' => 'Nama kelas tersebut sudah digunakan oleh entitas lain.',
            'nama_kelas.required' => 'Atribut nama kelas tidak boleh dikosongkan.'
        ]);

        $kelas->update($request->all());

        return redirect()->route('kelas.index')->with('success', 'Informasi entitas kelas berhasil diperbarui!');
    }

    /**
     * Menghapus entitas kelas dari sistem.
     */
    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        
        return redirect()->route('kelas.index')->with('success', 'Entitas kelas berhasil dihapus dari sistem!');
    }
}