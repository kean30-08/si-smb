<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Agenda;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    // Menampilkan halaman utama Laporan
    public function index()
    {
        $kelas = Kelas::all();
        return view('laporan.index', compact('kelas'));
    }

    // Fungsi Cetak Laporan Data Siswa
    public function cetakSiswa(Request $request)
    {
        $kelas_id = $request->kelas_id;
        
        // Jika pilih "Semua Kelas"
        if ($kelas_id == 'semua') {
            $siswas = Siswa::with('kelas')->orderBy('kelas_id')->orderBy('nama_lengkap')->get();
            $nama_kelas = 'Semua Kelas';
        } else {
            // Jika pilih kelas spesifik
            $siswas = Siswa::with('kelas')->where('kelas_id', $kelas_id)->orderBy('nama_lengkap')->get();
            $nama_kelas = Kelas::findOrFail($kelas_id)->nama_kelas;
        }

        if ($siswas->isEmpty()) {
            return back()->with('error', 'Tidak ada data siswa untuk kelas yang dipilih.');
        }

        $pdf = Pdf::loadView('laporan.pdf_siswa', compact('siswas', 'nama_kelas'));
        // Menggunakan stream() agar file terbuka di tab baru browser, bukan langsung terdownload
        return $pdf->stream('Laporan_Siswa_' . date('Ymd') . '.pdf'); 
    }

    // Fungsi Cetak Laporan Agenda
    public function cetakAgenda(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $mulai = $request->tanggal_mulai;
        $selesai = $request->tanggal_selesai;

        $agendas = Agenda::whereBetween('tanggal', [$mulai, $selesai])
                         ->orderBy('tanggal', 'asc')
                         ->orderBy('waktu_mulai', 'asc')
                         ->get();

        if ($agendas->isEmpty()) {
            return back()->with('error', 'Tidak ada jadwal kegiatan pada rentang tanggal tersebut.');
        }

        $pdf = Pdf::loadView('laporan.pdf_agenda', compact('agendas', 'mulai', 'selesai'));
        return $pdf->stream('Laporan_Agenda_' . date('Ymd') . '.pdf');
    }
}