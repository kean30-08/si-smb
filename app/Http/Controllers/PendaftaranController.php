<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\HistoriSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    // ===========================================
    // SISI PUBLIK (ORANG TUA)
    // ===========================================
    public function create()
    {
        $kelas = Kelas::all();
        return view('pendaftaran.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nama_panggilan' => 'required|string|max:255',
            'nis' => 'nullable|string|unique:siswas,nis',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'asal_sekolah' => 'required|string',
            'nomor_hp_siswa' => 'nullable|string',
            'nama_orang_tua' => 'required|string',
            'email_orang_tua' => 'nullable|email',
            'nomor_hp_orang_tua' => 'required|string',
            'alamat' => 'required|string',
        ]);

        // ==========================================
        // VALIDASI ANTI SPAM / DATA GANDA
        // ==========================================
        
        // Ubah input nama menjadi huruf kecil semua dan hapus spasi berlebih
        $namaInput = strtolower(trim($request->nama_lengkap));

        // 1. Cek apakah nama & tanggal lahir yang sama sedang "menunggu" antrean
        $sedangMenunggu = Pendaftaran::whereRaw('LOWER(nama_lengkap) = ?', [$namaInput])
            ->where('tanggal_lahir', $request->tanggal_lahir)
            ->where('status', 'menunggu')
            ->exists();

        if ($sedangMenunggu) {
            return back()->withInput()->withErrors([
                'nama_lengkap' => 'Pendaftaran ditolak: Anak dengan nama dan tanggal lahir ini sudah mendaftar dan sedang dalam proses konfirmasi Admin.'
            ]);
        }

        // 2. Cek apakah anak tersebut memang sudah resmi menjadi siswa aktif di sistem
        $sudahJadiSiswa = Siswa::whereRaw('LOWER(nama_lengkap) = ?', [$namaInput])
            ->where('tanggal_lahir', $request->tanggal_lahir)
            ->exists();

        if ($sudahJadiSiswa) {
            return back()->withInput()->withErrors([
                'nama_lengkap' => 'Pendaftaran ditolak: Anak dengan nama dan tanggal lahir ini sudah terdaftar secara resmi sebagai siswa SMB.'
            ]);
        }
        Pendaftaran::create($data);
        return back()->with('success', 'Pendaftaran berhasil dikirim! Silakan tunggu konfirmasi dari pihak sekolah minggu.');
    }

    // ===========================================
    // SISI ADMIN / PENGAJAR
    // ===========================================
    public function index()
    {
        $pendaftarans = Pendaftaran::where('status', 'menunggu')->latest()->get();
        $isAdmin = auth()->user()->isAdmin();
        return view('pendaftaran.index', compact('pendaftarans', 'isAdmin'));
    }

    public function terima($id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);

        DB::transaction(function () use ($pendaftaran) {
            // 1. Pindahkan ke Tabel Master Siswa
            $siswa = Siswa::create([
                'nama_lengkap' => $pendaftaran->nama_lengkap,
                'nama_panggilan' => $pendaftaran->nama_panggilan,
                // Jika NIS kosong, buatkan NIS sementara, admin bisa edit nanti
                'nis' => $pendaftaran->nis ?: 'SMB-' . strtoupper(substr(uniqid(), -6)),
                'jenis_kelamin' => $pendaftaran->jenis_kelamin,
                'tempat_lahir' => $pendaftaran->tempat_lahir,
                'tanggal_lahir' => $pendaftaran->tanggal_lahir,
                'asal_sekolah' => $pendaftaran->asal_sekolah,
                'nomor_hp_siswa' => $pendaftaran->nomor_hp_siswa,
                'nama_orang_tua' => $pendaftaran->nama_orang_tua,
                'email_orang_tua' => $pendaftaran->email_orang_tua,
                'nomor_hp_orang_tua' => $pendaftaran->nomor_hp_orang_tua,
                'alamat' => $pendaftaran->alamat,
                'status' => 'aktif'
            ]);

            // 2. Buat Histori Kelas Langsung
            $tahunAktif = TahunAjaran::where('status', 'aktif')->first();
            if ($tahunAktif) {
                HistoriSiswa::create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $pendaftaran->kelas_id,
                    'tahun_ajaran_id' => $tahunAktif->id,
                ]);
            }

            // 3. Ubah status pendaftaran
            $pendaftaran->update(['status' => 'diterima']);
        });

        return back()->with('success', 'Siswa berhasil diterima dan telah ditambahkan ke data master!');
    }

    public function tolak($id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);
        $pendaftaran->update(['status' => 'ditolak']);
        return back()->with('success', 'Pendaftaran ditolak.');
    }

    public function show($id)
    {
        $pendaftaran = Pendaftaran::with('kelas')->findOrFail($id);
        return view('pendaftaran.show', compact('pendaftaran'));
    }
}