<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\NilaiKehadiran;

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
        $isAdmin = auth()->user()->isAdmin();

        // Ambil data siswa beserta relasi nilai aktif (untuk ditarik nama kelasnya di view)
        $siswas = Siswa::with('nilaiKehadiranAktif.kelas')
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            // PERUBAHAN: Filter kelas sekarang harus mengecek ke tabel nilai_kehadirans yang aktif
            ->when($kelas_id, function ($query, $kelas_id) {
                return $query->whereHas('nilaiKehadiranAktif', function($q) use ($kelas_id) {
                    $q->where('kelas_id', $kelas_id);
                });
            })
            ->latest()
            ->paginate(10)
            ->appends([
                'search' => $search, 
                'status' => $status, 
                'kelas_id' => $kelas_id
            ]); 

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

        DB::transaction(function () use ($request) {
            // 1. Simpan Data Diri Siswa (Kecuali kelas_id)
            $siswa = Siswa::create($request->except(['kelas_id']));

            // 2. Ambil Tahun Ajaran Aktif
            $tahunAktif = TahunAjaran::where('status', 'aktif')->first();

            // 3. Daftarkan siswa ke kelas tersebut di Tahun Ajaran Aktif
            if ($tahunAktif) {
                NilaiKehadiran::create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $request->kelas_id,
                    'tahun_ajaran_id' => $tahunAktif->id,
                    'total_poin' => 0
                ]);
            }
        });

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
        // Ambil kelas aktif saat ini untuk ditampilkan di form edit
        $kelasSekarang = $siswa->nilaiKehadiranAktif->kelas_id ?? null;
        
        return view('siswa.edit', compact('siswa', 'kelas', 'kelasSekarang'));
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
            'nama_lengkap' => 'required|regex:/^[a-zA-Z\s]+$/|unique:siswas,nama_lengkap,' . $siswa->id,
            'nis' => 'required|regex:/^[0-9]+$/|unique:siswas,nis,' . $siswa->id,
            'jenis_kelamin' => 'required',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'required|string',
            'nama_orang_tua' => 'required|string',
            'nomor_hp_orang_tua' => 'required|regex:/^[0-9]+$/',
            'email_orang_tua' => 'required|email',
            'alamat' => 'required|string',
            'status' => 'required|in:aktif,tidak aktif,lulus',
        ]);

        DB::transaction(function () use ($request, $siswa) {
            // 1. Update Profil Siswa
            $siswa->update($request->except(['kelas_id']));

            // 2. Update atau Buat data pendaftaran kelas di Tahun Ajaran Aktif
            $tahunAktif = TahunAjaran::where('status', 'aktif')->first();
            
            if ($tahunAktif) {
                NilaiKehadiran::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran_id' => $tahunAktif->id
                    ],
                    [
                        'kelas_id' => $request->kelas_id
                    ]
                );
            }
        });

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
        // Eager load relasi terbaru
        $siswa->load('nilaiKehadiranAktif.kelas'); 
        return view('siswa.partials.cetak_kartu', compact('siswa'));
    }
}