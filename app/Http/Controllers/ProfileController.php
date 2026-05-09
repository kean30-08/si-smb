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

    // Fungsi Mengirim OTP ke Email Lama
    public function sendOtp(Request $request)
    {
        $user = auth()->user();
        $otp = rand(100000, 999999);

        // Simpan OTP di Cache selama 5 menit
        Cache::put('otp_profile_' . $user->id, $otp, now()->addMinutes(5));

        try {
            Mail::to($user->email)->send(new OtpProfileMail($otp));
            
            // Sensor email untuk ditampilkan di alert
            $maskedEmail = preg_replace('/(?<=..)[^@]+(?=@)/', '***', $user->email);
            return response()->json(['success' => true, 'message' => 'Kode OTP berhasil dikirim ke email lama Anda: ' . $maskedEmail]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email OTP. Pastikan koneksi internet aktif.']);
        }
    }

    // Fungsi Validasi OTP
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required']);
        $user = auth()->user();
        
        $cachedOtp = Cache::get('otp_profile_' . $user->id);

        if ($cachedOtp && $cachedOtp == $request->otp) {
            Cache::forget('otp_profile_' . $user->id); // Hapus OTP setelah sukses terpakai
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Kode OTP salah atau sudah kadaluarsa!']);
    }
}
