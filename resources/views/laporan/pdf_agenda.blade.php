<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekap Agenda</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #333; text-transform: uppercase;}
        .header p { margin: 5px 0; font-size: 14px;}
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Rekapitulasi Kegiatan</h2>
        <p>Vihara Dharma Cakra Tabanan</p>
        <p>Periode: <strong>{{ \Carbon\Carbon::parse($mulai)->translatedFormat('d F Y') }}</strong> s.d <strong>{{ \Carbon\Carbon::parse($selesai)->translatedFormat('d F Y') }}</strong></p>
        <hr>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Nama Kegiatan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($agendas as $index => $agenda)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($agenda->tanggal)->translatedFormat('d M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($agenda->waktu_mulai)->format('H:i') }} - {{ $agenda->waktu_selesai ? \Carbon\Carbon::parse($agenda->waktu_selesai)->format('H:i') : 'Selesai' }}</td>
                <td>{{ $agenda->nama_kegiatan }}</td>
                <td>{{ ucfirst($agenda->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>