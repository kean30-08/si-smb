<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Agenda;
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
     * Menampilkan daftar agenda yang dikelompokkan berdasarkan tanggal.
     * 
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $now = now();
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();

        $isAdmin = auth()->user()->isAdmin();

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

        // Definisikan $isAdmin di sini
        $isAdmin = auth()->user()->isAdmin();

        $agendasGrouped = Agenda::selectRaw('tanggal, count(id) as total_kegiatan, MAX(penanggung_jawab_id) as penanggung_jawab_id')
            ->with('penanggungJawab') 
            ->when($search, function ($query, $search) {
                return $query->where('tanggal', 'like', "%{$search}%");
            })
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->paginate(8)
            ->appends(['search' => $search]);

        if ($request->ajax()) {
            // Pastikan $isAdmin ikut dikirim ke file _table
            return view('agenda.partials._table', compact('agendasGrouped', 'isAdmin'))->render();
        }

        // Pastikan $isAdmin ikut dikirim ke halaman index
        return view('agenda.index', compact('agendasGrouped', 'isAdmin'));
    }

    public function showDate($tanggal)
    {
        $agendas = Agenda::where('tanggal', $tanggal)->orderBy('waktu_mulai', 'asc')->get();
        $pengajars = \App\Models\Pengajar::orderBy('nama_lengkap', 'asc')->get();
        $penanggungJawabId = $agendas->first()->penanggung_jawab_id ?? null;
        
        $isAdmin = auth()->user()->isAdmin();

        // Cukup kirimkan data ini saja, tidak perlu defaultKelasId
        return view('agenda.show', compact('agendas', 'tanggal', 'pengajars', 'penanggungJawabId', 'isAdmin'));
    }

    // TAMBAHKAN FUNGSI BARU UNTUK UPDATE PIC
    public function updatePic(Request $request, $tanggal)
    {
        $request->validate(['penanggung_jawab_id' => 'required|exists:pengajars,id']);
        
        // Update semua acara di tanggal tersebut dengan PIC yang baru
        Agenda::where('tanggal', $tanggal)->update(['penanggung_jawab_id' => $request->penanggung_jawab_id]);
        
        return back()->with('success', 'Penanggung Jawab hari tersebut berhasil diperbarui!');
    }

    public function create()
    {
        // Ambil semua pengajar untuk dropdown
        $pengajars = \App\Models\Pengajar::orderBy('nama_lengkap', 'asc')->get();
        return view('agenda.create', compact('pengajars'));
    }

    /**
     * Menyimpan rangkaian agenda baru dalam bentuk array (bulk store).
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'penanggung_jawab_id' => 'required|exists:pengajars,id',
            'nama_kegiatan' => 'required|array',
            'nama_kegiatan.*' => 'required|string',
            'waktu_mulai' => 'required|array',
            'waktu_selesai' => 'required|array',
        ]);

        $tanggal = $request->tanggal;

        // 1. VALIDASI MANUAL: Cek apakah di dalam form yang disubmit ada data kembar
        $kegiatanWaktuSubmitted = [];
        foreach ($request->nama_kegiatan as $index => $nama) {
            $waktu = $request->waktu_mulai[$index];
            $key = $nama . '-' . $waktu; // Contoh: "test 1-08:00"
            
            if (in_array($key, $kegiatanWaktuSubmitted)) {
                return back()->withInput()->withErrors(['nama_kegiatan' => 'Terdapat duplikasi kegiatan "'.$nama.'" di jam yang sama pada form pengisian Anda!']);
            }
            $kegiatanWaktuSubmitted[] = $key;
        }

        // 2. VALIDASI DATABASE: Cek apakah jadwal tersebut sudah ada di Database
        foreach ($request->nama_kegiatan as $index => $nama) {
            $exists = Agenda::where('tanggal', $tanggal)
                            ->where('waktu_mulai', $request->waktu_mulai[$index])
                            ->where('nama_kegiatan', $nama)
                            ->exists();
            if ($exists) {
                return back()->withInput()->withErrors(['nama_kegiatan' => 'Kegiatan yang sama pada tanggal dan jam tersebut sudah dibuat!']);
            }
        }

        // Jika lolos semua validasi, baru simpan ke database
        DB::transaction(function () use ($request, $tanggal) {
            foreach ($request->nama_kegiatan as $index => $nama) {
                Agenda::create([
                    'tanggal' => $tanggal,
                    'penanggung_jawab_id' => $request->penanggung_jawab_id,
                    'nama_kegiatan' => $nama,
                    'waktu_mulai' => $request->waktu_mulai[$index],
                    'waktu_selesai' => $request->waktu_selesai[$index],
                    'deskripsi_rundown' => $request->deskripsi_rundown[$index] ?? null,
                    'status' => 'akan datang',
                ]);
            }
        });

        return redirect()->route('agenda.index')->with('success', 'Rangkaian jadwal berhasil ditambahkan!');
    }

    public function edit(Agenda $agenda)
    {
        return view('agenda.edit', compact('agenda'));
    }

    /**
     * Memperbarui data agenda tunggal.
     */
    public function update(Request $request, Agenda $agenda)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'status' => 'required',
            // VALIDASI KOMBINASI: Cek unik tapi boleh nama yang sama asal jam/tanggal beda
            'nama_kegiatan' => [
                'required',
                'string',
                Rule::unique('agendas')->where(function ($query) use ($request) {
                    return $query->where('tanggal', $request->tanggal)
                                 ->where('waktu_mulai', $request->waktu_mulai);
                })->ignore($agenda->id) // Abaikan ID data ini sendiri (agar bisa disave walau tidak diganti namanya)
            ],
        ], [
            'nama_kegiatan.unique' => 'Gagal mengubah! Kegiatan dengan nama dan jam mulai tersebut sudah ada di rundown.'
        ]);

        $agenda->update($request->all());

        return redirect()->route('agenda.showDate', $request->tanggal)->with('success', 'Data agenda berhasil diperbarui!');
    }

    public function destroy(Agenda $agenda)
    {
        $tanggal = $agenda->tanggal;
        $agenda->delete();
        return redirect()->route('agenda.showDate', $tanggal)->with('success', 'Acara rundown berhasil dihapus!');
    }

    /**
     * Menghapus semua agenda yang berada pada tanggal yang sama.
     */
    public function destroyDate($tanggal)
    {
        $count = Agenda::where('tanggal', $tanggal)->count();

        if ($count === 0) {
            return redirect()->route('agenda.showDate', $tanggal)
                ->with('error', 'Tidak ada agenda pada tanggal tersebut.');
        }

        Agenda::where('tanggal', $tanggal)->delete();

        $formatted = null;
        try {
            $formatted = Carbon::parse($tanggal)->translatedFormat('d F Y');
        } catch (\Exception $e) {
            $formatted = $tanggal;
        }

        return redirect()->route('agenda.index')
            ->with('success', 'Semua agenda pada ' . $formatted . ' berhasil dihapus! (' . $count . ' kegiatan)');
    }

    public function createDetail($tanggal)
    {
        return view('agenda.create_detail', compact('tanggal'));
    }

    /**
     * Menyimpan satu detail acara tambahan pada tanggal yang sudah ada.
     */
    public function storeDetail(Request $request)
    {

        $request->validate([
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            // VALIDASI KOMBINASI UNTUK TAMBAH 1 JADWAL
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

        // Cari PIC dari agenda yang sudah ada di tanggal tersebut
        $existingAgenda = Agenda::where('tanggal', $request->tanggal)->first();
        $pic_id = $existingAgenda ? $existingAgenda->penanggung_jawab_id : null;

        Agenda::create(array_merge($request->all(), [
            'status' => 'akan datang',
            'penanggung_jawab_id' => $pic_id // Warisi PIC hari itu
        ]));

        return redirect()->route('agenda.showDate', $request->tanggal)->with('success', 'Acara tambahan berhasil dimasukkan!');
    }

    /**
     * Membuat PDF Rundown dan menyebarkannya ke email
     */
    public function broadcastPdf($tanggal) 
    {
        set_time_limit(300); 

        $agendas = Agenda::where('tanggal', $tanggal)->orderBy('waktu_mulai', 'asc')->get();

        if ($agendas->isEmpty()) {
            return redirect()->route('agenda.showDate', $tanggal)->with('error', 'Tidak ada data jadwal pada tanggal tersebut.');
        }

        try {
            $pdf = Pdf::loadView('agenda.pdf', compact('agendas', 'tanggal'));
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
    
    /**
     * Mengunduh file PDF Rundown secara langsung.
     */
    public function downloadPdf($tanggal)
    {
        $agendas = Agenda::where('tanggal', $tanggal)->orderBy('waktu_mulai', 'asc')->get();

        if ($agendas->isEmpty()) {
            return redirect()->route('agenda.showDate', $tanggal)->with('error', 'Tidak ada data jadwal untuk diunduh.');
        }

        $pdf = Pdf::loadView('agenda.pdf', compact('agendas', 'tanggal'));
        $fileName = 'Rundown_Kegiatan_' . Carbon::parse($tanggal)->format('d_M_Y') . '.pdf';
        
        return $pdf->download($fileName);
    }
}