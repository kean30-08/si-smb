<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahun_ajarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        return view('tahun_ajaran.index', compact('tahun_ajarans'));
    }

    public function create() { return view('tahun_ajaran.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_awal' => 'required|numeric|min:2000|max:2099',
            'semester' => 'required|in:Ganjil,Genap'
        ]);

        // ==========================================
        // VALIDASI URUTAN TAHUN AJARAN (MAJU & MUNDUR)
        // ==========================================
        $latestTa = TahunAjaran::orderBy('tahun_ajaran', 'desc')->first();
        $oldestTa = TahunAjaran::orderBy('tahun_ajaran', 'asc')->first();

        $expectedNext = $this->getExpectedNextTahunAjaran($latestTa);
        $expectedPrev = $this->getExpectedPreviousTahunAjaran($oldestTa);

        if ($latestTa && $oldestTa) {
            $reqTahunAwal = (int) $request->tahun_awal;
            $reqSemester = strtolower($request->semester);

            $isNext = ($expectedNext && $reqTahunAwal === $expectedNext['tahun_awal'] && $reqSemester === strtolower($expectedNext['semester']));
            $isPrev = ($expectedPrev && $reqTahunAwal === $expectedPrev['tahun_awal'] && $reqSemester === strtolower($expectedPrev['semester']));

            if (!$isNext && !$isPrev) {
                return back()->withInput()->withErrors([
                    'tahun_awal' => "Urutan tidak valid! Anda hanya bisa membuat TA yang berurutan maju ({$expectedNext['format']}) atau mundur ke masa lalu ({$expectedPrev['format']})."
                ]);
            }
        }
        // ==========================================

        $tahun_akhir = $request->tahun_awal + 1;
        $format_tahun_ajaran = $request->tahun_awal . '/' . $tahun_akhir . ' ' . $request->semester;
        $request->merge(['tahun_ajaran' => $format_tahun_ajaran]);
        $request->validate(['tahun_ajaran' => 'unique:tahun_ajarans,tahun_ajaran']);

        TahunAjaran::create([
            'tahun_ajaran' => $format_tahun_ajaran,
            'status' => 'tidak aktif'
        ]);

        return redirect()->route('tahun_ajaran.index')->with('success', 'Tahun Ajaran baru berhasil ditambahkan secara berurutan.');
    }

    public function edit(TahunAjaran $tahun_ajaran) { return view('tahun_ajaran.edit', compact('tahun_ajaran')); }

    public function update(Request $request, TahunAjaran $tahun_ajaran)
    {
        $request->validate([
            'tahun_awal' => 'required|numeric|min:2000|max:2099',
            'semester' => 'required|in:Ganjil,Genap'
        ]);

        // ==========================================
        // VALIDASI URUTAN SAAT EDIT (Hanya boleh edit ujung rantai)
        // ==========================================
        $count = TahunAjaran::count();
        if ($count > 1) {
            $latestTa = TahunAjaran::orderBy('tahun_ajaran', 'desc')->first();
            $oldestTa = TahunAjaran::orderBy('tahun_ajaran', 'asc')->first();

            $reqTahunAwal = (int) $request->tahun_awal;
            $reqSemester = strtolower($request->semester);

            if ($tahun_ajaran->id === $latestTa->id) {
                // Jika edit data paling atas (terbaru)
                $secondLatest = TahunAjaran::orderBy('tahun_ajaran', 'desc')->skip(1)->first();
                $expected = $this->getExpectedNextTahunAjaran($secondLatest);
                
                if ($expected && ($reqTahunAwal !== $expected['tahun_awal'] || $reqSemester !== strtolower($expected['semester']))) {
                    return back()->withInput()->withErrors([
                        'tahun_awal' => "Karena ini data terbaru, Anda hanya boleh mengubahnya menjadi {$expected['format']}."
                    ]);
                }
            } elseif ($tahun_ajaran->id === $oldestTa->id) {
                // Jika edit data paling bawah (terlama)
                $secondOldest = TahunAjaran::orderBy('tahun_ajaran', 'asc')->skip(1)->first();
                $expected = $this->getExpectedPreviousTahunAjaran($secondOldest);

                if ($expected && ($reqTahunAwal !== $expected['tahun_awal'] || $reqSemester !== strtolower($expected['semester']))) {
                    return back()->withInput()->withErrors([
                        'tahun_awal' => "Karena ini data terlama, Anda hanya boleh mengubahnya menjadi {$expected['format']}."
                    ]);
                }
            } else {
                return back()->withInput()->withErrors([
                    'tahun_awal' => "Data di tengah urutan tidak boleh diubah agar histori tidak rusak. Hapus data dari ujung (terbaru/terlama) jika ingin merombak."
                ]);
            }
        }
        // ==========================================

        $tahun_akhir = $request->tahun_awal + 1;
        $format_tahun_ajaran = $request->tahun_awal . '/' . $tahun_akhir . ' ' . $request->semester;
        $request->merge(['tahun_ajaran' => $format_tahun_ajaran]);
        $request->validate(['tahun_ajaran' => 'unique:tahun_ajarans,tahun_ajaran,' . $tahun_ajaran->id]);

        $tahun_ajaran->update(['tahun_ajaran' => $format_tahun_ajaran]);
        return redirect()->route('tahun_ajaran.index')->with('success', 'Tahun Ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahun_ajaran)
    {
        if ($tahun_ajaran->status == 'aktif') {
            return back()->with('error', 'Tidak dapat menghapus Tahun Ajaran yang sedang Aktif!');
        }

        // Cegah menghapus TA di tengah-tengah
        $count = TahunAjaran::count();
        if ($count > 1) {
            $latestTa = TahunAjaran::orderBy('tahun_ajaran', 'desc')->first();
            $oldestTa = TahunAjaran::orderBy('tahun_ajaran', 'asc')->first();

            if ($tahun_ajaran->id !== $latestTa->id && $tahun_ajaran->id !== $oldestTa->id) {
                return back()->with('error', 'Anda hanya dapat menghapus Tahun Ajaran di ujung rantai (paling baru atau paling lama) agar histori tidak terputus!');
            }
        }

        $tahun_ajaran->delete();
        return redirect()->route('tahun_ajaran.index')->with('success', 'Tahun Ajaran berhasil dihapus.');
    }

    public function aktifkan(\App\Models\TahunAjaran $tahun_ajaran)
    {
        $taLama = \App\Models\TahunAjaran::where('status', 'aktif')->first();

        \Illuminate\Support\Facades\DB::transaction(function () use ($tahun_ajaran, $taLama) {
            \App\Models\TahunAjaran::query()->update(['status' => 'tidak aktif']);
            $tahun_ajaran->update(['status' => 'aktif']);

            if ($taLama && $taLama->id != $tahun_ajaran->id) {
                $this->prosesMigrasiOtomatis($taLama, $tahun_ajaran);
            }
        });

        return redirect()->route('tahun_ajaran.index')->with('success', 'Tahun Ajaran berhasil diaktifkan dan sistem telah mensinkronisasi lompatan histori siswa secara otomatis!');
    }

    /**
     * FUNGSI BANTUAN 1: Menentukan TA MAJU (Selanjutnya)
     */
    private function getExpectedNextTahunAjaran($referenceTa)
    {
        if (!$referenceTa) return null;

        preg_match('/(\d{4})\/(\d{4})\s+(Ganjil|Genap)/i', $referenceTa->tahun_ajaran, $match);
        if (!$match) return null;

        $thnAwal = (int) $match[1];
        $sem = strtolower($match[3]);

        if ($sem == 'ganjil') {
            $nextThnAwal = $thnAwal;
            $nextSem = 'Genap';
        } else {
            $nextThnAwal = $thnAwal + 1;
            $nextSem = 'Ganjil';
        }

        return [
            'tahun_awal' => $nextThnAwal,
            'semester' => $nextSem,
            'format' => $nextThnAwal . '/' . ($nextThnAwal + 1) . ' ' . ucfirst($nextSem)
        ];
    }

    /**
     * FUNGSI BANTUAN 2: Menentukan TA MUNDUR (Masa Lalu)
     */
    private function getExpectedPreviousTahunAjaran($referenceTa)
    {
        if (!$referenceTa) return null;

        preg_match('/(\d{4})\/(\d{4})\s+(Ganjil|Genap)/i', $referenceTa->tahun_ajaran, $match);
        if (!$match) return null;

        $thnAwal = (int) $match[1];
        $sem = strtolower($match[3]);

        if ($sem == 'genap') {
            // Sebelum Genap adalah Ganjil di tahun yang sama
            $prevThnAwal = $thnAwal;
            $prevSem = 'Ganjil';
        } else {
            // Sebelum Ganjil adalah Genap di TAHUN SEBELUMNYA
            $prevThnAwal = $thnAwal - 1;
            $prevSem = 'Genap';
        }

        return [
            'tahun_awal' => $prevThnAwal,
            'semester' => $prevSem,
            'format' => $prevThnAwal . '/' . ($prevThnAwal + 1) . ' ' . ucfirst($prevSem)
        ];
    }

    private function prosesMigrasiOtomatis($taLama, $taBaru)
    {
        preg_match('/(\d{4})\/(\d{4})\s+(Ganjil|Genap)/i', $taLama->tahun_ajaran, $matchLama);
        preg_match('/(\d{4})\/(\d{4})\s+(Ganjil|Genap)/i', $taBaru->tahun_ajaran, $matchBaru);

        if (!$matchLama || !$matchBaru) return; 

        $thnLama = (int) $matchLama[1];
        $semLama = strtolower($matchLama[3]);

        $thnBaru = (int) $matchBaru[1];
        $semBaru = strtolower($matchBaru[3]);

        if ($thnBaru < $thnLama || ($thnBaru == $thnLama && $semLama == 'genap' && $semBaru == 'ganjil')) {
            return;
        }

        $kelasOrdered = \App\Models\Kelas::all()->sortBy(function($k) {
            $urutan = [
                'Kelas PG' => 1, 'Kelas TK A' => 2, 'Kelas TK B' => 3,
                'Kelas 1 SD' => 4, 'Kelas 2 SD' => 5, 'Kelas 3 SD' => 6, 'Kelas 4 SD' => 7, 'Kelas 5 SD' => 8, 'Kelas 6 SD' => 9,
                'Kelas 1 SMP' => 10, 'Kelas 2 SMP' => 11, 'Kelas 3 SMP' => 12,
                'Kelas 1 SMA' => 13, 'Kelas 2 SMA' => 14, 'Kelas 3 SMA' => 15,
            ];
            return $urutan[$k->nama_kelas] ?? 99;
        })->values();

        $kelasIdArray = $kelasOrdered->pluck('id')->toArray();

        $historis = \App\Models\HistoriSiswa::with('siswa')
            ->where('tahun_ajaran_id', $taLama->id)
            ->whereHas('siswa', function($q) {
                $q->where('status', 'aktif');
            })->get();

        foreach ($historis as $histori) {
            $currentThn = $thnLama;
            $currentSem = $semLama;
            $currentKelasId = $histori->kelas_id;

            while ($currentThn < $thnBaru || ($currentThn == $thnBaru && $currentSem != $semBaru)) {
                
                if ($currentSem == 'ganjil') {
                    $currentSem = 'genap'; 
                } else {
                    $currentSem = 'ganjil';
                    $currentThn++; 
                    
                    $currentIndex = array_search($currentKelasId, $kelasIdArray);
                    
                    if ($currentIndex !== false && isset($kelasIdArray[$currentIndex + 1])) {
                        $currentKelasId = $kelasIdArray[$currentIndex + 1];
                    } else {
                        $histori->siswa->update(['status' => 'lulus']);
                        break; 
                    }
                }

                $semStr = ucfirst($currentSem);
                $nextTaStr = "{$currentThn}/" . ($currentThn + 1) . " {$semStr}";

                if ($currentThn == $thnBaru && $currentSem == $semBaru) {
                    $targetTaId = $taBaru->id; 
                } else {
                    $intermediateTa = \App\Models\TahunAjaran::firstOrCreate(
                        ['tahun_ajaran' => $nextTaStr],
                        ['status' => 'tidak aktif'] 
                    );
                    $targetTaId = $intermediateTa->id;
                }

                \App\Models\HistoriSiswa::firstOrCreate([
                    'siswa_id' => $histori->siswa_id,
                    'tahun_ajaran_id' => $targetTaId
                ], [
                    'kelas_id' => $currentKelasId
                ]);
            }
        }
    }
}