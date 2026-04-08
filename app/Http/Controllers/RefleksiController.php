<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RefleksiSiswa;
use App\Models\Agenda;
use Carbon\Carbon;

class RefleksiController extends Controller
{
    /**
     * Menampilkan form refleksi publik dengan timer 24 Jam
     */
    public function create($tanggal)
    {
        $agendas = Agenda::where('tanggal', $tanggal)->orderBy('waktu_mulai', 'asc')->get();

        if ($agendas->isEmpty()) {
            abort(404, 'Tidak ada jadwal kegiatan pada tanggal ini.');
        }

        // Tentukan batas waktu (24 jam dari agenda paling pagi)
        $waktuMulaiPertama = $agendas->first()->waktu_mulai;
        $waktuBuka = Carbon::parse($tanggal . ' ' . $waktuMulaiPertama);
        $waktuTutup = $waktuBuka->copy()->addHours(24);
        $sekarang = Carbon::now();

        // Cek status ketersediaan form
        $statusForm = 'buka';
        if ($sekarang->lt($waktuBuka)) {
            $statusForm = 'belum_buka';
        } elseif ($sekarang->gt($waktuTutup)) {
            $statusForm = 'sudah_tutup';
        }

        return view('refleksi.create', compact('tanggal', 'waktuBuka', 'waktuTutup', 'statusForm', 'agendas'));
    }

    public function store(Request $request, $tanggal)
    {
        // Keamanan ganda: Tolak jika disubmit dari postman setelah waktu habis
        $agendas = Agenda::where('tanggal', $tanggal)->orderBy('waktu_mulai', 'asc')->get();
        $waktuBuka = Carbon::parse($tanggal . ' ' . $agendas->first()->waktu_mulai);
        $waktuTutup = $waktuBuka->copy()->addHours(24);

        if (!Carbon::now()->between($waktuBuka, $waktuTutup)) {
            return back()->withErrors(['Waktu pengisian form refleksi untuk kegiatan ini telah habis.']);
        }

        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'nis' => 'required|string|max:255',
            'nama_orang_tua' => 'required|string|max:255',
            'email_orang_tua' => 'nullable|email|max:255',
            'rangkuman' => 'required|string',
            'bagian_disukai' => 'required|string',
            'bagian_kurang_disukai' => 'required|string',
        ]);

        $data = $request->all();
        $data['tanggal'] = $tanggal;

        RefleksiSiswa::create($data);

        return redirect()->back()->with('success', 'Terima kasih! Refleksi diri kamu berhasil dikirim.');
    }

    /**
     * UNTUK ADMIN: Menampilkan daftar refleksi yang sudah dikumpulkan siswa
     */
    public function index($tanggal)
    {
        $refleksis = RefleksiSiswa::where('tanggal', $tanggal)->latest()->get();
        return view('refleksi.index', compact('refleksis', 'tanggal'));
    }

    /**
     * Menampilkan detail satu refleksi secara penuh
     */
    public function show($id)
    {
        $refleksi = RefleksiSiswa::findOrFail($id);
        return view('refleksi.show', compact('refleksi'));
    }
}