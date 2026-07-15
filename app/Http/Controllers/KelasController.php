<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $tahunAktif = \App\Models\TahunAjaran::where('status', 'aktif')->first();
        
        // PERBAIKAN: Gunakan nama relasi 'historiSiswas' sesuai dengan yang ada di Model Kelas
        $kelas = \App\Models\Kelas::withCount(['historiSiswas as jumlah_siswa' => function($query) use ($tahunAktif) {
            if ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id)
                      ->whereHas('siswa', function($q) {
                          $q->where('status', 'aktif'); // Hanya hitung siswa yang statusnya aktif
                      });
            } else {
                $query->where('id', 0); // Jika tidak ada TA aktif, jangan hitung apapun
            }
        }])->get(); 
                
        return view('kelas.index', compact('kelas'));
    }

    public function create()
    {
        return view('kelas.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi Jenjang Baru (Memasukkan TK A dan TK B)
        $request->validate([
            'jenjang' => 'required|in:SD,SMP,SMA,TK A,TK B,PG'
        ]);

        // 2. Logika Pembuatan Nama Kelas & Limit Angka
        if (in_array($request->jenjang, ['TK A', 'TK B', 'PG'])) {
            $nama_kelas = 'Kelas ' . $request->jenjang;
        } else {
            $maxTingkat = $request->jenjang == 'SD' ? 6 : 3;
            
            $request->validate([
                'tingkat' => 'required|integer|min:1|max:' . $maxTingkat
            ], [
                'tingkat.required' => 'Nomor kelas wajib diisi untuk jenjang ' . $request->jenjang,
                'tingkat.min' => 'Nomor kelas minimal 1.',
                'tingkat.max' => 'Nomor kelas maksimal ' . $maxTingkat . ' untuk jenjang ' . $request->jenjang . '.',
            ]);
            
            $nama_kelas = 'Kelas ' . $request->tingkat . ' ' . $request->jenjang;
        }

        // 3. Validasi Keunikan Data di Database
        $request->merge(['nama_kelas' => $nama_kelas]);
        $request->validate([
            'nama_kelas' => 'unique:kelas,nama_kelas',
        ], [
            'nama_kelas.unique' => 'Kelas '. $nama_kelas .' sudah terdaftar dalam sistem.',
        ]);

        Kelas::create(['nama_kelas' => $nama_kelas]);

        return redirect()->route('kelas.index')->with('success', 'Kelas baru berhasil ditambahkan!');
    }

    public function edit(Kelas $kelas) 
    {
        return view('kelas.edit', compact('kelas'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'jenjang' => 'required|in:SD,SMP,SMA,TK A,TK B,PG'
        ]);

        if (in_array($request->jenjang, ['TK A', 'TK B', 'PG'])) {
            $nama_kelas = 'Kelas ' . $request->jenjang;
        } else {
            $maxTingkat = $request->jenjang == 'SD' ? 6 : 3;
            
            $request->validate([
                'tingkat' => 'required|integer|min:1|max:' . $maxTingkat
            ], [
                'tingkat.required' => 'Nomor kelas wajib diisi untuk jenjang ' . $request->jenjang,
                'tingkat.min' => 'Nomor kelas minimal 1.',
                'tingkat.max' => 'Nomor kelas maksimal ' . $maxTingkat . ' untuk jenjang ' . $request->jenjang . '.',
            ]);
            
            $nama_kelas = 'Kelas ' . $request->tingkat . ' ' . $request->jenjang;
        }

        $request->merge(['nama_kelas' => $nama_kelas]);
        $request->validate([
            'nama_kelas' => 'unique:kelas,nama_kelas,' . $kelas->id,
        ], [
            'nama_kelas.unique' => 'Kelas '. $nama_kelas .' sudah ada dalam basis data.',
        ]);

        $kelas->update(['nama_kelas' => $nama_kelas]);

        return redirect()->route('kelas.index')->with('success', 'Informasi kelas berhasil diperbarui!');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Entitas kelas berhasil dihapus dari sistem!');
    }

    public function histori(Request $request)
    {
        $tahunAjarans = \App\Models\TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $taAktif = \App\Models\TahunAjaran::where('status', 'aktif')->first();
        
        // Menentukan TA yang dipilih
        $selectedTaId = $request->input('tahun_ajaran_id', $taAktif ? $taAktif->id : ($tahunAjarans->first()->id ?? null));
        $selectedTa = $tahunAjarans->where('id', $selectedTaId)->first();

        // Mengambil seluruh data kelas beserta statistiknya
        $kelasList = \App\Models\Kelas::all()->map(function ($kelas) use ($selectedTaId, $selectedTa) {
            
            $historis = \App\Models\HistoriSiswa::with('siswa')
                ->where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $selectedTaId)
                ->get();
            
            $kelas->jumlah_murid = $historis->count();
            
            // Siapkan penampung untuk dihitung
            $muridAktif = 0;
            $muridTidakAktif = 0;
            $muridTambahan = 0;

            if ($selectedTa) {
                foreach ($historis as $h) {
                    $siswa = $h->siswa;

                    // 1. HITUNG MURID TAMBAHAN (Siswa Baru)
                    // Cek apakah dia punya histori di TA yang lebih LAMA (<) dari TA terpilih
                    $punyaHistoriLama = \App\Models\HistoriSiswa::where('siswa_id', $siswa->id)
                        ->whereHas('tahunAjaran', function($q) use ($selectedTa) {
                            $q->where('tahun_ajaran', '<', $selectedTa->tahun_ajaran);
                        })->exists();
                    
                    if (!$punyaHistoriLama) {
                        $muridTambahan++; // Tidak punya masa lalu = Murid Baru di TA ini
                    }

                    // 2. HITUNG STATUS HISTORIS
                    if ($siswa->status == 'aktif') {
                        // Jika saat ini aktif, sudah pasti di masa lalu juga aktif
                        $muridAktif++;
                    } else {
                        // Jika saat ini statusnya 'TIDAK AKTIF' atau 'LULUS'
                        // Kita cek, apakah dia punya histori di TA yang lebih BARU (>) dari TA terpilih?
                        $punyaHistoriBaru = \App\Models\HistoriSiswa::where('siswa_id', $siswa->id)
                            ->whereHas('tahunAjaran', function($q) use ($selectedTa) {
                                $q->where('tahun_ajaran', '>', $selectedTa->tahun_ajaran);
                            })->exists();

                        if ($punyaHistoriBaru) {
                            // Punya histori di TA masa depan = Berarti di TA ini dia MASIH AKTIF
                            $muridAktif++;
                        } else {
                            // Tidak punya histori masa depan = TA ini adalah tempat terakhirnya (keluar/lulus)
                            $muridTidakAktif++;
                        }
                    }
                }
            }

            // Masukkan hasil perhitungan ke dalam property objek kelas
            $kelas->murid_aktif = $muridAktif;
            $kelas->murid_tidak_aktif = $muridTidakAktif;
            $kelas->murid_tambahan = $muridTambahan;

            return $kelas;
        });

        // Urutkan kelas sesuai hierarki (PG -> TK -> SD)
        $urutanKelas = [
            'Kelas PG' => 1, 'Kelas TK A' => 2, 'Kelas TK B' => 3,
            'Kelas 1 SD' => 4, 'Kelas 2 SD' => 5, 'Kelas 3 SD' => 6, 'Kelas 4 SD' => 7, 'Kelas 5 SD' => 8, 'Kelas 6 SD' => 9,
        ];
        
        $kelasList = $kelasList->sortBy(function($kelas) use ($urutanKelas) {
            return $urutanKelas[$kelas->nama_kelas] ?? 99;
        });

        return view('kelas.histori', compact('tahunAjarans', 'selectedTaId', 'selectedTa', 'kelasList'));
    }

    public function rincianHistori(Request $request)
    {
        $kelas_id = $request->input('kelas_id');
        $tahun_ajaran_id = $request->input('tahun_ajaran_id');

        abort_if(!$kelas_id || !$tahun_ajaran_id, 404);

        $kelas = \App\Models\Kelas::findOrFail($kelas_id);
        $selectedTa = \App\Models\TahunAjaran::findOrFail($tahun_ajaran_id);

        // Ambil histori siswa di kelas & TA tersebut
        $historis = \App\Models\HistoriSiswa::with('siswa')
            ->where('kelas_id', $kelas_id)
            ->where('tahun_ajaran_id', $tahun_ajaran_id)
            ->get()
            ->map(function ($h) use ($selectedTa) {
                $siswa = $h->siswa;
                
                // 1. LOGIKA CEK MURID BARU (Sama seperti di fungsi histori)
                $punyaHistoriLama = \App\Models\HistoriSiswa::where('siswa_id', $siswa->id)
                    ->whereHas('tahunAjaran', function($q) use ($selectedTa) {
                        $q->where('tahun_ajaran', '<', $selectedTa->tahun_ajaran);
                    })->exists();

                // Tandai jika dia tidak punya masa lalu di sistem = Murid Baru
                $h->is_murid_baru = !$punyaHistoriLama;

                // 2. LOGIKA MESIN WAKTU UNTUK STATUS DINAMIS (Bawaan Anda)
                if ($siswa->status == 'aktif') {
                    $h->dynamic_status = 'Aktif';
                    $h->status_class = 'bg-green-100 text-green-700';
                } else {
                    $punyaHistoriBaru = \App\Models\HistoriSiswa::where('siswa_id', $siswa->id)
                        ->whereHas('tahunAjaran', function($q) use ($selectedTa) {
                            $q->where('tahun_ajaran', '>', $selectedTa->tahun_ajaran);
                        })->exists();

                    if ($punyaHistoriBaru) {
                        $h->dynamic_status = 'Aktif'; // Masa lalu masih aktif
                        $h->status_class = 'bg-green-100 text-green-700';
                    } else {
                        $h->dynamic_status = ucwords($siswa->status); // Masa lalu sudah berhenti/lulus
                        $h->status_class = 'bg-red-100 text-red-700';
                    }
                }

                return $h;
            });

        return view('kelas.rincian_histori', compact('kelas', 'selectedTa', 'historis'));
    }
}