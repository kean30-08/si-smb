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
            /* Mencegah kartu terpotong di tengah halaman */
            overflow: hidden;
            /* Mencegah konten meluber jika nama terlalu panjang */
        }

        /* Bagian Header Kartu */
        .header {
            position: relative;
            /* Untuk memposisikan logo */
            text-align: center;
            border-bottom: 2px solid #1e1b4b;
            padding-bottom: 2mm;
            margin-bottom: 3mm;
            min-height: 12mm;
            /* Memastikan ruang cukup untuk logo */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .header-logo {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 12mm;
            width: auto;
            object-fit: contain;
        }

        .header h1 {
            margin: 0;
            font-size: 13px;
            /* Diperbesar */
            color: #1e1b4b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 900;
        }

        .header p {
            margin: 1px 0 0 0;
            font-size: 8px;
            /* Diperbesar */
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
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Teks Detail */
        .details-section {
            flex: 1;
        }

        .details-row {
            display: flex;
            margin-bottom: 1.5mm;
            align-items: flex-start;
            /* Berubah agar saat teks wrap, label tetap di atas */
            font-size: 10px;
            /* Diperbesar */
            line-height: 1.2;
        }

        .details-label {
            width: 15mm;
            /* Disesuaikan dengan ukuran font baru */
            color: #6b7280;
            font-weight: bold;
        }

        .details-value {
            color: #111827;
            font-weight: 900;
            flex: 1;
            word-wrap: break-word;
            /* Mengizinkan teks (nama) panjang untuk turun ke baris baru */
        }
    </style>
</head>

<body onload="window.print()">

    <div class="grid-container">
        {{-- Looping semua data siswa --}}
        @foreach ($siswa as $s)
            <div class="kartu">

                <div class="header">
                    {{-- Penambahan Logo SMB --}}
                    <img src="{{ asset('img/logo2_smb.jpg') }}" alt="Logo SMB" class="header-logo">

                    <h1>Kartu Identitas</h1>
                    <p>Pendidikan Anak Sekolah Minggu Buddha</p>
                </div>

                <div class="content">
                    {{-- QR Code diperbesar menjadi 70 --}}
                    <div class="qr-section">
                        {!! QrCode::size(70)->margin(1)->generate('SMB-' . $s->id) !!}
                    </div>

                    <div class="details-section">
                        <div class="details-row">
                            <div class="details-label">Nama</div>
                            {{-- Menghapus Str::limit agar nama tampil penuh --}}
                            <div class="details-value">: {{ $s->nama_lengkap }}</div>
                        </div>
                        <div class="details-row">
                            <div class="details-label">NIS</div>
                            <div class="details-value">: {{ $s->nis }}</div>
                        </div>
                        <div class="details-row">
                            {{-- Menggunakan ?? '-' untuk menghindari error jika kelas null --}}
                            <div class="details-label">Kelas</div>
                            <div class="details-value">: {{ $s->historiAktif->kelas->nama_kelas ?? '-' }}</div>
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
