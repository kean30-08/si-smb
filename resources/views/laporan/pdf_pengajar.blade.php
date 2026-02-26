<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; } 
        table { width: 100%; border-collapse: collapse; margin-top: 10px; } 
        th, td { border: 1px solid #000; padding: 5px; text-align: center; } 
        th { background-color: #f2f2f2; } 
        .left { text-align: left; }
    </style>
</head>
<body>
    <h2 style="text-align: center; margin-bottom: 0;">Laporan Data & Kehadiran Pengurus Vihara</h2>
    <p style="text-align: center; margin-top: 5px;">Periode: {{ \Carbon\Carbon::parse($mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($selesai)->format('d/m/Y') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th class="left">Nama Pengajar / Pengurus</th>
                <th>Jabatan</th>
                <th>Hadir</th>
                <th>Izin</th>
                <th>Sakit</th>
                <th>Alpa</th>
                <th>Persentase Keaktifan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pengajars as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="left">{{ $p->nama_lengkap }}</td>
                <td>{{ $p->jabatan ?? '-' }}</td>
                <td>{{ $p->total_hadir }}</td>
                <td>{{ $p->total_izin }}</td>
                <td>{{ $p->total_sakit }}</td>
                <td>{{ $p->total_alpa }}</td>
                <td><strong>{{ $p->persentase }}%</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>