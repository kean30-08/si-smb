<!DOCTYPE html>
<html>

<head>
    <title>Kode OTP Verifikasi</title>
</head>

<body style="font-family: Arial, sans-serif; color: #333; padding: 20px;">
    <h2 style="color: #4f46e5;">Kode Verifikasi Pengisian Refleksi</h2>
    <p>Seseorang sedang mencoba mengisi Form Refleksi Sekolah Minggu menggunakan email ini.</p>
    <p>Silakan masukkan kode 6 digit di bawah ini ke dalam form untuk memverifikasi dan mengirim data:</p>

    <div style="background-color: #f3f4f6; padding: 15px; text-align: center; border-radius: 8px; margin: 20px 0;">
        <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #1f2937;">{{ $otp }}</span>
    </div>

    <p style="color: #6b7280; font-size: 14px;"><em>*Kode ini hanya berlaku selama 5 menit. Abaikan email ini jika Anda
            tidak merasa mengisi form.</em></p>
</body>

</html>
