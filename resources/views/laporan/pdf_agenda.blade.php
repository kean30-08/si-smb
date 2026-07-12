<!DOCTYPE html>
<html>

<head>
    <style>
        /* Mengatur batas margin kertas PDF (Atas Kanan Bawah Kiri) */
        @page {
            margin: 140px 40px 40px 40px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
        }

        /* HEADER & KOP SURAT (Fixed Position agar berulang) */
        header {
            position: fixed;
            top: -120px;
            left: 0px;
            right: 0px;
            height: 100px;
        }

        table.kop-surat {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            border: none;
        }

        table.kop-surat td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .kop-text {
            text-align: center;
            line-height: 1.3;
        }

        .kop-title-1 {
            font-size: 20px;
            font-weight: bold;
        }

        .kop-title-2 {
            font-size: 18px;
            font-weight: bold;
        }

        .kop-address {
            font-size: 11px;
            margin-top: 4px;
        }

        .garis-kop {
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 2px;
            margin-top: 10px;
        }

        /* TABEL UTAMA */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 30px;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        table.main-table th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .center {
            text-align: center !important;
        }

        /* KOTAK ANALISIS */
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

        /* TANDA TANGAN */
        table.signature-table {
            width: 100%;
            margin-top: 30px;
            border: none !important;
            page-break-inside: avoid;
        }

        table.signature-table td {
            border: none !important;
            padding: 0;
            vertical-align: bottom;
        }

        .print-info {
            font-size: 10px;
            font-style: italic;
            color: #555;
        }

        .signature-wrapper {
            display: inline-block;
            width: 250px;
            text-align: center;
            float: right;
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
    </style>
</head>

<body>
    {{-- HEADER KOP SURAT (Otomatis Berulang) --}}
    <header>
        <table class="kop-surat">
            <tr>
                <td width="90" style="text-align: center;">
                    @php
                        $path = public_path('img/logo2_smb.jpg');
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    @endphp

                    <img src="{{ $base64 }}" width="80" alt="Logo SMB">
                </td>
                <td class="kop-text">
                    <div class="kop-title-1">SEKOLAH MINGGU BUDDHA (SMB)</div>
                    <div class="kop-title-2">VIHARA DHARMA CATTRA</div>
                    <div class="kop-address">Jl. Melati No.18, Delod Peken, Kec. Tabanan, Kabupaten Tabanan, Bali 82121
                    </div>
                </td>
                <td width="90"></td>
            </tr>
        </table>
        <div class="garis-kop"></div>
    </header>

    {{-- KONTEN UTAMA --}}
    <main>
        <h2 style="text-align: center; margin-top: 0; margin-bottom: 0;">Laporan Statistik Kegiatan & Agenda</h2>
        <p style="text-align: center; margin-top: 5px;">Periode: {{ \Carbon\Carbon::parse($mulai)->format('d/m/Y') }} -
            {{ \Carbon\Carbon::parse($selesai)->format('d/m/Y') }}</p>

        <table class="main-table">
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

                    {{-- TRIK JITU: Paksa potong tabel dan pindah halaman setiap 20 baris --}}
                    @if (($index + 1) % 20 == 0 && !$loop->last)
            </tbody>
        </table>
        <div style="page-break-before: always;"></div>
        <table class="main-table">
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
                @endif
            @empty
                <tr>
                    <td colspan="5" class="center">Tidak ada kegiatan pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if ($agendas->count() > 0)
            @php
                $total_kegiatan = $agendas->count();
                $total_kehadiran = $agendas->sum('jumlah_hadir');
                $rata_rata = $total_kegiatan > 0 ? round($total_kehadiran / $total_kegiatan) : 0;
                $kegiatan_tertinggi = $agendas->sortByDesc('jumlah_hadir')->first();
                $kegiatan_terendah = $agendas->sortBy('jumlah_hadir')->first();
            @endphp

            <div class="summary-box" style="page-break-inside: avoid;">
                <div class="summary-title">Ringkasan & Analisis Statistik</div>
                <ul class="summary-list">

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


            </div>
        @endif

        <table class="signature-table">
            <tr>
                <td style="width: 50%; text-align: left;">
                    <div class="print-info">
                        Dicetak pada: {{ \Carbon\Carbon::now('Asia/Makassar')->translatedFormat('d F Y, H:i') }} WITA
                    </div>
                </td>
                <td style="width: 50%; text-align: right;">
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
                </td>
            </tr>
        </table>
    </main>
</body>

</html>
