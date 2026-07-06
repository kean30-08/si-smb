<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Massal Kartu Pelajar</title>
    <style>
        /* Mengatur kertas A4 Portrait */
        @page {
            size: A4 portrait;
            margin: 10mm;
            /* Margin kertas */
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Container utama menggunakan Grid */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            /* 2 Kolom */
            gap: 5mm;
            /* Jarak antar kartu */
            justify-items: center;
        }

        /* Ukuran standar ID Card (90mm x 55mm) */
        .kartu {
            width: 90mm;
            height: 55mm;
            border: 2px solid #1e1b4b;
            border-radius: 8px;
            padding: 4mm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            page-break-inside: avoid;
            /* PENTING: Mencegah kartu terpotong di tengah halaman */
        }

        /* Bagian Header Kartu */
        .header {
            text-align: center;
            border-bottom: 2px solid #1e1b4b;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }

        .header h1 {
            margin: 0;
            font-size: 11px;
            /* Diperkecil untuk ID card */
            color: #1e1b4b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 900;
        }

        .header p {
            margin: 1px 0 0 0;
            font-size: 7px;
            color: #4b5563;
        }

        /* Bagian Isi Kartu */
        .content {
            display: flex;
            flex-direction: row;
            gap: 4mm;
            align-items: center;
            flex: 1;
        }

        /* QR Code */
        .qr-section {
            background-color: #f9fafb;
            padding: 2mm;
            border-radius: 6px;
            border: 1px dashed #d1d5db;
        }

        /* Teks Detail */
        .details-section {
            flex: 1;
        }

        .details-row {
            display: flex;
            margin-bottom: 1.5mm;
            align-items: center;
            font-size: 8px;
            /* Font kecil karena ukuran ID Card */
        }

        .details-label {
            width: 20mm;
            color: #6b7280;
            font-weight: bold;
        }

        .details-value {
            color: #111827;
            font-weight: 900;
            flex: 1;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="grid-container">
        {{-- Looping semua data siswa --}}
        @foreach ($siswa as $s)
            <div class="kartu">

                <div class="header">
                    <h1>Kartu Identitas</h1>
                    <p>Pendidikan Anak Sekolah Minggu Buddha</p>
                </div>

                <div class="content">
                    {{-- QR Code diubah ukurannya menjadi 55 --}}
                    <div class="qr-section">
                        {!! QrCode::size(55)->margin(1)->generate('SMB-' . $s->id) !!}
                    </div>

                    <div class="details-section">
                        <div class="details-row">
                            <div class="details-label">Nama</div>
                            <div class="details-value">: {{ Str::limit($s->nama_lengkap, 20) }}</div>
                        </div>
                        <div class="details-row">
                            <div class="details-label">NIS</div>
                            <div class="details-value">: {{ $s->nis }}</div>
                        </div>
                        <div class="details-row">
                            {{-- Menggunakan ?? '-' untuk menghindari error jika kelas null --}}
                            <div class="details-label">Kelas</div>
                            <div class="details-value">: {{ $s->nilaiKehadiranAktif->kelas->nama_kelas ?? '-' }}</div>
                        </div>
                        <div class="details-row">
                            <div class="details-label">L/P</div>
                            <div class="details-value">: {{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @endforeach
    </div>

</body>

</html>
