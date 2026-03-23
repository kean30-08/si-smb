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
    /**
     * Display a listing of the instructors with eager loaded relations.
     * Supports search filtering and AJAX partial rendering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        // Periksa hak akses pengguna administrator
        $isAdmin = !Pengajar::where('user_id', auth()->id())->exists();

        $pengajars = Pengajar::with(['user', 'jabatan']) 
            ->when($search, function ($query, $search) {
                return $query->where('nama_lengkap', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->appends(['search' => $search]);

        if ($request->ajax()) {
            return view('pengajar.partials._table', compact('pengajars', 'isAdmin'))->render();
        }

        return view('pengajar.index', compact('pengajars', 'isAdmin'));
    }

    /**
     * Tampilkan formulir untuk membuat data pengajar baru beserta pilihan jabatan.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $jabatans = Jabatan::all();
        return view('pengajar.create', compact('jabatans'));
    }

    /**
     * Menyimpan data pengajar baru beserta akun pengguna terkait secara atomik.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
        ], [
            'nomor_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
            'nama_lengkap.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
        ]);

        // Wrap in transaction to ensure atomic data integrity
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
            ]);

            return redirect()->route('pengajar.index')
                ->with('success', 'Data pengajar dan akun berhasil diverifikasi dan disimpan.');
        });
    }

    /**
     * Tampilkan detail informasi pengajar beserta jabatan terkait.
     *
     * @param  \App\Models\Pengajar  $pengajar
     * @return \Illuminate\View\View
     */
    public function show(Pengajar $pengajar)
    {
        $pengajar->load('jabatan'); 
        return view('pengajar.show', compact('pengajar'));
    }

    /**
     * Tampilkan formulir untuk mengedit data pengajar beserta kredensial akun terkait.
     * 
     * @param  \App\Models\Pengajar  $pengajar
     * @return \Illuminate\View\View
     */
    public function edit(Pengajar $pengajar)
    {
        $jabatans = Jabatan::all();
        return view('pengajar.edit', compact('pengajar', 'jabatans'));
    }

    /**
     * Perbarui informasi pengajar dan kredensial akun terkait jika diperlukan.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pengajar  $pengajar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Pengajar $pengajar)
    {
        $rules = [
            'nama_lengkap' => 'required|regex:/^[a-zA-Z\s]+$/',
            'jenis_kelamin' => 'required',
            'jabatan_id' => 'required|exists:jabatans,id',
            'alamat' => 'required',
            'nomor_hp' => 'required|regex:/^[0-9]+$/',
        ];

        $messages = [
            'nomor_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
            'nama_lengkap.regex' => 'Nama lengkap hanya boleh berisi huruf dan spasi.',
        ];

        // Kondisional validasi untuk perubahan kredensial (email dan password)
        if ($request->has('ubah_kredensial')) {
            $rules['email'] = 'required|email|unique:users,email,' . $pengajar->user_id;

            $messages = array_merge($messages, [
                'email.unique' => 'Email sudah terdaftar dalam sistem.',
                'email.email' => 'Format email tidak valid.',
            ]);

            if ($request->filled('password')) {
                $rules['password'] = 'min:6|confirmed';

                $messages = array_merge($messages, [
                    'password.confirmed' => 'Konfirmasi password tidak sesuai.',
                    'password.min' => 'Password minimal harus 6 karakter.',
                ]);
            }
        }

        $request->validate($rules, $messages);

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
            ]);

            return redirect()->route('pengajar.index')
                ->with('success', 'Informasi pengajar telah berhasil diperbarui.');
        });
    }

    /**
     * Remove the instructor and associated user account from storage.
     * 
     * @param  \App\Models\Pengajar  $pengajar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Pengajar $pengajar)
    {
        // Cascade deletion is handled via User model if exists
        if($pengajar->user) {
            $pengajar->user->delete(); 
        } else {
            $pengajar->delete();
        }
        
        return redirect()->route('pengajar.index')
            ->with('success', 'Data entitas berhasil dihapus dari sistem.');
    }
}