<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpProfileMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Kode Verifikasi Keamanan Profil')
                    ->html("
                        <h3>Peringatan Keamanan!</h3>
                        <p>Seseorang sedang mencoba mengubah Email atau Password pada akun Anda.</p>
                        <p>Jika ini adalah Anda, silakan masukkan 6 digit kode OTP berikut:</p>
                        <h2 style='color: blue; letter-spacing: 5px;'>{$this->otp}</h2>
                        <p><i>Kode ini akan kadaluarsa dalam 5 menit. JANGAN berikan kode ini kepada siapapun!</i></p>
                    ");
    }
}