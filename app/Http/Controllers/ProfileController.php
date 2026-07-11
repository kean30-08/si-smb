<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpProfileMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        $pengajar = \App\Models\Pengajar::where('user_id', $request->user()->id)->first();
        
        if ($pengajar) {
            $pengajar->update([
                // TAMBAHKAN SINKRONISASI NAMA LENGKAP
                'nama_lengkap' => $request->input('name'), 
                'nomor_hp' => $request->input('nomor_hp'),
                'jenis_kelamin' => $request->input('jenis_kelamin'),
                'alamat' => $request->input('alamat'),
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Fungsi Mengirim OTP untuk Verifikasi Keamanan Profil
     * Mendukung 2 tahap (Target 'old' dan 'new')
     */
    public function sendOtp(Request $request)
    {
        $user = auth()->user();
        $target = $request->input('target', 'old'); // default ke 'old'
        $otp = rand(100000, 999999);

        // Tentukan email tujuan dan nama kunci cache berdasarkan target
        if ($target === 'new') {
            $request->validate([
                'new_email' => 'required|email|unique:users,email',
            ]);
            $emailToSend = $request->new_email;
            $cacheKey = 'otp_profile_new_' . $user->id;
        } else {
            $emailToSend = $user->email;
            $cacheKey = 'otp_profile_old_' . $user->id;
        }

        // Simpan OTP di Cache selama 5 menit
        Cache::put($cacheKey, $otp, now()->addMinutes(5));

        // Sensor email untuk ditampilkan di respons
        $maskedEmail = preg_replace('/(?<=..)[^@]+(?=@)/', '***', $emailToSend);

        try {
            // Kirim OTP
            Mail::to($emailToSend)->send(new OtpProfileMail($otp));
            
            return response()->json([
                'success' => true,
                'message' => 'Kode OTP berhasil dikirim ke: ' . $maskedEmail
            ]);
        } catch (\Exception $e) {
            Cache::forget($cacheKey);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email OTP. Pastikan koneksi internet aktif atau coba lagi nanti.'
            ]);
        }
    }

    /**
     * Fungsi Validasi OTP
     * Mendukung pemisahan verifikasi antara email lama dan baru
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required',
            'target' => 'required'
        ]);

        $user = auth()->user();
        $target = $request->target;
        $cacheKey = $target === 'new' ? 'otp_profile_new_' . $user->id : 'otp_profile_old_' . $user->id;
        
        $cachedOtp = Cache::get($cacheKey);

        if ($cachedOtp && $cachedOtp == $request->otp) {
            Cache::forget($cacheKey); // Hapus OTP setelah sukses terpakai
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Kode OTP salah atau sudah kadaluarsa!']);
    }
}