<?php

namespace App\Http\Controllers;

use App\Models\Pengajar;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PengajarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status'); // Tangkap filter status
        $isAdmin = auth()->user()->isAdmin();

        $pengajars = Pengajar::with(['user', 'jabatan']) 
            ->when($search, function ($query, $search) {
                return $query->where('nama_lengkap', 'like', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                // Filter status jika ada input dari form
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->appends(['search' => $search, 'status' => $status]);

        if ($request->ajax()) {
            return view('pengajar.partials._table', compact('pengajars', 'isAdmin'))->render();
        }

        return view('pengajar.index', compact('pengajars', 'isAdmin'));
    }

    public function create()
    {
        // PROTEKSI: Hanya Admin yang bisa buka form tambah
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('pengajar.index')->with('error', 'Akses Ditolak! Hanya Admin yang berhak menambahkan pengajar baru.');
        }

        $jabatans = Jabatan::all();
        return view('pengajar.create', compact('jabatans'));
    }

    public function store(Request $request)
    {
        // PROTEKSI: Hanya Admin yang bisa proses simpan data
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('pengajar.index')->with('error', 'Akses Ditolak!');
        }

        $request->validate([
            'nama_lengkap' => 'required',
            'nomor_hp' => 'required|regex:/^[0-9]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'jabatan_id' => 'required|exists:jabatans,id'
        ]);

        return DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->nama_lengkap,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            Pengajar::create([
                'user_id' => $user->id,
                'jabatan_id' => $request->jabatan_id,
                'nama_lengkap' => $request->nama_lengkap,
                'nomor_hp' => $request->nomor_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'status' => 'aktif', // Default aktif
            ]);

            return redirect()->route('pengajar.index')->with('success', 'Data pengajar dan akun berhasil disimpan.');
        });
    }

    public function show(Pengajar $pengajar)
    {
        $pengajar->load('jabatan'); 
        return view('pengajar.show', compact('pengajar'));
    }

    public function edit(Pengajar $pengajar)
    {
        // PROTEKSI: Hanya Admin yang boleh masuk ke form edit siapa pun
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('pengajar.index')->with('error', 'Akses Ditolak! Hanya Admin yang berhak mengubah data pengajar.');
        }

        $jabatans = Jabatan::all();
        return view('pengajar.edit', compact('pengajar', 'jabatans'));
    }

    public function update(Request $request, Pengajar $pengajar)
    {
        // PROTEKSI: Hanya Admin yang boleh memproses update
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('pengajar.index')->with('error', 'Akses Ditolak! Hanya Admin yang berhak mengubah data pengajar.');
        }
        
        $rules = [
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'jabatan_id' => 'required|exists:jabatans,id',
            'alamat' => 'required',
            'nomor_hp' => 'required|regex:/^[0-9]+$/',
            'status' => 'required|in:aktif,tidak aktif',
        ];

        // Cegah pengubahan status Kepala Sekolah Utama (Admin)
        if ($pengajar->user_id == 1 || $pengajar->jabatan_id == 2) { 
             $request->merge(['status' => 'aktif']);
        }

        if ($request->has('ubah_kredensial')) {
            $rules['email'] = 'required|email|unique:users,email,' . $pengajar->user_id;
            if ($request->filled('password')) {
                $rules['password'] = 'min:6|confirmed';
            }
        }

        $request->validate($rules);

        return DB::transaction(function () use ($request, $pengajar) {
            $userData = ['name' => $request->nama_lengkap];

            if ($request->has('ubah_kredensial')) {
                $userData['email'] = $request->email;
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
            }

            $pengajar->user->update($userData);

            $pengajar->update([
                'jabatan_id' => $request->jabatan_id,
                'nama_lengkap' => $request->nama_lengkap,
                'nomor_hp' => $request->nomor_hp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'status' => $request->status,
            ]);

            return redirect()->route('pengajar.index')->with('success', 'Informasi pengajar telah berhasil diperbarui.');
        });
    }

    public function destroy(Pengajar $pengajar)
    {
        // PROTEKSI: Hanya Admin yang boleh menghapus (dan tidak boleh hapus diri sendiri/kepsek)
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('pengajar.index')->with('error', 'Akses Ditolak! Hanya Admin yang berhak menghapus data pengajar.');
        }

        if ($pengajar->user_id == 1 || $pengajar->jabatan_id == 2 || $pengajar->id == auth()->user()->pengajar->id) {
            return redirect()->route('pengajar.index')->with('error', 'Akses Ditolak! Akun Anda sendiri atau Kepala Sekolah tidak dapat dihapus.');
        }

        if($pengajar->user) {
            $pengajar->user->delete(); 
        } else {
            $pengajar->delete();
        }
        
        return redirect()->route('pengajar.index')->with('success', 'Data pengajar berhasil dihapus.');
    }

    public function histori(\App\Models\Pengajar $pengajar)
    {
        // 1. Tarik riwayat absensi yang BENAR-BENAR TERSIMPAN untuk pengajar ini (Sebagai Peta/Kamus)
        $absensisMap = \App\Models\AbsensiPengajar::where('pengajar_id', $pengajar->id)
            ->pluck('status_kehadiran', 'agenda_id')
            ->toArray();

        // 2. Tarik SEMUA jadwal (Agenda) yang terdaftar pada sistem
        $agendas = \App\Models\Agenda::with('tahunAjaran')
            ->whereNotNull('tahun_ajaran_id')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Tanggal pengajar didaftarkan ke sistem (Agar agenda di masa lalu sebelum dia masuk tidak ikut dihitung)
        $tglDaftar = \Carbon\Carbon::parse($pengajar->created_at)->format('Y-m-d');

        // 3. Kelompokkan agenda berdasarkan Tahun Ajaran
        $historisGrouped = $agendas->filter(function($agenda) use ($tglDaftar) {
            return $agenda->tanggal >= $tglDaftar; // Hanya tampilkan jadwal setelah dia bergabung
        })->groupBy(function($agenda) {
            return $agenda->tahunAjaran->tahun_ajaran;
        });

        $historiData = [];

        foreach ($historisGrouped as $ta => $agendaGroup) {
            $hadir = 0; $izin = 0; $sakit = 0; $alpa = 0;
            $detail_absensi = [];

            foreach ($agendaGroup as $agenda) {
                // Cek status dari peta absensi. Jika datanya tidak ada/kosong, otomatis dianggap 'Alpa'
                $status = $absensisMap[$agenda->id] ?? 'alpa';

                // Jika bukan hari libur, masukkan ke dalam perhitungan nilai
                if (!$agenda->is_libur) {
                    if ($status == 'hadir') $hadir++;
                    elseif ($status == 'izin') $izin++;
                    elseif ($status == 'sakit') $sakit++;
                    else $alpa++;
                }

                // Susun format untuk ditampilkan di rincian bulanan
                $bulan = \Carbon\Carbon::parse($agenda->tanggal)->translatedFormat('F Y');
                
                // Bikin object buatan agar bisa dibaca oleh Blade View tanpa harus mengubah file HTML
                $detail_absensi[$bulan][] = (object)[
                    'status_kehadiran' => $status,
                    'agenda' => $agenda
                ];
            }

            $poin = ($hadir * 5) + ($izin * 1) + ($sakit * 1);

            $historiData[$ta] = (object)[
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'poin' => $poin,
                'detail_absensi' => $detail_absensi
            ];
        }

        // Urutkan dari Tahun Ajaran terbaru (Z-A)
        krsort($historiData);

        return view('pengajar.histori', compact('pengajar', 'historiData'));
    }
}