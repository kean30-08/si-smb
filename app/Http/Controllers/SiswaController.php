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

        $siswas = Siswa::with('historiAktif.kelas', 'historiAktif.tahunAjaran')
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

        return view('siswa.index', compact('siswas', 'kelas', 'isAdmin', 'kelas_id'));
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
            'nama_panggilan' => 'required|string|max:255',
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
            'nama_panggilan' => 'required|string|max:255',
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
            // VALIDASI BARU UNTUK ALASAN
            'alasan_tidak_aktif' => 'nullable|required_if:status,tidak aktif|string',
        ], [
            'alasan_tidak_aktif.required_if' => 'Alasan wajib diisi karena Anda mengubah status siswa menjadi Tidak Aktif.',
        ]);

        $tahunAktif = TahunAjaran::where('status', 'aktif')->first();

        // ==========================================
        // VALIDASI MENCEGAH LOMPAT KELAS / > 2 SEMESTER
        // ==========================================
        if ($tahunAktif) {
            // 1. VALIDASI: Maksimal 2 Semester di Kelas yang Sama
            $jumlahSemesterDiKelasIni = HistoriSiswa::where('siswa_id', $siswa->id)
                ->where('kelas_id', $request->kelas_id)
                ->where('tahun_ajaran_id', '!=', $tahunAktif->id)
                ->count();

            if ($jumlahSemesterDiKelasIni >= 2) {
                return back()->withInput()->withErrors([
                    'kelas_id' => 'Validasi Gagal! Siswa ini sudah pernah menduduki kelas tersebut selama 2 semester penuh. Anda tidak dapat menurunkannya atau menahannya di kelas yang sama untuk ke-3 kalinya.'
                ]);
            }

            // 2. VALIDASI: Mencegah Loncat Kelas
            $urutanKelas = [
                'Kelas PG' => 1, 'Kelas TK A' => 2, 'Kelas TK B' => 3,
                'Kelas 1 SD' => 4, 'Kelas 2 SD' => 5, 'Kelas 3 SD' => 6, 'Kelas 4 SD' => 7, 'Kelas 5 SD' => 8, 'Kelas 6 SD' => 9,
                'Kelas 1 SMP' => 10, 'Kelas 2 SMP' => 11, 'Kelas 3 SMP' => 12,
                'Kelas 1 SMA' => 13, 'Kelas 2 SMA' => 14, 'Kelas 3 SMA' => 15,
            ];

            $kelasTujuan = Kelas::find($request->kelas_id);
            $urutanTujuan = $urutanKelas[$kelasTujuan->nama_kelas] ?? 0;

            $historiTerakhirMasaLalu = HistoriSiswa::with(['kelas', 'tahunAjaran'])
                ->where('siswa_id', $siswa->id)
                ->where('tahun_ajaran_id', '!=', $tahunAktif->id)
                ->get()
                ->sortByDesc(function($h) {
                    return $h->tahunAjaran->tahun_ajaran ?? ''; 
                })->first();

            if ($historiTerakhirMasaLalu && $historiTerakhirMasaLalu->kelas) {
                $urutanTerakhir = $urutanKelas[$historiTerakhirMasaLalu->kelas->nama_kelas] ?? 0;
                $namaKelasTerakhir = $historiTerakhirMasaLalu->kelas->nama_kelas;
                $namaKelasTujuan = $kelasTujuan->nama_kelas;

                $selisih = $urutanTujuan - $urutanTerakhir;

                if ($selisih > 1) {
                    return back()->withInput()->withErrors([
                        'kelas_id' => "Validasi Gagal! Kelas terakhir siswa ini adalah {$namaKelasTerakhir}. Anda tidak bisa melompatkannya langsung ke {$namaKelasTujuan}."
                    ]);
                }

                if ($selisih < -1) {
                    return back()->withInput()->withErrors([
                        'kelas_id' => "Validasi Gagal! Kelas terakhir siswa ini adalah {$namaKelasTerakhir}. Penurunan ke {$namaKelasTujuan} terlalu jauh dan merusak urutan histori."
                    ]);
                }
            }
        }
        // ==========================================

        DB::transaction(function () use ($request, $siswa, $tahunAktif) {
            // Ambil semua data request kecuali kelas_id
            $dataSiswa = $request->except(['kelas_id']);
            
            // JIKA STATUS BUKAN "TIDAK AKTIF", BERSIHKAN ALASANNYA
            if ($dataSiswa['status'] !== 'tidak aktif') {
                $dataSiswa['alasan_tidak_aktif'] = null;
            }

            $siswa->update($dataSiswa);
            
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
            // 1. Ambil SEMUA agenda pada Tahun Ajaran tersebut (sebagai pondasi utama)
            $agendas = \App\Models\Agenda::where('tahun_ajaran_id', $histori->tahun_ajaran_id)
                ->orderBy('tanggal', 'asc')
                ->get();

            // 2. Ambil data absensi siswa khusus untuk agenda-agenda di atas
            $absensisSiswa = \App\Models\Absensi::where('siswa_id', $siswa->id)
                ->whereIn('agenda_id', $agendas->pluck('id'))
                ->get()
                ->keyBy('agenda_id'); // Jadikan id agenda sebagai kunci pencarian cepat

            $detailGabungan = collect();
            $hadir = 0; $izin = 0; $sakit = 0; $alpa = 0;

            // 3. Gabungkan data Agenda dengan data Absensi siswa
            // Ambil tanggal daftar siswa (format Y-m-d)
            $tglDaftar = \Carbon\Carbon::parse($siswa->created_at)->format('Y-m-d');

            // 3. Gabungkan data Agenda dengan data Absensi siswa
            foreach ($agendas as $agenda) {
                // Cek apakah jadwal ini terjadi sebelum siswa mendaftar
                $isBelumDaftar = $agenda->tanggal < $tglDaftar;

                $absen = $absensisSiswa->get($agenda->id);
                
                // Jika belum daftar, jangan jadikan Alpa. Berikan status khusus
                if ($isBelumDaftar) {
                    $status = '-';
                } else {
                    $status = $absen ? $absen->status_kehadiran : 'alpa'; 
                }

                // Masukkan format objek tiruan agar bisa dibaca oleh view Blade
                $detailGabungan->push((object)[
                    'agenda' => $agenda,
                    'status_kehadiran' => $status,
                    'is_belum_daftar' => $isBelumDaftar // Lempar flag ini ke View
                ]);

                // 4. Hitung poin dan rekapitulasi (ABAIKAN JIKA HARI LIBUR ATAU BELUM DAFTAR)
                if (!$agenda->is_libur && !$isBelumDaftar) {
                    if ($status == 'hadir') $hadir++;
                    elseif ($status == 'izin') $izin++;
                    elseif ($status == 'sakit') $sakit++;
                    elseif ($status == 'alpa') $alpa++;
                }
            }

            // Simpan hasil kalkulasi ke dalam objek histori
            $histori->hadir = $hadir;
            $histori->izin = $izin;
            $histori->sakit = $sakit;
            $histori->alpa = $alpa;
            
            $histori->poin = ($hadir * 5) + ($izin * 1) + ($sakit * 1);

            // 5. Susun rincian per bulan untuk ditampilkan di view (Berdasarkan $detailGabungan)
            $histori->detail_absensi = $detailGabungan->groupBy(function($item) {
                return \Carbon\Carbon::parse($item->agenda->tanggal)->translatedFormat('F Y');
            });
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
                return $item->tahunAjaran->tahun_ajaran ?? ''; 
            })
            ->groupBy(function($item) {
                return $item->kelas ? $item->kelas->nama_kelas : 'Tanpa Kelas';
            })
            ->sortBy(function($items, $key) use ($urutanKelas) {
                return $urutanKelas[$key] ?? 99; 
            });

        // Ambil semua data Tahun Ajaran untuk dropdown edit
        $semuaTahunAjaran = \App\Models\TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

        return view('siswa.histori', compact('siswa', 'historisGrouped', 'semuaTahunAjaran'));
    }

    // FUNGSI BARU: Update Tahun Ajaran di Histori
    // FUNGSI BARU: Update Tahun Ajaran & Kelas di Histori
    public function updateHistori(Request $request, $id)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'kelas_id' => 'required|exists:kelas,id' // TAMBAHAN: Menerima input kelas_id
        ]);

        $histori = \App\Models\HistoriSiswa::findOrFail($id);

        // Cek agar tidak ada duplikasi data di kelas dan TA yang BERSAMAAN
        $exists = \App\Models\HistoriSiswa::where('siswa_id', $histori->siswa_id)
            ->where('kelas_id', $request->kelas_id)
            ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Gagal! Histori untuk Kelas dan Tahun Ajaran tersebut sudah ada.');
        }

        // Simpan pembaruan kelas dan TA
        $histori->update([
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'kelas_id' => $request->kelas_id // TAMBAHAN: Simpan perubahan kelas
        ]);

        return back()->with('success', 'Histori Kelas dan Tahun Ajaran berhasil dikoreksi.');
    }

    // FUNGSI BARU: Hapus Histori
    public function destroyHistori($id)
    {
        $histori = \App\Models\HistoriSiswa::findOrFail($id);
        $histori->delete();

        return back()->with('success', 'Data histori salah berhasil dihapus.');
    }

    public function cetakBarcodeMassal()
    {
        // Hanya mengambil siswa yang berstatus 'aktif' dan urut berdasarkan nama
        $siswas = \App\Models\Siswa::where('status', 'aktif')->orderBy('nama_lengkap', 'asc')->get();
        
        return view('siswa.partials.cetak_barcode_massal', compact('siswas'));
    }

    public function cetakBarcode(Siswa $siswa)
    {
        return view('siswa.partials.cetak_barcode', compact('siswa'));
    }

    public function cetakKartuBaru()
    {
        $tahunAktif = TahunAjaran::where('status', 'aktif')->first();

        if (!$tahunAktif) {
            return back()->with('error', 'Cetak gagal: Tidak ada Tahun Ajaran yang sedang aktif saat ini.');
        }

        // 1. Kumpulkan ID murid yang punya riwayat di Tahun Ajaran LAIN (Berarti mereka Murid Lama)
        $idMuridLama = HistoriSiswa::where('tahun_ajaran_id', '!=', $tahunAktif->id)
            ->pluck('siswa_id')
            ->toArray();

        // 2. Kumpulkan ID murid yang punya riwayat di Tahun Ajaran AKTIF
        $idMuridDiTaAktif = HistoriSiswa::where('tahun_ajaran_id', $tahunAktif->id)
            ->pluck('siswa_id')
            ->toArray();

        // 3. FILTER FINAL: Ambil siswa yang ADA di TA Aktif, tapi BUKAN Murid Lama
        $siswa = \App\Models\Siswa::with('historiAktif.kelas')
            ->where('status', 'aktif')
            ->whereIn('id', $idMuridDiTaAktif)
            ->whereNotIn('id', $idMuridLama) // Coret semua murid lama dari daftar
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        if ($siswa->isEmpty()) {
            return back()->with('error', "Tidak ada murid baru (pendaftar murni) pada Tahun Ajaran {$tahunAktif->tahun_ajaran}.");
        }

        return view('siswa.partials.cetak_massal', compact('siswa'));
    }

    public function ulangTahun()
    {
        // 1. Ambil Semua Siswa Aktif, urutkan berdasarkan bulan dan hari
        $siswasData = \App\Models\Siswa::where('status', 'aktif')
            ->orderByRaw('MONTH(tanggal_lahir) ASC')
            ->orderByRaw('DAY(tanggal_lahir) ASC')
            ->get();

        // 2. Kelompokkan berdasarkan Nama Bulan
        $groupedSiswas = $siswasData->groupBy(function($siswa) {
            return \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('F');
        });

        // 3. LOGIKA REORDER: Pindahkan bulan saat ini (dan seterusnya) ke paling atas
        $bulanSekarangInt = (int) \Carbon\Carbon::now()->format('n'); // 1 s.d 12
        $sortedGroup = collect();

        // Urutkan mulai dari bulan sekarang sampai Desember
        for ($i = $bulanSekarangInt; $i <= 12; $i++) {
            $namaBulan = \Carbon\Carbon::create()->month($i)->translatedFormat('F');
            if ($groupedSiswas->has($namaBulan)) {
                $sortedGroup->put($namaBulan, $groupedSiswas->get($namaBulan));
            }
        }
        
        // Lanjutkan dari Januari sampai bulan sebelum bulan sekarang
        for ($i = 1; $i < $bulanSekarangInt; $i++) {
            $namaBulan = \Carbon\Carbon::create()->month($i)->translatedFormat('F');
            if ($groupedSiswas->has($namaBulan)) {
                $sortedGroup->put($namaBulan, $groupedSiswas->get($namaBulan));
            }
        }
        
        $siswas = $sortedGroup;

        // 4. LOGIKA REMINDER NOTIFIKASI: Cari siswa yang ulang tahun 7 hari ke depan
        $hariIni = \Carbon\Carbon::now()->startOfDay();
        $batasReminder = \Carbon\Carbon::now()->addDays(7)->endOfDay();
        
        $siswaMendekatiUltah = $siswasData->filter(function($siswa) use ($hariIni, $batasReminder) {
            $tglLahir = \Carbon\Carbon::parse($siswa->tanggal_lahir);
            // Jadikan ulang tahunnya di tahun ini
            $ultahTahunIni = $tglLahir->copy()->year($hariIni->year);
            
            // Jika ultah tahun ini sudah lewat, cek ultah tahun depan
            if ($ultahTahunIni->isPast() && !$ultahTahunIni->isToday()) {
                $ultahTahunIni->addYear();
            }
            
            return $ultahTahunIni->between($hariIni, $batasReminder);
        })->sortBy(function($siswa) use ($hariIni) {
            $tglLahir = \Carbon\Carbon::parse($siswa->tanggal_lahir);
            $ultahTahunIni = $tglLahir->copy()->year($hariIni->year);
            if ($ultahTahunIni->isPast() && !$ultahTahunIni->isToday()) $ultahTahunIni->addYear();
            return $ultahTahunIni;
        });

        return view('siswa.ulang_tahun', compact('siswas', 'siswaMendekatiUltah'));
    }
}