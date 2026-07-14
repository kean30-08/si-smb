<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Barcode - {{ $siswa->nama_panggilan ?? $siswa->nama_lengkap }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1cm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .barcode-item {
            width: 3.8cm;
            height: 4.6cm;
            box-sizing: border-box;
            padding: 4px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: 1px dashed #9ca3af;
            margin-top: 30px;
            /* Jarak antara tombol dan kertas virtual */
        }

        .qr-wrapper {
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
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
            margin-top: 5px;
            width: 100%;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            word-wrap: break-word;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .barcode-item {
                margin-top: 0;
            }

            body {
                align-items: flex-start;
            }

            /* Saat diprint, posisi otomatis di pojok kiri atas kertas */
        }

        .no-print {
            width: 100%;
            text-align: center;
            padding: 15px;
            background: #f3f4f6;
            border-bottom: 1px solid #e5e7eb;
        }

        .btn-print {
            background-color: #1f2937;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-print:hover {
            background-color: #374151;
        }
    </style>
</head>

<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Print Stiker Barcode Ini</button>
        <p style="font-size: 12px; color: #6b7280; margin-top: 8px;">
            Pastikan pengaturan Scale diatur ke <strong>"Default"</strong> agar ukuran pas.
        </p>
    </div>

    <div class="barcode-item">
        <div class="qr-wrapper">
            {!! QrCode::size(120)->margin(0)->generate('SMB-' . $siswa->id) !!}
        </div>
        <div class="nama-siswa">
            {{ strtoupper($siswa->nama_panggilan ?? $siswa->nama_lengkap) }}
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>

</html>
