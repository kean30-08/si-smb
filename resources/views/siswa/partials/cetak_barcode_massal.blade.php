<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Barcode Massal - Stiker A4</title>
    <style>
        /* Konfigurasi Kertas A4 Portrait */
        @page {
            size: A4 portrait;
            margin: 1cm;
            /* Margin standar printer 1cm di semua sisi */
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start;
            background-color: #fff;
        }

        /*
           Rumus Layout Baru (5 Kolom x 6 Baris):
           Lebar Kertas (A4) = 21cm - 2cm (Margin Kiri-Kanan) = 19cm.
           19cm dibagi 5 kolom = 3.8cm per kotak.
           
           Tinggi Kertas (A4) = 29.7cm - 2cm (Margin Atas-Bawah) = 27.7cm.
           27.7cm dibagi 6 baris = 4.6cm per kotak.
        */
        .barcode-item {
            width: 3.8cm;
            height: 4.6cm;
            box-sizing: border-box;
            padding: 4px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            page-break-inside: avoid;
            /* GARIS PANDUAN GUNTING */
            border: 1px dashed #9ca3af;
        }

        .qr-wrapper {
            /* QR Code diperbesar sedikit karena ruang lebih lebar */
            width: 3.2cm;
            height: 3.2cm;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .qr-wrapper svg {
            width: 100%;
            height: 100%;
        }

        .nama-siswa {
            /* Font sedikit diperbesar */
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
            margin-top: 5px;
            width: 100%;
            /* Mencegah teks kepanjangan merusak layout (Maks 2 baris) */
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            word-wrap: break-word;
        }

        /* Tombol print disembunyikan saat masuk mode cetak kertas */
        @media print {
            .no-print {
                display: none !important;
            }
        }

        .no-print {
            width: 100%;
            text-align: center;
            padding: 15px;
            background: #f3f4f6;
            margin-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .btn-print {
            background-color: #4f46e5;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-print:hover {
            background-color: #4338ca;
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Print Stiker Barcode Sekarang</button>
        <p style="font-size: 12px; color: #6b7280; margin-top: 8px;">
            Saran Printer: Atur Scale ke <strong>"Default"</strong> atau <strong>"100%"</strong>, dan matikan
            <strong>"Headers and Footers"</strong>.
        </p>
    </div>

    @foreach ($siswas as $siswa)
        <div class="barcode-item">
            <div class="qr-wrapper">
                {{-- QR Code di-generate mengikuti ukuran wrapper --}}
                {!! QrCode::size(120)->margin(0)->generate('SMB-' . $siswa->id) !!}
            </div>

            <div class="nama-siswa">
                {{ strtoupper($siswa->nama_panggilan ?? $siswa->nama_lengkap) }}
            </div>
        </div>
    @endforeach

    <script>
        // Otomatis memunculkan dialog print saat halaman selesai dimuat
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>

</html>
