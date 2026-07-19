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
        }

        /* Bagian Header Kartu */
        .header {
            position: relative;
            text-align: center;
            border-bottom: 2px solid #1e1b4b;
            padding-bottom: 2mm;
            margin-bottom: 3mm;
            min-height: 12mm;
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
            color: #1e1b4b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 900;
        }

        .header p {
            margin: 1px 0 0 0;
            font-size: 8px;
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
            margin-bottom: 1mm; 
            align-items: flex-start;
            font-size: 8.5px; 
            line-height: 1.2;
        }

        /* Lebar label diperkecil sedikit karena titik dua dipisah */
        .details-label {
            width: 19mm; 
            color: #6b7280;
            font-weight: bold;
        }

        /* Class baru khusus untuk titik dua agar sejajar */
        .details-colon {
            width: 2mm;
            color: #111827;
            font-weight: 900;
            margin-right: 1mm;
            text-align: center;
        }

        .details-value {
            color: #111827;
            font-weight: 900;
            flex: 1;
            word-wrap: break-word;
        }

        /* Class khusus untuk membatasi teks alamat maksimal 2 baris */
        .alamat-limit {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="kartu">
        <div class="header">
            <img src="{{ asset('img/logo2_smb.jpg') }}" alt="Logo SMB" class="header-logo">
            <h1>Sekolah Minggu Buddha Vihara Dharma Cattra</h1>
            <p>Jl. Melati No.18, Tabanan - Bali</p>
        </div>

        <div class="content">
            <div class="qr-section">
                {!! QrCode::size(70)->margin(1)->generate('SMB-' . $siswa->id) !!}
            </div>

            <div class="details-section">
                <div class="details-row">
                    <div class="details-label">Nama</div>
                    <div class="details-colon">:</div>
                    <div class="details-value">{{ $siswa->nama_lengkap }}</div>
                </div>
                <div class="details-row">
                    <div class="details-label">TTL</div>
                    <div class="details-colon">:</div>
                    <div class="details-value">{{ $siswa->tempat_lahir }}, {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d-m-Y') }}</div>
                </div>
                <div class="details-row">
                    <div class="details-label">NIK</div>
                    <div class="details-colon">:</div>
                    <div class="details-value">{{ $siswa->nis }}</div>
                </div>
                <div class="details-row">
                    <div class="details-label">Sekolah</div>
                    <div class="details-colon">:</div>
                    <div class="details-value">{{ $siswa->asal_sekolah }}</div>
                </div>
                <div class="details-row">
                    <div class="details-label">Alamat</div>
                    <div class="details-colon">:</div>
                    <div class="details-value alamat-limit">{{ $siswa->alamat }}</div>
                </div>
                <div class="details-row">
                    <div class="details-label">No. Telp (Ortu)</div>
                    <div class="details-colon">:</div>
                    <div class="details-value">{{ $siswa->nomor_hp_orang_tua ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>