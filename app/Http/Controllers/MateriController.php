<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MateriController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kelas_id = $request->input('kelas_id');

        $kelas = Kelas::all();

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

        return view('materi.index', compact('materis', 'kelas'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        return view('materi.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'deskripsi' => 'nullable|string',
            // Validasi file: maksimal 5MB, format bebas untuk dokumen
            'file_materi' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:5120', 
        ]);

        $data = $request->all();

        // Jika ada file yang diupload
        if ($request->hasFile('file_materi')) {
            $file = $request->file('file_materi');
            
            // Mengambil ekstensi asli (contoh: pdf, docx)
            $extension = $file->getClientOriginalExtension();
            
            // Membuat nama file: slug-dari-judul_timestamp.ekstensi
            // Contoh hasil: pengenalan-sejarah-buddha_1708345678.pdf
            $fileName = Str::slug($request->judul) . '_' . time() . '.' . $extension;
            
            // Simpan file dengan nama khusus menggunakan storeAs()
            $filePath = $file->storeAs('materi_files', $fileName, 'public');
            $data['file_materi'] = $filePath;
        }

        Materi::create($data);

        return redirect()->route('materi.index')->with('success', 'Materi pembelajaran berhasil diunggah!');
    }

    public function edit(Materi $materi)
    {
        $kelas = Kelas::all();
        return view('materi.edit', compact('materi', 'kelas'));
    }

    public function update(Request $request, Materi $materi)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kelas_id' => 'required|exists:kelas,id',
            'deskripsi' => 'nullable|string',
            'file_materi' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar|max:5120',
        ]);

        $data = $request->except(['file_materi']); // Ambil semua data kecuali file dulu

        // Jika User mengupload file baru
        if ($request->hasFile('file_materi')) {
            // Hapus file lama jika ada
            if ($materi->file_materi && Storage::disk('public')->exists($materi->file_materi)) {
                Storage::disk('public')->delete($materi->file_materi);
            }
            
            $file = $request->file('file_materi');
            $extension = $file->getClientOriginalExtension();
            
            // Membuat nama file baru berdasarkan judul baru
            $fileName = Str::slug($request->judul) . '_' . time() . '.' . $extension;
            
            // Simpan file
            $filePath = $file->storeAs('materi_files', $fileName, 'public');
            $data['file_materi'] = $filePath;
        }

        $materi->update($data);

        return redirect()->route('materi.index')->with('success', 'Materi berhasil diperbarui!');
    }

    public function destroy(Materi $materi)
    {
        // Hapus file fisiknya dari folder storage sebelum menghapus data di database
        if ($materi->file_materi && Storage::disk('public')->exists($materi->file_materi)) {
            Storage::disk('public')->delete($materi->file_materi);
        }

        $materi->delete();

        return redirect()->route('materi.index')->with('success', 'Materi dan file lampirannya berhasil dihapus!');
    }
}