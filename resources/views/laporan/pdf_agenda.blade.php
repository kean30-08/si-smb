<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin-bottom: 50px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .center {
            text-align: center;
        }

        /* Style untuk kotak ringkasan analisis */
        .summary-box {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #555;
            background-color: #fafafa;
            border-radius: 5px;
        }

        .summary-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            text-transform: uppercase;
        }

        .summary-list {
            margin: 0;
            padding-left: 20px;
            margin-bottom: 15px;
        }

        .summary-list li {
            margin-bottom: 6px;
        }

        .summary-paragraph {
            text-align: justify;
            line-height: 1.6;
            margin: 0;
            font-size: 11px;
        }

        /* TAMBAHAN: Style untuk Kotak Tanda Tangan */
        .signature-box {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
            /* Mencegah ttd terpotong ke halaman berikutnya */
        }

        .signature-wrapper {
            float: right;
            width: 250px;
            text-align: center;
        }

        .signature-date {
            margin-bottom: 10px;
            font-size: 11px;
        }

        .signature-title {
            margin-bottom: 60px;
            /* Ruang kosong untuk paraf/tanda tangan */
            font-weight: bold;
            font-size: 11px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 12px;
        }

        .signature-position {
            font-size: 11px;
            margin-top: 3px;
        }

        /* Clearfix untuk mengatasi float */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center; margin-bottom: 0;">Laporan Statistik Kegiatan & Agenda</h2>
    <p style="text-align: center; margin-top: 5px;">Periode: {{ \Carbon\Carbon::parse($mulai)->format('d/m/Y') }} -
        {{ \Carbon\Carbon::parse($selesai)->format('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Nama Kegiatan</th>
                <th>Jumlah Siswa Hadir</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($agendas as $index => $agenda)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($agenda->tanggal)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($agenda->waktu_mulai)->format('H:i') }} -
                        {{ $agenda->waktu_selesai ? \Carbon\Carbon::parse($agenda->waktu_selesai)->format('H:i') : 'Selesai' }}
                    </td>
                    <td>{{ $agenda->nama_kegiatan }}</td>
                    <td class="center"><strong>{{ $agenda->jumlah_hadir }} Orang</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center">Tidak ada kegiatan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- LOGIKA PERHITUNGAN STATISTIK (Dijalankan jika ada data agenda) --}}
    @if ($agendas->count() > 0)
        @php
            $total_kegiatan = $agendas->count();
            $total_kehadiran = $agendas->sum('jumlah_hadir');
            $rata_rata = $total_kegiatan > 0 ? round($total_kehadiran / $total_kegiatan) : 0;
            $kegiatan_tertinggi = $agendas->sortByDesc('jumlah_hadir')->first();
            $kegiatan_terendah = $agendas->sortBy('jumlah_hadir')->first();
        @endphp

        <div class="summary-box">
            <div class="summary-title">Ringkasan & Analisis Statistik</div>

            <ul class="summary-list">
                <li><strong>Total Jumlah Kegiatan:</strong> {{ $total_kegiatan }} Kegiatan</li>
                <li><strong>Total Kehadiran Siswa:</strong> {{ $total_kehadiran }} Kehadiran</li>
                <li><strong>Rata-rata Kehadiran:</strong> ~{{ $rata_rata }} Siswa per kegiatan</li>
                <li>
                    <strong>Kehadiran Tertinggi:</strong>
                    {{ $kegiatan_tertinggi->nama_kegiatan }} pada
                    {{ \Carbon\Carbon::parse($kegiatan_tertinggi->tanggal)->translatedFormat('d F Y') }}
                    ({{ $kegiatan_tertinggi->jumlah_hadir }} Siswa)
                </li>
                <li>
                    <strong>Kehadiran Terendah:</strong>
                    {{ $kegiatan_terendah->nama_kegiatan }} pada
                    {{ \Carbon\Carbon::parse($kegiatan_terendah->tanggal)->translatedFormat('d F Y') }}
                    ({{ $kegiatan_terendah->jumlah_hadir }} Siswa)
                </li>
            </ul>

            <p class="summary-paragraph">
                <strong>Kesimpulan:</strong> Berdasarkan data kegiatan pada periode
                {{ \Carbon\Carbon::parse($mulai)->translatedFormat('d F Y') }} sampai
                {{ \Carbon\Carbon::parse($selesai)->translatedFormat('d F Y') }}, tercatat sebanyak
                <strong>{{ $total_kegiatan }} kegiatan</strong> yang telah dilaksanakan. Total akumulasi kehadiran
                siswa pada seluruh kegiatan mencapai <strong>{{ $total_kehadiran }} kehadiran</strong>, dengan
                rata-rata <strong>{{ $rata_rata }} siswa hadir per kegiatan</strong>.
                Tingkat partisipasi tertinggi dicapai pada kegiatan
                <strong>{{ $kegiatan_tertinggi->nama_kegiatan }}</strong>
                ({{ \Carbon\Carbon::parse($kegiatan_tertinggi->tanggal)->translatedFormat('d F Y') }}) dengan jumlah
                <strong>{{ $kegiatan_tertinggi->jumlah_hadir }} siswa</strong>, sedangkan kehadiran paling minim
                tercatat pada kegiatan <strong>{{ $kegiatan_terendah->nama_kegiatan }}</strong>
                ({{ \Carbon\Carbon::parse($kegiatan_terendah->tanggal)->translatedFormat('d F Y') }}) dengan jumlah
                <strong>{{ $kegiatan_terendah->jumlah_hadir }} siswa</strong>.
            </p>
        </div>
    @endif

    {{-- TAMBAHAN: KOTAK TANDA TANGAN (PARAF) --}}
    <div class="signature-box clearfix">
        <div class="signature-wrapper">
            <div class="signature-date">
                {{-- Tanggal otomatis menyesuaikan hari saat PDF ini di-download --}}
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                <br>Mengetahui,<br>

            </div>

            <div class="signature-title">
                Kepala Sekolah Minggu Buddha
            </div>

            {{-- Nama Admin diambil dari database (dari LaporanController) --}}
            <div class="signature-name">
                {{ $admin->name ?? 'Admin Sekolah Minggu' }}
            </div>
        </div>
    </div>

</body>

</html>
