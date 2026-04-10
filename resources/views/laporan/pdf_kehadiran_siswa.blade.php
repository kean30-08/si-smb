<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .left {
            text-align: left;
        }

        .row-total {
            background-color: #e6e6e6;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
            padding-right: 10px;
        }

        /* Style Tanda Tangan */
        .signature-box {
            width: 100%;
            page-break-inside: avoid;
        }

        .print-info {
            float: left;
            margin-top: 85px;
            /* Disesuaikan agar sejajar dengan nama Kepala Sekolah */
            font-size: 10px;
            font-style: italic;
            color: #555;
        }

        .signature-wrapper {
            float: right;
            width: 250px;
            text-align: center;
        }

        .signature-date {
            margin-bottom: 5px;
            font-size: 11px;
        }

        .signature-title {
            margin-bottom: 60px;
            font-weight: bold;
            font-size: 11px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 12px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center; margin-bottom: 0;">Laporan Tingkat Keaktifan Siswa</h2>
    <p style="text-align: center; margin-top: 5px;">Periode: {{ \Carbon\Carbon::parse($mulai)->format('d/m/Y') }} -
        {{ \Carbon\Carbon::parse($selesai)->format('d/m/Y') }} | Kelas: {{ $nama_kelas }}</p>

    <table>
        <thead>
            <tr>
                <th>Peringkat</th>
                <th class="left">Nama Siswa</th>
                <th>Kls</th>
                <th>Hadir</th>
                <th>Izin</th>
                <th>Sakit</th>
                <th>Alpa</th>
                <th>Persentase Keaktifan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswas as $index => $siswa)
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
            @empty
                <tr>
                    <td colspan="8">Tidak ada data siswa pada kelas/periode ini.</td>
                </tr>
            @endforelse
        </tbody>

        @if ($siswas->count() > 0)
            <tfoot>
                <tr class="row-total">
                    <td colspan="3" class="text-right">TOTAL KESELURUHAN</td>
                    <td>{{ $siswas->sum('total_hadir') }}</td>
                    <td>{{ $siswas->sum('total_izin') }}</td>
                    <td>{{ $siswas->sum('total_sakit') }}</td>
                    <td>{{ $siswas->sum('total_alpa') }}</td>
                    <td>-</td>
                </tr>
                <tr class="row-total">
                    <td colspan="3" class="text-right">RATA-RATA</td>
                    <td>{{ round((float) $siswas->avg('total_hadir'), 1) }}</td>
                    <td>{{ round((float) $siswas->avg('total_izin'), 1) }}</td>
                    <td>{{ round((float) $siswas->avg('total_sakit'), 1) }}</td>
                    <td>{{ round((float) $siswas->avg('total_alpa'), 1) }}</td>
                    <td>{{ round((float) $siswas->avg('persentase')) }}%</td>
                </tr>
            </tfoot>
        @endif
    </table> {{-- PERHATIKAN: Tag table ditutup di sini --}}

    {{-- KOTAK TANDA TANGAN & WAKTU CETAK --}}
    <div class="signature-box clearfix">
        {{-- Kiri: Info Cetak --}}
        <div class="print-info">
            Dicetak pada: {{ \Carbon\Carbon::now('Asia/Makassar')->translatedFormat('d F Y, H:i') }} WITA
        </div>

        {{-- Kanan: Tanda Tangan --}}
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
</body>

</html>
