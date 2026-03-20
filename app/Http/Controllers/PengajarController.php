<?php

namespace App\Http\Controllers;

use App\Models\Pengajar;
use App\Models\User;
use App\Models\Jabatan; // Jangan lupa import model Jabatan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PengajarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        // Cek apakah user adalah admin
        $isAdmin = !\App\Models\Pengajar::where('user_id', auth()->id())->exists();

        // Mengambil data pengajar beserta data akun (user) dan jabatannya
        // Tambahkan 'jabatan' di dalam method with()
        $pengajars = Pengajar::with(['user', 'jabatan']) 
            ->when($search, function ($query, $search) {
                return $query->where('nama_lengkap', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->appends(['search' => $search]);

        // LOGIKA AJAX
        if ($request->ajax()) {
            return view('pengajar.partials._table', compact('pengajars', 'isAdmin'))->render();
        }

        // Jika diakses biasa, kembalikan halaman penuh
        return view('pengajar.index', compact('pengajars', 'isAdmin'));
    }

    public function create()
    {
        // Ambil semua data master jabatan untuk dropdown
        $jabatans = Jabatan::all();
        
        return view('pengajar.create', compact('jabatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|regex:/^[a-zA-Z\s]+$/',
            'nomor_hp' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'jabatan_id' => 'required|exists:jabatans,id'
        ], [
            'nama_lengkap.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'password.confirmed' => 'Password dan Konfirmasi Password tidak cocok!',
            'email.unique' => 'Email ini sudah terdaftar, silakan gunakan email lain.',
            'password.min' => 'Password harus minimal 6 karakter.'
        ]);

        // 1. Buat Akun User untuk Login
        $user = User::create([
            'name' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 2. Buat Data Biodata Pengajar
        Pengajar::create([
            'user_id' => $user->id,
            'jabatan_id' => $request->jabatan_id, // Simpan ID Jabatan ke tabel pengajars
            'nama_lengkap' => $request->nama_lengkap,
            'nomor_hp' => $request->nomor_hp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('pengajar.index')->with('success', 'Data pengajar dan akun berhasil dibuat!');
    }

    public function show(Pengajar $pengajar)
    {
        // Pastikan relasi jabatan di-load saat melihat detail
        $pengajar->load('jabatan'); 
        
        return view('pengajar.show', compact('pengajar'));
    }

    public function edit(Pengajar $pengajar)
    {
        // Ambil semua data master jabatan untuk dropdown
        $jabatans = Jabatan::all();
        
        return view('pengajar.edit', compact('pengajar', 'jabatans'));
    }

    public function update(Request $request, Pengajar $pengajar)
    {
        // 1. Validasi Dasar (Selalu Dijalankan)
        $rules = [
            'nama_lengkap' => 'required|regex:/^[a-zA-Z\s]+$/',
            'jenis_kelamin' => 'required',
            'jabatan_id' => 'required|exists:jabatans,id',
            'alamat' => 'required',
            'nomor_hp' => 'required|regex:/^[0-9]+$/',
        ];

        $messages = [
            'nama_lengkap.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'nomor_hp.required' => 'Nomor HP wajib diisi.',
            'nomor_hp.regex' => 'Nomor HP hanya boleh berisi angka (tanpa spasi atau strip).',
        ];

        // 2. Validasi Tambahan JIKA Admin Mencentang Kotak Edit Akun
        if ($request->has('ubah_kredensial')) {
            $rules['email'] = 'required|email|unique:users,email,' . $pengajar->user_id;
            $messages['email.unique'] = 'Email ini sudah terdaftar, silakan gunakan email lain.';
            
            // Password hanya divalidasi jika diketik (tidak kosong)
            if ($request->filled('password')) {
                $rules['password'] = 'min:6|confirmed';
                $messages['password.min'] = 'Password harus minimal 6 karakter.';
                $messages['password.confirmed'] = 'Password dan Konfirmasi Password tidak cocok!';
            }
        }

        $request->validate($rules, $messages);

        // 3. Update Data User (Login)
        $userData = [
            'name' => $request->nama_lengkap,
        ];

        // Jika centang diaktifkan, baru kita update email & password
        if ($request->has('ubah_kredensial')) {
            $userData['email'] = $request->email;
            
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
        }
        $pengajar->user->update($userData);

        // 4. Update Biodata Pengajar
        $pengajar->update([
            'jabatan_id' => $request->jabatan_id,
            'nama_lengkap' => $request->nama_lengkap,
            'nomor_hp' => $request->nomor_hp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('pengajar.index')->with('success', 'Data pengajar berhasil diperbarui!');
    }

    public function destroy(Pengajar $pengajar)
    {
        // Hapus akun User (Otomatis biodata Pengajar ikut terhapus karena relasi cascade)
        if($pengajar->user) {
            $pengajar->user->delete(); 
        } else {
            $pengajar->delete();
        }
        
        return redirect()->route('pengajar.index')->with('success', 'Data pengajar berhasil dihapus!');
    }
}