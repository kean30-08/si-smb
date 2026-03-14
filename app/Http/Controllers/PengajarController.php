<?php

namespace App\Http\Controllers;

use App\Models\Pengajar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PengajarController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    // Cek apakah user adalah admin (berguna jika Anda membatasi tombol Edit/Hapus di view)
    $isAdmin = !\App\Models\Pengajar::where('user_id', auth()->id())->exists();

    // Mengambil data pengajar beserta data akun (user) miliknya
    $pengajars = Pengajar::with('user')
        ->when($search, function ($query, $search) {
            return $query->where('nama_lengkap', 'like', "%{$search}%");
            
            // Catatan: Jika pengajar punya NIP dan Anda ingin bisa dicari juga, 
            // Anda bisa tambahkan orWhere di sini seperti pada siswa.
            // ->orWhere('nip', 'like', "%{$search}%"); 
        })
        ->latest()
        ->paginate(10)
        ->appends(['search' => $search]);

    // LOGIKA AJAX
    if ($request->ajax()) {
        // Mengembalikan hanya HTML tabelnya saja
        return view('pengajar.partials._table', compact('pengajars', 'isAdmin'))->render();
    }

    // Jika diakses biasa, kembalikan halaman penuh
    return view('pengajar.index', compact('pengajars', 'isAdmin'));
}

    public function create()
    {
        return view('pengajar.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'jenis_kelamin' => 'required'
        ], [
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
            'nama_lengkap' => $request->nama_lengkap,
            //'nip' => $request->nip,
            'nomor_hp' => $request->nomor_hp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'jabatan' => $request->jabatan ?? 'Guru Kelas',
        ]);

        return redirect()->route('pengajar.index')->with('success', 'Data pengajar dan akun berhasil dibuat!');
    }

    public function show(Pengajar $pengajar)
    {
        return view('pengajar.show', compact('pengajar'));
    }

    public function edit(Pengajar $pengajar)
    {
        return view('pengajar.edit', compact('pengajar'));
    }

    public function update(Request $request, Pengajar $pengajar)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            // Pengecualian validasi email unik untuk user yang sedang diedit
            'email' => 'required|email|unique:users,email,'.$pengajar->user_id, 
            'jenis_kelamin' => 'required'
        ]);

        // 1. Update Data User (Login)
        $userData = [
            'name' => $request->nama_lengkap,
            'email' => $request->email,
        ];
        // Jika password diisi baru, maka update passwordnya
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        $pengajar->user->update($userData);

        // 2. Update Biodata Pengajar
        $pengajar->update([
            'nama_lengkap' => $request->nama_lengkap,
            //'nip' => $request->nip,
            'nomor_hp' => $request->nomor_hp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'jabatan' => $request->jabatan,
        ]);

        return redirect()->route('pengajar.index')->with('success', 'Data pengajar berhasil diperbarui!');
    }

    public function destroy(Pengajar $pengajar)
    {
        // Hapus akun User (Otomatis biodata Pengajar ikut terhapus karena relasi cascade di database)
        if($pengajar->user) {
            $pengajar->user->delete(); 
        } else {
            $pengajar->delete();
        }
        
        return redirect()->route('pengajar.index')->with('success', 'Data pengajar berhasil dihapus!');
    }
}