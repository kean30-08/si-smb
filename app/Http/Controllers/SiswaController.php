<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\HistoriSiswa; // Ganti NilaiKehadiran dengan HistoriSiswa

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $kelas_id = $request->input('kelas_id');

        $kelas = Kelas::all();
        $isAdmin = auth()->user()->isAdmin();

        
        $siswas = Siswa::with('historiAktif.kelas')
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
                return $query->whereHas('historiAktif', function($q) use ($kelas_id) {
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

    public function show(Siswa $siswa)
    {
        // Ganti relasi historiAktif menjadi historiAktif
        $siswa->load('historiAktif.kelas', 'historiAktif.tahunAjaran');

        $poin = 0;
        $historiAktif = $siswa->historiAktif;

        if ($historiAktif) {
            $absensi = \App\Models\Absensi::where('siswa_id', $siswa->id)
                ->whereHas('agenda', function($q) use ($historiAktif) {
                    $q->where('tahun_ajaran_id', $historiAktif->tahun_ajaran_id);
                })->get();

            $hadir = $absensi->where('status_kehadiran', 'hadir')->count();
            $izin = $absensi->where('status_kehadiran', 'izin')->count();
            $sakit = $absensi->where('status_kehadiran', 'sakit')->count();

            $poin = ($hadir * 5) + ($izin * 1) + ($sakit * 1);
        }

        return view('siswa.show', compact('siswa', 'poin'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        return view('siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|unique:siswas,nama_lengkap|regex:/^[a-zA-Z\s]+$/',
            'nis' => 'required|unique:siswas,nis|regex:/^[0-9]+$/',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'required|string',
            'nama_orang_tua' => 'required|string',
            'nomor_hp_orang_tua' => 'nullable|regex:/^[0-9]+$/',
            'email_orang_tua' => 'nullable|email',
            'alamat' => 'required|string',
            'asal_sekolah' => 'nullable|string|max:255',
            'nomor_hp_siswa' => 'nullable|regex:/^[0-9]+$/',
        ], [
            'kelas_id.required' => 'Tidak ada kelas yang dibuat.',
            'nis.unique' => 'Nomor Induk Siswa (NIS) sudah terdaftar dalam sistem.',
            'nis.regex' => 'NIS hanya diperbolehkan berisi angka.',
            'nama_lengkap.regex' => 'Format nama tidak valid (hanya huruf dan spasi).',
            'nama_lengkap.unique' => 'Nama yang dimasukkan sudah terdaftar.',
            'email_orang_tua.email' => 'Format alamat email tidak valid.',
            'nomor_hp_orang_tua.regex' => 'Nomor HP orang tua hanya boleh berisi angka.',
            'nomor_hp_siswa.regex' => 'Nomor HP Siswa hanya boleh berisi angka.',
        ]);

        DB::transaction(function () use ($request) {
            $siswa = Siswa::create($request->except(['kelas_id']));
            $tahunAktif = TahunAjaran::where('status', 'aktif')->first();

            if ($tahunAktif) {
                // Gunakan HistoriSiswa, tidak ada lagi total_poin
                HistoriSiswa::create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $request->kelas_id,
                    'tahun_ajaran_id' => $tahunAktif->id,
                ]);
            }
        });

        return redirect()->route('siswa.index')->with('success', 'Data entitas siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::all();
        // Ganti relasi historiAktif menjadi historiAktif
        $kelasSekarang = $siswa->historiAktif->kelas_id ?? null;
        
        return view('siswa.edit', compact('siswa', 'kelas', 'kelasSekarang'));
    }

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
            'nomor_hp_orang_tua' => 'nullable|regex:/^[0-9]+$/',
            'email_orang_tua' => 'nullable|email',
            'alamat' => 'required|string',
            'status' => 'required|in:aktif,tidak aktif,lulus',
            'asal_sekolah' => 'nullable|string|max:255',
            'nomor_hp_siswa' => 'nullable|regex:/^[0-9]+$/',
        ]);

        DB::transaction(function () use ($request, $siswa) {
            $siswa->update($request->except(['kelas_id']));
            $tahunAktif = TahunAjaran::where('status', 'aktif')->first();
            
            if ($tahunAktif) {
                HistoriSiswa::updateOrCreate(
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

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('siswa.index')->with('success', 'Entitas data siswa telah dihapus.');
    }

    public function cetakKartu(Siswa $siswa)
    {
        $siswa->load('historiAktif.kelas'); 
        return view('siswa.partials.cetak_kartu', compact('siswa'));
    }

    public function cetakMassal()
    {
        $siswa = \App\Models\Siswa::with('historiAktif.kelas')->get();
        return view('siswa.partials.cetak_massal', compact('siswa'));
    }

    public function histori(Siswa $siswa)
    {
        $historiMentah = \App\Models\HistoriSiswa::with(['kelas', 'tahunAjaran'])
                            ->where('siswa_id', $siswa->id)
                            ->get();

        $historiMentah->each(function ($histori) use ($siswa) {
            $absensis = \App\Models\Absensi::where('siswa_id', $siswa->id)
                ->whereHas('agenda', function($q) use ($histori) {
                    $q->where('tahun_ajaran_id', $histori->tahun_ajaran_id);
                })->get();

            $histori->hadir = $absensis->where('status_kehadiran', 'hadir')->count();
            $histori->izin = $absensis->where('status_kehadiran', 'izin')->count();
            $histori->sakit = $absensis->where('status_kehadiran', 'sakit')->count();
            $histori->alpa = $absensis->where('status_kehadiran', 'alpa')->count();
            
            $histori->poin = ($histori->hadir * 5) + ($histori->izin * 1) + ($histori->sakit * 1);
        });

        // PEMBOBOTAN URUTAN KELAS AGAR ACCORDION RAPI (PG -> TK -> SD -> SMP -> SMA)
        $urutanKelas = [
            'Kelas PG' => 1, 'Kelas TK A' => 2, 'Kelas TK B' => 3,
            'Kelas 1 SD' => 4, 'Kelas 2 SD' => 5, 'Kelas 3 SD' => 6, 'Kelas 4 SD' => 7, 'Kelas 5 SD' => 8, 'Kelas 6 SD' => 9,
            'Kelas 1 SMP' => 10, 'Kelas 2 SMP' => 11, 'Kelas 3 SMP' => 12,
            'Kelas 1 SMA' => 13, 'Kelas 2 SMA' => 14, 'Kelas 3 SMA' => 15,
        ];

        $historisGrouped = $historiMentah
            ->sortByDesc(function($item) {
                // 1. Urutkan isi di dalam tabel dari TA paling baru ke paling lama
                return $item->tahunAjaran->tahun_ajaran ?? ''; 
            })
            ->groupBy(function($item) {
                return $item->kelas ? $item->kelas->nama_kelas : 'Tanpa Kelas';
            })
            ->sortBy(function($items, $key) use ($urutanKelas) {
                // 2. Urutkan Accordion dari atas ke bawah sesuai hierarki kelas
                return $urutanKelas[$key] ?? 99; 
            });

        return view('siswa.histori', compact('siswa', 'historisGrouped'));
    }
}