<!DOCTYPE html>
<html>

<head>
    <title>Rundown Kegiatan Vihara</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            color: #333;
        }

        .header p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 40px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Style Tanda Tangan */
        .signature-box {
            width: 100%;
            page-break-inside: avoid;
            margin-top: 20px;
        }

        .signature-wrapper {
            float: right;
            width: 250px;
            text-align: center;
        }

        .signature-date {
            margin-bottom: 5px;
            font-size: 12px;
        }

        .signature-title {
            margin-bottom: 60px;
            font-weight: bold;
            font-size: 12px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 13px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .footer-note {
            margin-top: 50px;
            font-size: 12px;
            text-align: center;
            color: #555;
            clear: both;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Rundown Kegiatan Sekolah Minggu & Puja Bakti</h2>
        <p>Vihara Dharma Cakra Tabanan</p>
        <p><strong>Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</strong></p>
        <hr>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Waktu</th>
                <th style="width: 35%;">Nama Kegiatan</th>
                <th style="width: 45%;">Catatan Khusus</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($agendas as $agenda)
                <tr>
                    <td>
                        {{ \Carbon\Carbon::parse($agenda->waktu_mulai)->format('H:i') }} -
                        {{ $agenda->waktu_selesai ? \Carbon\Carbon::parse($agenda->waktu_selesai)->format('H:i') : 'Selesai' }}
                    </td>
                    <td><strong>{{ $agenda->nama_kegiatan }}</strong></td>
                    <td>{{ $agenda->deskripsi_rundown ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- KOTAK TANDA TANGAN --}}
    <div class="signature-box clearfix">
        <div class="signature-wrapper">
            <div class="signature-date">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}<br>
                Mengetahui,
            </div>
            <div class="signature-title">
                Kepala Sekolah Minggu Buddha
            </div>
            <div class="signature-name">
                {{ $admin->name ?? 'Admin Sekolah Minggu' }}
            </div>
        </div>
    </div>

    <p class="footer-note">
        <em>Dokumen ini dicetak otomatis oleh Sistem Informasi Sekolah Minggu Buddha.</em>
    </p>
</body>

</html>
