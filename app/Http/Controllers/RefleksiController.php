<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RefleksiSiswa;
use App\Models\Agenda;
use App\Models\Kelas;
use App\Models\Siswa; // Pastikan Model Siswa dipanggil
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpRefleksiMail;

class RefleksiController extends Controller
{
    public function create($tanggal)
    {
        $agendas = Agenda::where('tanggal', $tanggal)->orderBy('waktu_mulai', 'asc')->get();

        if ($agendas->isEmpty()) {
            abort(404, 'Tidak ada jadwal kegiatan pada tanggal ini.');
        }

        $waktuMulaiPertama = $agendas->first()->waktu_mulai;
        $waktuBuka = Carbon::parse($tanggal . ' ' . $waktuMulaiPertama);
        $waktuTutup = $waktuBuka->copy()->addHours(24);
        $sekarang = Carbon::now();

        $statusForm = 'buka';
        if ($sekarang->lt($waktuBuka)) {
            $statusForm = 'belum_buka';
        } elseif ($sekarang->gt($waktuTutup)) {
            $statusForm = 'sudah_tutup';
        }

        $kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
        
        // AMBIL DATA SISWA AKTIF UNTUK DROPDOWN
        $siswas = Siswa::with('nilaiKehadiranAktif')->where('status', 'aktif')->orderBy('nama_lengkap', 'asc')->get();

        return view('refleksi.create', compact('tanggal', 'statusForm', 'waktuBuka', 'waktuTutup', 'kelas', 'siswas'));
    }

    /**
     * Endpoint AJAX untuk mengirim kode OTP (BERDASARKAN SISWA ID DARI DATABASE)
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id'
        ]);

        // Tarik data siswa dari database (Murni kebenaran database, tidak bisa dimanipulasi frontend)
        $siswa = Siswa::find($request->siswa_id);

        if (empty($siswa->email_orang_tua)) {
            return response()->json(['success' => false, 'message' => 'Email orang tua kamu belum terdaftar di sistem. Silakan lapor ke Guru / Admin!']);
        }

        $email = $siswa->email_orang_tua;
        $otp = rand(100000, 999999);

        // Simpan OTP di Cache berdasarkan ID Siswa
        Cache::put('otp_refleksi_siswa_' . $siswa->id, $otp, now()->addMinutes(5));

        try {
            Mail::to($email)->send(new OtpRefleksiMail($otp));
            
            // Sensor email untuk notifikasi agar aman (contoh: budi***@gmail.com)
            $maskedEmail = preg_replace('/(?<=..)[^@]+(?=@)/', '***', $email);
            
            return response()->json(['success' => true, 'message' => 'Kode OTP berhasil dikirim ke email: ' . $maskedEmail]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email. Pastikan koneksi internet server benar.']);
        }
    }

    public function store(Request $request, $tanggal)
    {
        $agendas = Agenda::where('tanggal', $tanggal)->orderBy('waktu_mulai', 'asc')->get();
        $waktuBuka = Carbon::parse($tanggal . ' ' . $agendas->first()->waktu_mulai);
        $waktuTutup = $waktuBuka->copy()->addHours(24);

        if (!Carbon::now()->between($waktuBuka, $waktuTutup)) {
            return back()->withErrors(['Waktu pengisian form refleksi untuk kegiatan ini telah habis.']);
        }

        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'otp' => 'required|numeric',
            'rangkuman' => 'required|string',
            'bagian_disukai' => 'required|string',
            'bagian_kurang_disukai' => 'required|string',
        ], [
            'siswa_id.required' => 'Silakan pilih Nama kamu terlebih dahulu.',
            'otp.required' => 'Kode OTP wajib diisi untuk verifikasi.'
        ]);

        $siswa = Siswa::with('nilaiKehadiranAktif')->find($request->siswa_id);

        // VERIFIKASI OTP BERDASARKAN ID SISWA
        $cachedOtp = Cache::get('otp_refleksi_siswa_' . $siswa->id);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return back()->withInput()->withErrors(['otp' => 'Kode OTP salah atau sudah kedaluwarsa (berlaku 5 menit). Silakan minta kode baru.']);
        }

        // Hapus OTP dari cache agar tidak bisa dipakai 2x
        Cache::forget('otp_refleksi_siswa_' . $siswa->id);

        // SIMPAN KE DATABASE (Tarik data identitas langsung dari relasi DB, bukan dari form input)
        RefleksiSiswa::create([
            'tanggal' => $tanggal,
            'nama_siswa' => $siswa->nama_lengkap,
            'nis' => $siswa->nis,
            'kelas_id' => $siswa->nilaiKehadiranAktif->kelas_id ?? null,
            'nama_orang_tua' => $siswa->nama_orang_tua ?? '-',
            'email_orang_tua' => $siswa->email_orang_tua,
            'rangkuman' => $request->rangkuman,
            'bagian_disukai' => $request->bagian_disukai,
            'bagian_kurang_disukai' => $request->bagian_kurang_disukai,
        ]);

        return redirect()->back()->with('success', 'Terima kasih! Refleksi diri kamu telah terverifikasi dan berhasil dikirim.');
    }

    public function index($tanggal)
    {
        $refleksis = RefleksiSiswa::with('kelas')->where('tanggal', $tanggal)->latest()->get();
        return view('refleksi.index', compact('refleksis', 'tanggal'));
    }

    public function show($id)
    {
        $refleksi = RefleksiSiswa::with('kelas')->findOrFail($id);
        return view('refleksi.show', compact('refleksi'));
    }
}