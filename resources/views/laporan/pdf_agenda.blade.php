<!DOCTYPE html>
<html>
<head><style>body { font-family: Arial, sans-serif; font-size: 11px; } table { width: 100%; border-collapse: collapse; margin-top: 10px; } th, td { border: 1px solid #000; padding: 5px; text-align: left; } th { background-color: #f2f2f2; text-align: center; } .center { text-align: center; }</style></head>
<body>
    <h2 style="text-align: center; margin-bottom: 0;">Laporan Statistik Kegiatan & Agenda</h2>
    <p style="text-align: center; margin-top: 5px;">Periode: {{ \Carbon\Carbon::parse($mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($selesai)->format('d/m/Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>No</th><th>Tanggal</th><th>Waktu</th><th>Nama Kegiatan</th><th>Jumlah Siswa Hadir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($agendas as $index => $agenda)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($agenda->tanggal)->format('d M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($agenda->waktu_mulai)->format('H:i') }} - {{ $agenda->waktu_selesai ? \Carbon\Carbon::parse($agenda->waktu_selesai)->format('H:i') : 'Selesai' }}</td>
                <td>{{ $agenda->nama_kegiatan }}</td>
                <td class="center"><strong>{{ $agenda->jumlah_hadir }} Orang</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>