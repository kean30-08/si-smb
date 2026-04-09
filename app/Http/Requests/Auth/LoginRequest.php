<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1. Cek apakah user ada di database
        $user = \App\Models\User::where('email', $this->email)->first();

        // 2. Jika user ditemukan, cek apakah dia punya data pengajar dan berstatus tidak aktif
        if ($user && $user->pengajar && $user->pengajar->status === 'tidak aktif') {
            \Illuminate\Support\Facades\RateLimiter::hit($this->throttleKey());
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi Administrator.',
            ]);
        }

        // 3. Lanjutkan proses login bawaan Laravel
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            \Illuminate\Support\Facades\RateLimiter::hit($this->throttleKey());

            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        \Illuminate\Support\Facades\RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
