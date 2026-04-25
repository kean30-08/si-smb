<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Agenda;
use App\Models\TahunAjaran;
use App\Mail\BroadcastAgendaMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class AgendaController extends Controller
{
    /**
     * FUNGSI BANTUAN (HELPER) UNTUK VALIDASI RENTANG SEMESTER
     * Mencegah penyisipan jadwal di antara tanggal awal dan akhir semester lain.
     */
    private function checkSemesterOverlap($tanggal, $currentTaId)
    {
        if (!$currentTaId) return null;

        // Cari rentang wilayah (Tanggal Awal s/d Tanggal Akhir) dari SETIAP Tahun Ajaran LAIN
        $rentangSemesterLain = Agenda::select('tahun_ajaran_id', DB::raw('MIN(tanggal) as start_date'), DB::raw('MAX(tanggal) as end_date'))
            ->whereNotNull('tahun_ajaran_id')
            ->where('tahun_ajaran_id', '!=', $currentTaId)
            ->groupBy('tahun_ajaran_id')
            ->get();

        foreach ($rentangSemesterLain as $rentang) {
            // Jika tanggal yang diinput jatuh di DALAM wilayah semester lain, BLOKIR!
            if ($tanggal >= $rentang->start_date && $tanggal <= $rentang->end_date) {
                $namaTa = TahunAjaran::find($rentang->tahun_ajaran_id)->tahun_ajaran ?? 'Semester Lain';
                $startFormat = Carbon::parse($rentang->start_date)->translatedFormat('d M Y');
                $endFormat = Carbon::parse($rentang->end_date)->translatedFormat('d M Y');
                
                return "Gagal! Tanggal $tanggal menyusup di dalam rentang waktu $namaTa ($startFormat s/d $endFormat). Silakan pilih tanggal setelah semester tersebut berakhir.";
            }
        }
        
        // Pengecekan ekstra: Jika tanggalnya persis berbenturan dengan tanggal tunggal milik semester lain
        $existing = Agenda::where('tanggal', $tanggal)->where('tahun_ajaran_id', '!=', $currentTaId)->first();
        if ($existing) {
             $namaTa = $existing->tahunAjaran->tahun_ajaran ?? 'Semester Lain';
             return "Gagal! Tanggal $tanggal sudah dimiliki oleh $namaTa.";
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
        
        $tahunAktif = TahunAjaran::where('status', 'aktif')->first();
        $filterTahun = $request->input('tahun_ajaran_id', $tahunAktif ? $tahunAktif->id : null);
        $tahunAjarans = TahunAjaran::orderBy('created_at', 'desc')->get();

        $agendasGrouped = Agenda::selectRaw('tanggal, tahun_ajaran_id, count(id) as total_kegiatan, MIN(id) as first_agenda_id')
            ->when($filterTahun, function ($q, $filterTahun) {
                return $q->where('tahun_ajaran_id', $filterTahun);
            })
            ->when($search, function ($query, $search) {
                return $query->where('tanggal', 'like', "%{$search}%");
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
        $penanggungJawabIds = $agendas->first() ? $agendas->first()->penanggungJawab->pluck('id')->toArray() : [];
        $isAdmin = auth()->check() ? auth()->user()->isAdmin() : false;

        return view('agenda.show', compact('agendas', 'tanggal', 'pengajars', 'penanggungJawabIds', 'isAdmin'));
    }

    public function updatePic(Request $request, $tanggal)
    {
        $request->validate([
            'penanggung_jawab_id' => 'nullable|array',
            'penanggung_jawab_id.*' => 'exists:pengajars,id'
        ]);
        
        $agendas = Agenda::where('tanggal', $tanggal)->get();
        $picIds = $request->penanggung_jawab_id ?? [];
        
        foreach ($agendas as $agenda) {
            $agenda->penanggungJawab()->sync($picIds);
        }
        
        return back()->with('success', 'Daftar PIC Absensi hari tersebut berhasil diperbarui!');
    }

    public function create()
    {
        $pengajars = \App\Models\Pengajar::orderBy('nama_lengkap', 'asc')->get();
        return view('agenda.create', compact('pengajars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'penanggung_jawab_id' => 'nullable|array', 
            'penanggung_jawab_id.*' => 'exists:pengajars,id',
            'nama_kegiatan' => 'required|array',
            'nama_kegiatan.*' => 'required|string',
            'waktu_mulai' => 'required|array',
            'waktu_selesai' => 'required|array',
        ]);

        $tanggal = $request->tanggal;
        $tahunAktif = TahunAjaran::where('status', 'aktif')->first();
        $taId = $tahunAktif ? $tahunAktif->id : null;

        // VALIDASI WILAYAH SEMESTER: Memastikan tidak overlap dengan batas MIN dan MAX semester lain
        if ($taId) {
            $overlapError = $this->checkSemesterOverlap($tanggal, $taId);
            if ($overlapError) {
                return back()->withInput()->withErrors(['tanggal' => $overlapError]);
            }
        }

        $kegiatanWaktuSubmitted = [];
        foreach ($request->nama_kegiatan as $index => $nama) {
            $waktu = $request->waktu_mulai[$index];
            $key = $nama . '-' . $waktu; 
            
            if (in_array($key, $kegiatanWaktuSubmitted)) {
                return back()->withInput()->withErrors(['nama_kegiatan' => 'Terdapat duplikasi kegiatan "'.$nama.'" di jam yang sama pada form pengisian Anda!']);
            }
            $kegiatanWaktuSubmitted[] = $key;
        }

        foreach ($request->nama_kegiatan as $index => $nama) {
            $exists = Agenda::where('tanggal', $tanggal)
                            ->where('waktu_mulai', $request->waktu_mulai[$index])
                            ->where('nama_kegiatan', $nama)
                            ->exists();
            if ($exists) {
                return back()->withInput()->withErrors(['nama_kegiatan' => 'Kegiatan yang sama pada tanggal dan jam tersebut sudah dibuat!']);
            }
        }

        DB::transaction(function () use ($request, $tanggal, $taId) {
            $picIds = $request->penanggung_jawab_id ?? [];
            
            foreach ($request->nama_kegiatan as $index => $nama) {
                $agenda = Agenda::create([
                    'tahun_ajaran_id' => $taId,
                    'tanggal' => $tanggal,
                    'nama_kegiatan' => $nama,
                    'waktu_mulai' => $request->waktu_mulai[$index],
                    'waktu_selesai' => $request->waktu_selesai[$index],
                    'deskripsi_rundown' => $request->deskripsi_rundown[$index] ?? null,
                    'status' => 'akan datang',
                ]);

                if (!empty($picIds)) {
                    $agenda->penanggungJawab()->sync($picIds);
                }
            }
        });

        return redirect()->route('agenda.index')->with('success', 'Rangkaian jadwal berhasil ditambahkan!');
    }

    public function edit(Agenda $agenda)
    {
        return view('agenda.edit', compact('agenda'));
    }

    public function update(Request $request, Agenda $agenda)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'status' => 'required',
            'nama_kegiatan' => [
                'required',
                'string',
                Rule::unique('agendas')->where(function ($query) use ($request) {
                    return $query->where('tanggal', $request->tanggal)
                                 ->where('waktu_mulai', $request->waktu_mulai);
                })->ignore($agenda->id) 
            ],
        ], [
            'nama_kegiatan.unique' => 'Gagal mengubah! Kegiatan dengan nama dan jam mulai tersebut sudah ada di rundown.'
        ]);

        // VALIDASI WILAYAH SEMESTER SAAT UPDATE TANGGAL
        $agendaTaId = $agenda->tahun_ajaran_id;
        if ($agendaTaId) {
            $overlapError = $this->checkSemesterOverlap($request->tanggal, $agendaTaId);
            if ($overlapError) {
                return back()->withInput()->withErrors(['tanggal' => $overlapError]);
            }
        }

        $agenda->update($request->all());

        return redirect()->route('agenda.showDate', $request->tanggal)->with('success', 'Data agenda berhasil diperbarui!');
    }

    public function destroy(Agenda $agenda)
    {
        $tanggal = $agenda->tanggal;
        $agenda->delete();
        return redirect()->route('agenda.showDate', $tanggal)->with('success', 'Acara rundown berhasil dihapus!');
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
            ->with('success', 'Semua agenda pada ' . Carbon::parse($tanggal)->translatedFormat('d F Y') . ' berhasil dihapus! (' . $count . ' kegiatan)');
    }

    public function createDetail($tanggal)
    {
        return view('agenda.create_detail', compact('tanggal'));
    }

    public function storeDetail(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'nama_kegiatan' => [
                'required',
                'string',
                Rule::unique('agendas')->where(function ($query) use ($request) {
                    return $query->where('tanggal', $request->tanggal)
                                 ->where('waktu_mulai', $request->waktu_mulai);
                })
            ],
        ], [
            'nama_kegiatan.unique' => 'Gagal menambah! Kegiatan "'.$request->nama_kegiatan.'" pada jam '.$request->waktu_mulai.' sudah ada di rundown ini.'
        ]);

        $tahunAktif = TahunAjaran::where('status', 'aktif')->first();
        $taId = $tahunAktif ? $tahunAktif->id : null;

        // VALIDASI WILAYAH SEMESTER
        if ($taId) {
            $overlapError = $this->checkSemesterOverlap($request->tanggal, $taId);
            if ($overlapError) {
                // Menaruh pesan error di kolom tanggal agar pop-up error muncul di form
                return back()->withInput()->withErrors(['tanggal' => $overlapError]); 
            }
        }

        $data = $request->except('penanggung_jawab_id');
        $data['status'] = 'akan datang';
        $data['tahun_ajaran_id'] = $taId;
        
        $newAgenda = Agenda::create($data);

        $existingAgenda = Agenda::where('tanggal', $request->tanggal)->where('id', '!=', $newAgenda->id)->first();
        if ($existingAgenda) {
            $picIds = $existingAgenda->penanggungJawab->pluck('id')->toArray();
            if (!empty($picIds)) {
                $newAgenda->penanggungJawab()->sync($picIds);
            }
        }

        return redirect()->route('agenda.showDate', $request->tanggal)->with('success', 'Acara tambahan berhasil dimasukkan!');
    }

    public function broadcastPdf($tanggal) 
    {
        set_time_limit(300); 
        $agendas = Agenda::where('tanggal', $tanggal)->orderBy('waktu_mulai', 'asc')->get();

        if ($agendas->isEmpty()) {
            return redirect()->route('agenda.showDate', $tanggal)->with('error', 'Tidak ada data jadwal pada tanggal tersebut.');
        }

        $admin = \App\Models\User::first(); 

        try {
            $pdf = Pdf::loadView('agenda.pdf', compact('agendas', 'tanggal', 'admin'));
            $pdfContent = $pdf->output();

            $emails = Siswa::where('status', 'aktif')
                           ->whereNotNull('email_orang_tua')
                           ->where('email_orang_tua', '!=', '')
                           ->distinct() 
                           ->pluck('email_orang_tua');

            if ($emails->isEmpty()) {
                return redirect()->route('agenda.showDate', $tanggal)->with('error', 'Tidak ada data email orang tua yang tersimpan.');
            }

            foreach ($emails as $email) {
                Mail::to($email)->send(new BroadcastAgendaMail($pdfContent));
                usleep(500000);
            }

            return redirect()->route('agenda.showDate', $tanggal)
                             ->with('success', 'Jadwal berhasil dikirim ke ' . $emails->count() . ' email orang tua!');

        } catch (\Exception $e) {
            return redirect()->route('agenda.showDate', $tanggal)
                             ->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }
    
    public function downloadPdf($tanggal)
    {
        $agendas = Agenda::where('tanggal', $tanggal)->orderBy('waktu_mulai', 'asc')->get();

        if ($agendas->isEmpty()) {
            return redirect()->route('agenda.showDate', $tanggal)->with('error', 'Tidak ada data jadwal untuk diunduh.');
        }

        $admin = \App\Models\User::first(); 
        $pdf = Pdf::loadView('agenda.pdf', compact('agendas', 'tanggal', 'admin'));
        $fileName = 'Rundown_Kegiatan_' . Carbon::parse($tanggal)->format('d_M_Y') . '.pdf';
        
        return $pdf->download($fileName);
    }
}