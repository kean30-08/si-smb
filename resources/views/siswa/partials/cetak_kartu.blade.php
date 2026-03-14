<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identitas Pelajar - {{ $siswa->nama_lengkap }}</title>
    <style>
        /* Memaksa orientasi Landscape A4 */
        @page {
            size: A4 landscape;
            margin: 0; 
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Kunci tepat 1 layar penuh */
            box-sizing: border-box;
            overflow: hidden; /* Cegah elemen tumpah ke halaman 2 */
        }

        .page-container {
            width: 100%;
            height: 100%;
            padding: 35px 50px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            border: 15px solid #1e1b4b; /* Bingkai sedikit dipertipis */
        }

        .header {
            text-align: center;
            border-bottom: 4px solid #1e1b4b;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 34px; /* Diperkecil agar rapi */
            color: #1e1b4b;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 900;
        }
        .header p {
            margin: 8px 0 0 0;
            font-size: 16px;
            color: #4b5563;
            letter-spacing: 1px;
        }

        .content {
            display: flex;
            flex: 1;
            align-items: center;
            justify-content: space-around;
            padding: 0 20px;
        }

        /* Kotak QR Code yang sudah disesuaikan */
        .qr-section {
            text-align: center;
            background-color: #f9fafb;
            padding: 30px;
            border-radius: 15px;
            border: 3px dashed #d1d5db;
        }
        .qr-text {
            margin-top: 15px;
            font-size: 22px;
            font-weight: 900;
            color: #111827;
            letter-spacing: 2px;
        }

        /* Detail Siswa */
        .details-section {
            flex: 0.7;
        }
        .details-row {
            display: flex;
            margin-bottom: 22px;
            border-bottom: 2px dotted #e5e7eb;
            padding-bottom: 12px;
            align-items: center;
        }
        .details-label {
            width: 200px; /* Diperkecil */
            font-size: 18px; /* Diperkecil */
            color: #6b7280;
            font-weight: bold;
        }
        .details-value {
            font-size: 22px; /* Diperkecil agar bisa 1 baris */
            color: #111827;
            font-weight: 900;
            flex: 1;
        }

        .footer {
            text-align: center;
            margin-top: auto;
            padding-top: 15px;
            font-size: 13px;
            color: #9ca3af;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="page-container">
        
        <div class="header">
            <h1>Data Identitas Pelajar</h1>
            <p>Pendidikan Anak Sekolah Minggu Buddha (SMB) Vihara</p>
        </div>
        
        <div class="content">
            <div class="qr-section">
                {!! QrCode::size(220)->margin(2)->generate('SMB-' . $siswa->id) !!}
                
            </div>
            
            <div class="details-section">
                <div class="details-row">
                    <div class="details-label">Nama Lengkap</div>
                    <div class="details-value">: {{ $siswa->nama_lengkap }}</div>
                </div>
                <div class="details-row">
                    <div class="details-label">NIS / ID</div>
                    <div class="details-value">: {{ $siswa->nis }}</div>
                </div>
                <div class="details-row">
                    <div class="details-label">Kelas</div>
                    <div class="details-value">: {{ $siswa->kelas->nama_kelas ?? '-' }}</div>
                </div>
                <div class="details-row">
                    <div class="details-label">Jenis Kelamin</div>
                    <div class="details-value">: {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                </div>
                <div class="details-row">
                    <div class="details-label">Tempat, Tgl Lahir</div>
                    <div class="details-value">: {{ $siswa->tempat_lahir }}, {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d F Y') }}</div>
                </div>
            </div>
        </div>

        <div class="footer">
            Dokumen ini dicetak otomatis dari Sistem Informasi Sekolah Minggu Buddha pada {{ \Carbon\Carbon::now()->format('d F Y H:i') }}
        </div>
        
    </div>

</body>
</html>