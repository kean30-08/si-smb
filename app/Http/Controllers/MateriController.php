<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Kelas;
use App\Models\Pengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MateriController extends Controller
{
    /**
     * Menampilkan daftar materi dengan fitur pencarian dan filter kelas.
     * Mendukung pemuatan tabel secara asinkron (AJAX).
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kelas_id = $request->input('kelas_id');

        $kelas = Kelas::all();

        // Cek otoritas pengguna (Administrator check)
        $isAdmin = !Pengajar::where('user_id', auth()->id())->exists();

        $materis = Materi::with('kelas')
            ->when($search, function ($query, $search) {
                return $query->where('judul', 'like', "%{$search}%");
            })
            ->when($kelas_id, function ($query, $kelas_id) {
                return $query->where('kelas_id', $kelas_id);
            })
            ->latest()
            ->paginate(10)
            ->appends(['search' => $search, 'kelas_id' => $kelas_id]);
        
        if ($request->ajax()) {
            return view('materi.partials._table', compact('materis', 'isAdmin'))->render();
        }

        return view('materi.index', compact('materis', 'kelas', 'isAdmin'));
    }

    /**
     * Menampilkan formulir tambah materi baru.
     */
    public function create()
    {
        $kelas = Kelas::all();
        return view('materi.create', compact('kelas'));
    }

    /**
     * Menyimpan data materi baru ke database beserta file fisik.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255|unique:materis,judul',
            'kelas_id' => 'required|exists:kelas,id',
            'deskripsi' => 'nullable|string',
            'file_materi' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:5120', 
        ], [
            'file_materi.mimes' => 'Format file harus berupa dokumen (pdf, doc, docx, ppt, pptx, xls, xlsx, zip, rar).',
            'file_materi.max' => 'Ukuran file tidak boleh melebihi 5MB.',
            'judul.unique' => 'Judul materi sudah terdaftar dalam sistem.',
        ]);

        $data = $request->all();

        // Logika pengunggahan file materi
        if ($request->hasFile('file_materi')) {
            $file = $request->file('file_materi');
            $extension = $file->getClientOriginalExtension();
            
            // Penamaan file yang SEO-friendly dan unik
            $fileName = Str::slug($request->judul) . '_' . time() . '.' . $extension;
            
            $filePath = $file->storeAs('materi_files', $fileName, 'public');
            $data['file_materi'] = $filePath;
        }

        Materi::create($data);

        return redirect()->route('materi.index')->with('success', 'Materi pembelajaran berhasil diunggah!');
    }

    /**
     * Menampilkan formulir edit materi.
     */
    public function edit(Materi $materi)
    {
        $kelas = Kelas::all();
        return view('materi.edit', compact('materi', 'kelas'));
    }

    /**
     * Memperbarui data materi dan mengelola penggantian file fisik.
     */
    public function update(Request $request, Materi $materi)
    {
        $request->validate([
            'judul' => 'required|string|max:255|unique:materis,judul,' . $materi->id,
            'kelas_id' => 'required|exists:kelas,id',
            'deskripsi' => 'nullable|string',
            'file_materi' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:5120',
        ], [
            'file_materi.mimes' => 'Format file harus berupa dokumen (pdf, doc, docx, ppt, pptx, xls, xlsx, zip, rar).',
            'file_materi.max' => 'Ukuran file tidak boleh melebihi 5MB.',
            'judul.unique' => 'Judul materi sudah terdaftar dalam sistem.',
        ]);

        $data = $request->except(['file_materi']); 

        if ($request->hasFile('file_materi')) {
            // Hapus file fisik lama jika ada untuk efisiensi penyimpanan
            if ($materi->file_materi && Storage::disk('public')->exists($materi->file_materi)) {
                Storage::disk('public')->delete($materi->file_materi);
            }
            
            $file = $request->file('file_materi');
            $extension = $file->getClientOriginalExtension();
            
            $fileName = Str::slug($request->judul) . '_' . time() . '.' . $extension;
            
            $filePath = $file->storeAs('materi_files', $fileName, 'public');
            $data['file_materi'] = $filePath;
        }

        $materi->update($data);

        return redirect()->route('materi.index')->with('success', 'Materi berhasil diperbarui!');
    }

    /**
     * Menampilkan detail informasi materi.
     */
    public function show(Materi $materi)
    {
        $materi->load('kelas'); 
        return view('materi.show', compact('materi'));
    }

    /**
     * Menghapus entitas materi dan file terkait dari server.
     */
    public function destroy(Materi $materi)
    {
        // Pastikan file fisik terhapus sebelum record database hilang
        if ($materi->file_materi && Storage::disk('public')->exists($materi->file_materi)) {
            Storage::disk('public')->delete($materi->file_materi);
        }

        $materi->delete();

        return redirect()->route('materi.index')->with('success', 'Materi dan file lampiran berhasil dihapus!');
    }
}