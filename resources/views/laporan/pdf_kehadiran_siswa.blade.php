<!DOCTYPE html>
<html>
<head><style>body { font-family: Arial, sans-serif; font-size: 11px; } table { width: 100%; border-collapse: collapse; margin-top: 10px; } th, td { border: 1px solid #000; padding: 5px; text-align: center; } th { background-color: #f2f2f2; } .left { text-align: left; }</style></head>
<body>
    <h2 style="text-align: center; margin-bottom: 0;">Laporan Tingkat Keaktifan Siswa (Apresiasi)</h2>
    <p style="text-align: center; margin-top: 5px;">Periode: {{ \Carbon\Carbon::parse($mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($selesai)->format('d/m/Y') }} | Kelas: {{ $nama_kelas }}</p>
    <table>
        <thead>
            <tr>
                <th>Peringkat</th>
                <th class="left">Nama Siswa</th>
                <th>Kls</th>
                <th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alpa</th>
                <th>Persentase Keaktifan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($siswas as $index => $siswa)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="left">{{ $siswa->nama_lengkap }}</td>
                <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                <td>{{ $siswa->total_hadir }}</td>
                <td>{{ $siswa->total_izin }}</td>
                <td>{{ $siswa->total_sakit }}</td>
                <td>{{ $siswa->total_alpa }}</td>
                <td><strong>{{ $siswa->persentase }}%</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>