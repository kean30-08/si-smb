<!DOCTYPE html>
<html>
<head>
    <title>Laporan Data Siswa</title>
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
        <h2>Laporan Data Siswa Sekolah Minggu</h2>
        <p>Vihara Dharma Cakra Tabanan</p>
        <p><strong>Kelas: {{ $nama_kelas }}</strong></p>
        <hr>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Lengkap</th>
                <th>L/P</th>
                <th>Kelas</th>
                <th>Nomor HP Ortu</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($siswas as $index => $siswa)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $siswa->nis }}</td>
                <td>{{ $siswa->nama_lengkap }}</td>
                <td>{{ $siswa->jenis_kelamin }}</td>
                <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                <td>{{ $siswa->nomor_hp_orang_tua ?? '-' }}</td>
                <td>{{ ucfirst($siswa->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>