<?php

namespace App\Http\Controllers;
use App\Models\Siswa;
use App\Mail\BroadcastAgendaMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Agenda;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        // --- FITUR AUTO UPDATE STATUS CERDAS ---
        \App\Models\Agenda::where('status', 'akan datang')
            ->where('tanggal', '=', now()->toDateString())
            ->where('waktu_mulai', '<=', now()->toTimeString())
            ->where('waktu_selesai', '>', now()->toTimeString())
            ->update(['status' => 'sedang berlangsung']);

        \App\Models\Agenda::whereIn('status', ['akan datang', 'sedang berlangsung'])
            ->where(function($query) {
                $query->where('tanggal', '<', now()->toDateString()) 
                      ->orWhere(function($q) {
                          $q->where('tanggal', '=', now()->toDateString())
                            ->where('waktu_selesai', '<=', now()->toTimeString());
                      });
            })->update(['status' => 'selesai']);
        // ----------------------------------------

        $search = $request->input('search');

        // Mengelompokkan data berdasarkan tanggal (GROUP BY)
        $agendasGrouped = \App\Models\Agenda::selectRaw('tanggal, count(id) as total_kegiatan')
            ->when($search, function ($query, $search) {
                // Pencarian sekarang berdasarkan format tanggal (YYYY-MM-DD)
                return $query->where('tanggal', 'like', "%{$search}%");
            })
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('agenda.index', compact('agendasGrouped'));
    }

    // Fungsi Baru untuk menampilkan detail rundown pada tanggal tertentu
    public function showDate($tanggal)
    {
        $agendas = \App\Models\Agenda::where('tanggal', $tanggal)
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        return view('agenda.show', compact('agendas', 'tanggal'));
    }

public function store(Request $request)
{
    // Validasi input berupa Array (Karena form mengirim banyak data sekaligus)
    $request->validate([
        'tanggal' => 'required|date',
        'nama_kegiatan' => 'required|array',
        'nama_kegiatan.*' => 'required|string',
        'waktu_mulai' => 'required|array',
        'waktu_mulai.*' => 'required',
        // Waktu selesai WAJIB diisi agar fitur Auto-Update status bisa mendeteksi kapan acara berakhir
        'waktu_selesai' => 'required|array', 
        'waktu_selesai.*' => 'required',
    ]);

    $tanggal = $request->tanggal;

    // Looping / Ulangi penyimpanan sebanyak jumlah kegiatan yang ditambahkan di form
    foreach ($request->nama_kegiatan as $index => $nama) {
        Agenda::create([
            'tanggal' => $tanggal,
            'nama_kegiatan' => $nama,
            'waktu_mulai' => $request->waktu_mulai[$index],
            'waktu_selesai' => $request->waktu_selesai[$index],
            'deskripsi_rundown' => $request->deskripsi_rundown[$index] ?? null,
            'status' => 'akan datang', // Default selalu akan datang
        ]);
    }

    return redirect()->route('agenda.index')->with('success', 'Rangkaian jadwal berhasil ditambahkan!');
}

    public function create()
    {
        return view('agenda.create');
    }


    public function edit(Agenda $agenda)
    {
        return view('agenda.edit', compact('agenda'));
    }

    public function update(Request $request, Agenda $agenda)
    {
        $request->validate([
            'nama_kegiatan' => 'required',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'status' => 'required'
        ]);

        $agenda->update($request->all());

        return redirect()->route('agenda.index')->with('success', 'Status/Data agenda berhasil diperbarui!');
    }

    public function destroy(Agenda $agenda)
    {
        $agenda->delete();
        return redirect()->route('agenda.index')->with('success', 'Agenda berhasil dihapus!');
    }

    // Tambahkan variabel $tanggal di dalam kurung
    public function broadcastPdf($tanggal) 
    {
        // Beri waktu loading maksimal 5 menit agar tidak putus di tengah jalan
        set_time_limit(300); 

        $agendas = \App\Models\Agenda::where('tanggal', $tanggal)->orderBy('waktu_mulai', 'asc')->get();

        if ($agendas->isEmpty()) {
            return redirect()->route('agenda.showDate', $tanggal)->with('error', 'Tidak ada data jadwal pada tanggal tersebut.');
        }

        // --- SISTEM PELINDUNG ERROR (TRY-CATCH) ---
        try {
            // Proses buat PDF
            $pdf = Pdf::loadView('agenda.pdf', compact('agendas', 'tanggal'));
            $pdfContent = $pdf->output();

            // Cari email HANYA untuk siswa yang masih Aktif
            $emails = \App\Models\Siswa::where('status', 'aktif')
                           ->whereNotNull('email_orang_tua')
                           ->where('email_orang_tua', '!=', '')
                           ->distinct() 
                           ->pluck('email_orang_tua')
                           ->unique();
                           

            if ($emails->isEmpty()) {
                return redirect()->route('agenda.showDate', $tanggal)->with('error', 'Tidak ada data email orang tua yang tersimpan.');
            }

            // Proses kirim
            foreach ($emails as $email) {
                Mail::to($email)->send(new \App\Mail\BroadcastAgendaMail($pdfContent));
                
                sleep(1);
            }

            // Jika semua lancar, kembali bawa pesan sukses
            return redirect()->route('agenda.showDate', $tanggal)
                             ->with('success', 'Jadwal berhasil dikirim ke ' . $emails->count() . ' email orang tua!');

        } catch (\Exception $e) {
            // JIKA TERJADI ERROR APAPUN (Koneksi putus, timeout, dll), tangkap di sini!
            // Alih-alih layar putih 500, kita kembalikan ke halaman dengan Pop-up merah
            return redirect()->route('agenda.showDate', $tanggal)
                             ->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }
    
    // Fungsi untuk mengunduh PDF secara langsung (tanpa kirim email)
    public function downloadPdf($tanggal)
    {
        $agendas = \App\Models\Agenda::where('tanggal', $tanggal)->orderBy('waktu_mulai', 'asc')->get();

        if ($agendas->isEmpty()) {
            return redirect()->route('agenda.showDate', $tanggal)->with('error', 'Tidak ada data jadwal pada tanggal tersebut untuk didownload.');
        }

        // Buat PDF menggunakan template yang sudah ada
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('agenda.pdf', compact('agendas', 'tanggal'));
        
        // Buat nama file yang rapi (Contoh: Rundown_Kegiatan_19_Feb_2026.pdf)
        $fileName = 'Rundown_Kegiatan_' . \Carbon\Carbon::parse($tanggal)->format('d_M_Y') . '.pdf';
        
        // Gunakan perintah ->download() agar browser langsung mengunduhnya
        return $pdf->download($fileName);
    }

    // Menampilkan form tambah acara untuk tanggal spesifik
    public function createDetail($tanggal)
    {
        return view('agenda.create_detail', compact('tanggal'));
    }

    // Menyimpan acara tambahan tersebut
    public function storeDetail(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_kegiatan' => 'required|string',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
        ]);

        Agenda::create([
            'tanggal' => $request->tanggal,
            'nama_kegiatan' => $request->nama_kegiatan,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'deskripsi_rundown' => $request->deskripsi_rundown,
            'status' => 'akan datang', 
        ]);

        // Setelah simpan, kembalikan ke halaman detail tanggal tersebut
        return redirect()->route('agenda.showDate', $request->tanggal)->with('success', 'Acara tambahan berhasil dimasukkan ke jadwal!');
    }
}