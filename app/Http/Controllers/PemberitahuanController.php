<?php

namespace App\Http\Controllers;

use App\Models\Pemberitahuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PemberitahuanController extends Controller
{
    public function index()
    {
        $pemberitahuans = Pemberitahuan::orderBy('created_at', 'desc')->paginate(10);
        $isAdmin = auth()->check() ? auth()->user()->isAdmin() : false;
        
        return view('pemberitahuan.index', compact('pemberitahuans', 'isAdmin'));
    }

    public function create()
    {
        return view('pemberitahuan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'status' => 'required|in:aktif,arsip',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Validasi gambar max 2MB
        ]);

        $data = $request->all();

        // Logika Upload Gambar
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $nama_gambar = time() . '_' . $gambar->getClientOriginalName();
            $path = $gambar->storeAs('public/pemberitahuan_images', $nama_gambar);
            $data['gambar'] = 'pemberitahuan_images/' . $nama_gambar;
        }

        Pemberitahuan::create($data);

        return redirect()->route('pemberitahuan.index')
                         ->with('success', 'Pemberitahuan beserta gambar berhasil ditambahkan.');
    }

    public function edit(Pemberitahuan $pemberitahuan)
    {
        return view('pemberitahuan.edit', compact('pemberitahuan'));
    }

    public function update(Request $request, Pemberitahuan $pemberitahuan)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'status' => 'required|in:aktif,arsip',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        // Logika Update Gambar (Hapus yang lama, simpan yang baru)
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($pemberitahuan->gambar && Storage::exists('public/' . $pemberitahuan->gambar)) {
                Storage::delete('public/' . $pemberitahuan->gambar);
            }

            $gambar = $request->file('gambar');
            $nama_gambar = time() . '_' . $gambar->getClientOriginalName();
            $path = $gambar->storeAs('public/pemberitahuan_images', $nama_gambar);
            $data['gambar'] = 'pemberitahuan_images/' . $nama_gambar;
        }

        $pemberitahuan->update($data);

        return redirect()->route('pemberitahuan.index')
                         ->with('success', 'Pemberitahuan berhasil diperbarui.');
    }

    public function destroy(Pemberitahuan $pemberitahuan)
    {
        // Hapus gambar fisik dari server sebelum menghapus data dari database
        if ($pemberitahuan->gambar && Storage::exists('public/' . $pemberitahuan->gambar)) {
            Storage::delete('public/' . $pemberitahuan->gambar);
        }

        $pemberitahuan->delete();
        return redirect()->route('pemberitahuan.index')
                         ->with('success', 'Pemberitahuan berhasil dihapus.');
    }

    public function show(Pemberitahuan $pemberitahuan)
    {
        return view('pemberitahuan.show', compact('pemberitahuan'));
    }
}