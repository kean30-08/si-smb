<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AgendaController extends Controller
{
    private function getTaWeight($taString)
    {
        $parts = explode(' ', $taString);
        if (count($parts) < 2) return 0;
        $years = explode('/', $parts[0]);
        $baseYear = (int)$years[0]; 
        $semester = strtolower($parts[1]) == 'ganjil' ? 1 : 2;
        return ($baseYear * 10) + $semester; 
    }

    private function checkSemesterOverlap($tanggal, $currentTaId)
    {
        if (!$currentTaId) return null;

        $currentTa = TahunAjaran::find($currentTaId);
        if (!$currentTa) return null;

        $taString = $currentTa->tahun_ajaran;
        $parts = explode(' ', $taString);
        if (count($parts) >= 1) {
            $years = explode('/', $parts[0]);
            if (count($years) == 2) {
                $startYear = (int)$years[0];
                $endYear = (int)$years[1];
                $inputYear = (int) Carbon::parse($tanggal)->year;

                if ($inputYear < $startYear || $inputYear > $endYear) {
                    $tanggalFormat = Carbon::parse($tanggal)->translatedFormat('d M Y');
                    return "Gagal! Tanggal $tanggalFormat (Tahun $inputYear) tidak valid karena berada di luar cakupan Tahun Ajaran yang dipilih ($startYear - $endYear).";
                }
            }
        }
        
        $currentWeight = $this->getTaWeight($currentTa->tahun_ajaran);

        $rentangSemesterLain = Agenda::select('tahun_ajaran_id', DB::raw('MIN(tanggal) as start_date'), DB::raw('MAX(tanggal) as end_date'))
            ->whereNotNull('tahun_ajaran_id')
            ->where('tahun_ajaran_id', '!=', $currentTaId)
            ->groupBy('tahun_ajaran_id')
            ->get();

        foreach ($rentangSemesterLain as $rentang) {
            $lainTa = TahunAjaran::find($rentang->tahun_ajaran_id);
            if (!$lainTa) continue;
            
            $lainWeight = $this->getTaWeight($lainTa->tahun_ajaran);
            $namaTaLain = $lainTa->tahun_ajaran;
            
            $startFormat = Carbon::parse($rentang->start_date)->translatedFormat('d M Y');
            $endFormat = Carbon::parse($rentang->end_date)->translatedFormat('d M Y');
            $tanggalFormat = Carbon::parse($tanggal)->translatedFormat('d M Y');

            if ($tanggal >= $rentang->start_date && $tanggal <= $rentang->end_date) {
                return "Gagal! Tanggal $tanggalFormat menyusup di dalam rentang waktu $namaTaLain ($startFormat s/d $endFormat).";
            }

            if ($currentWeight < $lainWeight && $tanggal >= $rentang->start_date) {
                return "Gagal kronologis! Semester {$currentTa->tahun_ajaran} adalah semester lampau, tidak boleh melangkahi jadwal masa depan.";
            }

            if ($currentWeight > $lainWeight && $tanggal <= $rentang->end_date) {
                return "Gagal kronologis! Semester {$currentTa->tahun_ajaran} adalah semester baru, tidak boleh mundur mendahului jadwal lampau.";
            }
        }
        
        $existing = Agenda::where('tanggal', $tanggal)->where('tahun_ajaran_id', '!=', $currentTaId)->first();
        if ($existing) {
             $namaTa = $existing->tahunAjaran->tahun_ajaran ?? 'Semester Lain';
             return "Gagal! Tanggal " . Carbon::parse($tanggal)->translatedFormat('d M Y') . " sudah di-booking oleh $namaTa.";
        }

        return null;
    }

    public function index(Request $request)
    {
        $now = now();
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();
        $isAdmin = auth()->check() ? auth()->user()->isAdmin() : false;

        Agenda::where('status', 'akan datang')
            ->where('tanggal', $today)
            ->where('waktu_mulai', '<=', $currentTime)
            ->where('waktu_selesai', '>', $currentTime)
            ->update(['status' => 'sedang berlangsung']);

        Agenda::whereIn('status', ['akan datang', 'sedang berlangsung'])
            ->where(function($query) use ($today, $currentTime) {
                $query->where('tanggal', '<', $today) 
                    ->orWhere(function($q) use ($today, $currentTime) {
                        $q->where('tanggal', $today)
                            ->where('waktu_selesai', '<=', $currentTime);
                    });
            })->update(['status' => 'selesai']);

       $search = $request->input('search');
        $querySearch = $search; // Variabel khusus untuk disuntikkan ke database

        if ($search) {
            $searchClean = strtolower(trim($search));
            
            // Peta nama bulan Indonesia ke format angka (dengan pengapit strip)
            $bulanIndo = [
                'januari' => '01', 'februari' => '02', 'maret' => '03',
                'april' => '04', 'mei' => '05', 'juni' => '06',
                'juli' => '07', 'agustus' => '08', 'september' => '09',
                'oktober' => '10', 'november' => '11', 'desember' => '12'
            ];

            $isMonthTranslated = false;
            foreach ($bulanIndo as $indo => $angka) {
                if (str_contains($searchClean, $indo)) {
                    $searchClean = str_replace($indo, "-$angka-", $searchClean);
                    $isMonthTranslated = true;
                    break;
                }
            }

            if ($isMonthTranslated) {
                // Pecah teks pencarian berdasarkan spasi untuk menyusun YYYY-MM-DD parsial
                $parts = array_values(array_filter(explode(' ', $searchClean)));
                
                if (count($parts) === 3) {
                    // Kasus "6 juli 2026" -> mencari "2026-07-06"
                    $tgl = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                    $bln = str_replace('-', '', $parts[1]);
                    $thn = $parts[2];
                    $querySearch = "$thn-$bln-$tgl";
                } elseif (count($parts) === 2) {
                    if (str_contains($parts[0], '-')) {
                        // Kasus "juli 2026" -> mencari "%2026-07-%"
                        $bln = str_replace('-', '', $parts[0]);
                        $thn = $parts[1];
                        $querySearch = "$thn-$bln-";
                    } else {
                        // Kasus "6 juli" -> mencari "%-07-06"
                        $tgl = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                        $bln = str_replace('-', '', $parts[1]);
                        $querySearch = "-$bln-$tgl";
                    }
                } elseif (count($parts) === 1) {
                    // Kasus "juli" -> mencari "%-07-%"
                    $querySearch = $parts[0];
                }
            }
        }
        
        $tahunAktif = TahunAjaran::where('status', 'aktif')->first();
        $filterTahun = $request->input('tahun_ajaran_id');
        
        // PERBAIKAN DI SINI: Gunakan 'tahun_ajaran' bukan 'created_at'
        $tahunAjarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

        $agendasGrouped = Agenda::selectRaw('tanggal, tahun_ajaran_id, count(id) as total_kegiatan, MIN(id) as first_agenda_id')
            ->when($filterTahun, function ($q, $filterTahun) {
                return $q->where('tahun_ajaran_id', $filterTahun);
            })
            ->when($querySearch, function ($query, $querySearch) {
                return $query->where('tanggal', 'like', "%{$querySearch}%");
            })
            // TAMBAHAN: Sembunyikan agenda internal dari tamu (Guest) yang belum login
            ->when(!auth()->check(), function ($query) { 
                return $query->where('is_public', 1);
                })
            ->with('tahunAjaran') 
            ->groupBy('tanggal', 'tahun_ajaran_id') 
            ->orderBy('tanggal', 'desc')
            ->paginate(8)
            ->appends(['search' => $search, 'tahun_ajaran_id' => $filterTahun]);

        $firstAgendaIds = $agendasGrouped->pluck('first_agenda_id');
        $agendasWithPicsRaw = Agenda::with('penanggungJawab')->whereIn('id', $firstAgendaIds)->get();
        
        $agendasWithPics = [];
        foreach($agendasWithPicsRaw as $agenda) {
            $key = $agenda->tanggal . '_' . $agenda->tahun_ajaran_id;
            $agendasWithPics[$key] = $agenda;
        }

        if ($request->ajax()) {
            return view('agenda.partials._table', compact('agendasGrouped', 'agendasWithPics', 'isAdmin'))->render();
        }

        return view('agenda.index', compact('agendasGrouped', 'agendasWithPics', 'isAdmin', 'tahunAjarans', 'filterTahun'));
    }

    public function showDate($tanggal)
    {
        $agendas = Agenda::with('penanggungJawab')->where('tanggal', $tanggal)->orderBy('waktu_mulai', 'asc')->get();
        $pengajars = \App\Models\Pengajar::orderBy('nama_lengkap', 'asc')->get();
        
        // Ambil PIC dari kegiatan pertama (karena ini sudah berbasis tanggal)
        $penanggungJawabIds = $agendas->first() ? $agendas->first()->penanggungJawab->pluck('id')->toArray() : [];
        $isAdmin = auth()->check() ? auth()->user()->isAdmin() : false;

        return view('agenda.show', compact('agendas', 'tanggal', 'pengajars', 'penanggungJawabIds', 'isAdmin'));
    }

    public function updatePic(Request $request, $tanggal)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:100',
            'is_public' => 'required|boolean',
            'is_libur' => 'required|boolean', // TAMBAHAN
            'penanggung_jawab_id' => 'nullable|array',
            'penanggung_jawab_id.*' => 'exists:pengajars,id'
        ]);
        
        $agendas = Agenda::where('tanggal', $tanggal)->get();
        $picIds = $request->penanggung_jawab_id ?? [];
        
        foreach ($agendas as $agenda) {
            $agenda->update([
                'nama_kegiatan' => $request->nama_kegiatan,
                'is_public' => $request->is_public,
                'is_libur' => $request->is_libur // TAMBAHAN
            ]);
            $agenda->penanggungJawab()->sync($picIds);
        }
        
        return back()->with('success', 'Detail agenda dan Penanggung Jawab berhasil diperbarui!');
    }

    public function create()
    {
        $pengajars = \App\Models\Pengajar::orderBy('nama_lengkap', 'asc')->get();
        return view('agenda.create', compact('pengajars'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Tanggal, Nama Kegiatan, dan PIC
        $request->validate([
            'tanggal' => 'required|date',
            'nama_kegiatan' => 'required|string|max:100',
            'is_public' => 'required|boolean', // Validasi Maksimal 100 huruf
            'is_libur' => 'required|boolean',
            'penanggung_jawab_id' => 'nullable|array', 
            'penanggung_jawab_id.*' => 'exists:pengajars,id',
        ]);

        $tanggal = $request->tanggal;
        $tahunAktif = TahunAjaran::where('status', 'aktif')->first();
        $taId = $tahunAktif ? $tahunAktif->id : null;

        // 2. Validasi Wilayah Semester
        if ($taId) {
            $overlapError = $this->checkSemesterOverlap($tanggal, $taId);
            if ($overlapError) {
                return back()->withInput()->withErrors(['tanggal' => $overlapError]);
            }
        }

        // 3. Mencegah duplikasi
        $exists = Agenda::where('tanggal', $tanggal)->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['tanggal' => 'Jadwal absensi untuk tanggal tersebut sudah dibuat!']);
        }

        // 4. Simpan ke Database
        DB::transaction(function () use ($request, $tanggal, $taId) {
            $picIds = $request->penanggung_jawab_id ?? [];
            
            $agenda = Agenda::create([
                'tahun_ajaran_id' => $taId,
                'tanggal' => $tanggal,
                // Menggunakan inputan user alih-alih tulisan statis 'Absensi Harian'
                'nama_kegiatan' => $request->nama_kegiatan, 
                'is_public' => $request->is_public,
                'is_libur' => $request->is_libur,
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '12:00:00',
                'deskripsi_rundown' => null,
                'status' => 'akan datang',
            ]);

            if (!empty($picIds)) {
                $agenda->penanggungJawab()->sync($picIds);
            }
        });

        return redirect()->route('agenda.index')->with('success', 'Jadwal absensi berhasil dibuat!');
    }

    public function destroyDate($tanggal)
    {
        $count = Agenda::where('tanggal', $tanggal)->count();
        if ($count === 0) {
            return redirect()->route('agenda.showDate', $tanggal)
                ->with('error', 'Tidak ada agenda pada tanggal tersebut.');
        }

        Agenda::where('tanggal', $tanggal)->delete();
        return redirect()->route('agenda.index')
            ->with('success', 'Semua agenda pada ' . Carbon::parse($tanggal)->translatedFormat('d F Y') . ' berhasil dihapus.');
    }
}