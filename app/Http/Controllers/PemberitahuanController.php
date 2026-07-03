<?php

namespace App\Http\Controllers;

use App\Models\Pemberitahuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $fileName = Str::slug($request->judul) . '_' . time() . '.' . $file->getClientOriginalExtension();
            // Simpan ke public/uploads/pemberitahuan
            $file->move(public_path('uploads/pemberitahuan'), $fileName);
            $data['gambar'] = 'uploads/pemberitahuan/' . $fileName;
        }

        Pemberitahuan::create($data);

        return redirect()->route('pemberitahuan.index')->with('success', 'Pemberitahuan berhasil dibuat!');
    }

    public function show(Pemberitahuan $pemberitahuan)
    {
        return view('pemberitahuan.show', compact('pemberitahuan'));
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

        $data = $request->except(['gambar']);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if ($pemberitahuan->gambar && File::exists(public_path($pemberitahuan->gambar))) {
                File::delete(public_path($pemberitahuan->gambar));
            }

            $file = $request->file('gambar');
            $fileName = Str::slug($request->judul) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/pemberitahuan'), $fileName);
            $data['gambar'] = 'uploads/pemberitahuan/' . $fileName;
        }

        $pemberitahuan->update($data);

        return redirect()->route('pemberitahuan.index')->with('success', 'Pemberitahuan berhasil diperbarui!');
    }

    public function destroy(Pemberitahuan $pemberitahuan)
    {
        if ($pemberitahuan->gambar && File::exists(public_path($pemberitahuan->gambar))) {
            File::delete(public_path($pemberitahuan->gambar));
        }

        $pemberitahuan->delete();
        return redirect()->route('pemberitahuan.index')->with('success', 'Pemberitahuan berhasil dihapus!');
    }
}