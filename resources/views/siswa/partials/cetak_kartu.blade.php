<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identitas Pelajar - {{ $siswa->nama_lengkap }}</title>
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
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 20mm;
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
            /* Jarak diperbesar sedikit */
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
            /* Posisi persis di tengah vertikal header */
            height: 12mm;
            width: auto;
            object-fit: contain;
        }

        .header h1 {
            margin: 0;
            font-size: 13px;
            /* Diperbesar dari 11px */
            color: #1e1b4b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 900;
        }

        .header p {
            margin: 1px 0 0 0;
            font-size: 8px;
            /* Diperbesar dari 7px */
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
            /* Berubah agar saat teks nama turun ke bawah (wrap), posisi label tetap di atas */
            font-size: 10px;
            /* Diperbesar dari 8px */
            line-height: 1.2;
        }

        .details-label {
            width: 15mm;
            /* Disesuaikan agar pas dengan font baru */
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

    <div class="kartu">
        <div class="header">
            {{-- Penambahan Logo SMB --}}
            <img src="{{ asset('img/logo2_smb.jpg') }}" alt="Logo SMB" class="header-logo">

            <h1>Kartu Identitas</h1>
            <p>Pendidikan Anak Sekolah Minggu Buddha</p>
        </div>

        <div class="content">
            {{-- QR Code diperbesar dari 55 menjadi 70 --}}
            <div class="qr-section">
                {!! QrCode::size(70)->margin(1)->generate('SMB-' . $siswa->id) !!}
            </div>

            <div class="details-section">
                <div class="details-row">
                    <div class="details-label">Nama</div>
                    {{-- Menghapus Str::limit agar nama tampil utuh (akan otomatis ke baris baru jika kepanjangan) --}}
                    <div class="details-value">: {{ $siswa->nama_lengkap }}</div>
                </div>
                <div class="details-row">
                    <div class="details-label">NIS</div>
                    <div class="details-value">: {{ $siswa->nis }}</div>
                </div>
                <div class="details-row">
                    <div class="details-label">Kelas</div>
                    <div class="details-value">: {{ $siswa->historiAktif->kelas->nama_kelas ?? '-' }}</div>
                </div>
                <div class="details-row">
                    <div class="details-label">L/P</div>
                    <div class="details-value">: {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
