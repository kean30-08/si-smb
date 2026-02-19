<!DOCTYPE html>
<html>
<head>
    <title>Rundown Kegiatan Vihara</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #333; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
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
                <th>Waktu</th>
                <th>Nama Kegiatan</th>
                <th>Catatan Khusus</th>
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

    <p style="margin-top: 30px; font-size: 12px; text-align: center;">
        <em>Dokumen ini dibuat otomatis oleh Sistem Informasi Sekolah Minggu Buddha.</em>
    </p>
</body>
</html>