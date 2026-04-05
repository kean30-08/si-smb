<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Pengajar;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa dengan fitur pencarian, filter status, dan filter kelas.
     * Mendukung pemuatan data secara asinkron (AJAX).
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $kelas_id = $request->input('kelas_id');

        $kelas = Kelas::all();
        
        // Identifikasi hak akses pengguna (Administrator check)
        $isAdmin = !Pengajar::where('user_id', auth()->id())->exists();

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

        // Logika pengiriman partial view untuk request AJAX
        if ($request->ajax()) {
            return view('siswa.partials._table', compact('siswas', 'isAdmin'))->render();
        }

        return view('siswa.index', compact('siswas', 'kelas', 'isAdmin'));
    }

    /**
     * Menampilkan detail profil individu siswa.
     * 
     * @param Siswa $siswa
     * @return \Illuminate\View\View
     */
    public function show(Siswa $siswa)
    {
        return view('siswa.show', compact('siswa'));
    }

    /**
     * Menampilkan formulir pendaftaran siswa baru.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $kelas = Kelas::all();
        return view('siswa.create', compact('kelas'));
    }

    /**
     * Menyimpan data siswa baru ke dalam database.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi data input dengan pesan error kustom
        $request->validate([
            'nama_lengkap' => 'required|unique:siswas,nama_lengkap|regex:/^[a-zA-Z\s]+$/',
            'nis' => 'required|unique:siswas,nis|regex:/^[0-9]+$/',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'required|string',
            'nama_orang_tua' => 'required|string',
            'nomor_hp_orang_tua' => 'required|regex:/^[0-9]+$/',
            'email_orang_tua' => 'required|email',
            'alamat' => 'required|string',
        ], [
            'kelas_id.required' => 'Tidak ada kelas yang dibuat.',
            'nis.unique' => 'Nomor Induk Siswa (NIS) sudah terdaftar dalam sistem.',
            'nis.regex' => 'NIS hanya diperbolehkan berisi angka.',
            'nama_lengkap.regex' => 'Format nama tidak valid (hanya huruf dan spasi).',
            'nama_lengkap.unique' => 'Nama yang dimasukkan sudah terdaftar.',
            'email_orang_tua.email' => 'Format alamat email tidak valid.',
            'nomor_hp_orang_tua.regex' => 'Nomor HP orang tua hanya boleh berisi angka.',
        ]);

        Siswa::create($request->all());

        return redirect()->route('siswa.index')->with('success', 'Data entitas siswa berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir untuk memperbarui data siswa.
     * 
     * @param Siswa $siswa
     * @return \Illuminate\View\View
     */
    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::all();
        return view('siswa.edit', compact('siswa', 'kelas'));
    }

    /**
     * Memperbarui informasi profil siswa di database.
     * 
     * @param Request $request
     * @param Siswa $siswa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama_lengkap' => 'required|unique:siswas,nama_lengkap,' . $siswa->id.'|regex:/^[a-zA-Z\s]+$/',
            'nis' => 'required|regex:/^[0-9]+$/|unique:siswas,nis,' . $siswa->id,
            'jenis_kelamin' => 'required',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'required|string',
            'nama_orang_tua' => 'required|string',
            'nomor_hp_orang_tua' => 'required|regex:/^[0-9]+$/',
            'email_orang_tua' => 'required|email',
            'alamat' => 'required|string',
            'total_poin' => 'nullable|integer|min:0|max:100',
        ], [
            'nis.unique' => 'NIS sudah terdaftar untuk entitas lain.',
            'nis.regex' => 'NIS hanya diperbolehkan berisi angka.',
            'email_orang_tua.email' => 'Pastikan format email sudah benar.',
            'nomor_hp_orang_tua.regex' => 'Nomor HP orang tua hanya boleh berisi angka.',
            'nama_lengkap.regex' => 'Format nama tidak valid (hanya huruf dan spasi).',
            'nama_lengkap.unique' => 'Nama yang dimasukkan sudah terdaftar.',
            'total_poin.max' => 'Total poin tidak boleh melebihi 100.',
            'total_poin.min' => 'Total poin tidak boleh kurang dari 0.',
        ]);

        $siswa->update($request->all());

        return redirect()->route('siswa.index')->with('success', 'Informasi profil siswa berhasil diperbarui.');
    }

    /**
     * Menghapus entitas data siswa dari sistem.
     * 
     * @param Siswa $siswa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('siswa.index')->with('success', 'Entitas data siswa telah dihapus.');
    }

    /**
     * Menghasilkan tampilan Kartu Identitas Siswa (ID Card).
     * 
     * @param Siswa $siswa
     * @return \Illuminate\View\View
     */
    public function cetakKartu(Siswa $siswa)
    {
        // Eager load relasi kelas untuk efisiensi render
        $siswa->load('kelas'); 
        
        return view('siswa.partials.cetak_kartu', compact('siswa'));
    }
}