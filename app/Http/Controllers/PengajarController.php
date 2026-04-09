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
        $jabatans = Jabatan::all();
        return view('pengajar.create', compact('jabatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|regex:/^[a-zA-Z\s]+$/',
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
        $jabatans = Jabatan::all();
        return view('pengajar.edit', compact('pengajar', 'jabatans'));
    }

    public function update(Request $request, Pengajar $pengajar)
    {
        $rules = [
            'nama_lengkap' => 'required|regex:/^[a-zA-Z\s]+$/',
            'jenis_kelamin' => 'required',
            'jabatan_id' => 'required|exists:jabatans,id',
            'alamat' => 'required',
            'nomor_hp' => 'required|regex:/^[0-9]+$/',
            'status' => 'required|in:aktif,tidak aktif', // Validasi status baru
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
            
            // Jika status tidak aktif, hancurkan akses login user dengan mengubah password acak (Opsional untuk keamanan ekstra)
            if ($request->status == 'tidak aktif') {
                 // Anda juga bisa menambahkan logika suspend di LoginController, 
                 // tapi cara termudah memutus akses adalah mereset password / email
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
        // PROTEKSI: Kepala Sekolah (Jabatan ID 2 atau User ID 1) TIDAK BOLEH dihapus
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
}