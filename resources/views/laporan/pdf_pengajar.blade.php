<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi Pengajar</title>
    <style>
        @page {
            margin: 140px 30px 40px 30px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            color: #000;
        }

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

        .legend-box {
            font-size: 10px;
            margin-bottom: 10px;
        }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-bottom: 30px;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }

        table.main-table th {
            background-color: #f2f2f2;
        }

        table.main-table tr {
            page-break-inside: auto;
        }

        .left {
            text-align: left;
            padding-left: 6px !important;
        }

        .row-total {
            background-color: #e6e6e6;
            font-weight: bold;
        }

        .tgl-kecil {
            font-size: 8px;
            font-weight: normal;
            display: block;
            margin-top: 2px;
        }

        .page-break {
            page-break-after: always;
        }

        /* Teks Libur Vertikal Rapi */
        .cell-libur {
            font-weight: bold;
            text-align: center;
            vertical-align: top;
            padding-top: 15px !important;
            font-size: 9px;
            line-height: 1.3;
            border: 1px solid #000 !important;
        }

        /* Tanda Tangan */
        table.signature-table {
            width: 100%;
            margin-top: 10px;
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
    <header>
        <table class="kop-surat">
            <tr>
                <td width="90" style="text-align: center;">
                    @php
                        $path = public_path('img/logo2_smb.jpg');
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        if (file_exists($path)) {
                            $data = file_get_contents($path);
                            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        } else {
                            $base64 = '';
                        }
                    @endphp
                    @if ($base64)
                        <img src="{{ $base64 }}" width="80" alt="Logo SMB">
                    @endif
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

    <main>
        @php $isFirstPage = true; @endphp

        @foreach ($agendasPerBulan as $bulanKey => $tanggalArray)
            @php
                $namaBulan = \Carbon\Carbon::createFromFormat('Y-m', $bulanKey)->translatedFormat('F Y');
                $tanggals = $tanggalArray->values()->all();
                $jumlahKolom = count($tanggals);
                if ($jumlahKolom == 0) {
                    $jumlahKolom = 1;
                }
                $romawiMinggu = ['I', 'II', 'III', 'IV', 'V', 'VI'];
                $totalHadirPerMinggu = array_fill(0, $jumlahKolom, 0);

                // Chunk Pengajar per 20 orang untuk menghindari bug garis hilang
                $pengajarChunks = $pengajars->chunk(20);
            @endphp

            @foreach ($pengajarChunks as $chunkIndex => $chunk)
                @if (!$isFirstPage)
                    <div class="page-break"></div>
                @endif
                @php $isFirstPage = false; @endphp

                <h2 style="text-align: center; margin-top: 0; margin-bottom: 5px;">LAPORAN ABSENSI PENGAJAR / PENGURUS
                    SMB VDC {{ $chunkIndex > 0 ? '(Lanjutan)' : '' }}</h2>
                <p style="text-align: center; margin-top: 0; margin-bottom: 10px; font-weight: bold; font-size: 12px;">
                    TAHUN AJARAN: {{ strtoupper($nama_ta) }}</p>

                <div class="legend-box">
                    <strong>Keterangan:</strong> H = Hadir &nbsp;|&nbsp; I = Izin &nbsp;|&nbsp; S = Sakit &nbsp;|&nbsp;
                    L = Libur &nbsp;|&nbsp; A = Alpa
                </div>

                <table class="main-table">
                    <thead>
                        <tr>
                            <th rowspan="2" width="5%">NO</th>
                            <th rowspan="2" width="40%">NAMA PENGAJAR</th>
                            <th rowspan="2" width="25%">JABATAN</th>
                            <th colspan="{{ $jumlahKolom }}">{{ strtoupper($namaBulan) }}</th>
                        </tr>
                        <tr>
                            @for ($i = 0; $i < $jumlahKolom; $i++)
                                <th>
                                    M.{{ $romawiMinggu[$i] ?? $i + 1 }}
                                    <span class="tgl-kecil">
                                        {{ isset($tanggals[$i]) ? \Carbon\Carbon::parse($tanggals[$i])->format('d M') : '-' }}
                                    </span>
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $globalNo = $chunkIndex * 20 + 1;
                            $indexInChunk = 0;
                        @endphp

                        @foreach ($chunk as $p)
                            <tr>
                                <td>{{ $globalNo++ }}</td>
                                <td class="left">{{ $p->nama_lengkap }}</td>
                                <td>{{ $p->jabatan->nama_jabatan ?? '-' }}</td>

                                @for ($i = 0; $i < $jumlahKolom; $i++)
                                    @if (isset($tanggals[$i]))
                                        @php
                                            $tgl = $tanggals[$i];
                                            $deskripsiLibur = $agendaStatusMap[$tgl] ?? false;
                                        @endphp

                                        @if ($deskripsiLibur)
                                            @if ($indexInChunk == 0)
                                                @php
                                                    $vertHTML = strtoupper(str_replace(' ', '<br>', $deskripsiLibur));
                                                @endphp
                                                <td rowspan="{{ count($chunk) }}" class="cell-libur">
                                                    {!! $vertHTML !!}
                                                </td>
                                            @endif
                                        @else
                                            @php
                                                $status = $p->absen_map[$tgl] ?? null;
                                                $simbol = 'A'; // Default Alpa

                                                if ($status == 'hadir') {
                                                    $simbol = 'H';
                                                    $totalHadirPerMinggu[$i]++;
                                                } elseif ($status == 'sakit') {
                                                    $simbol = 'S';
                                                } elseif ($status == 'izin') {
                                                    $simbol = 'I';
                                                }
                                            @endphp
                                            <td><strong>{{ $simbol }}</strong></td>
                                        @endif
                                    @else
                                        <td>-</td>
                                    @endif
                                @endfor
                            </tr>
                            @php $indexInChunk++; @endphp
                        @endforeach

                        @if ($loop->last)
                            <tr class="row-total">
                                <td colspan="3" class="text-right" style="padding-right: 10px;">JUMLAH KEHADIRAN (H)
                                </td>
                                @for ($i = 0; $i < $jumlahKolom; $i++)
                                    <td>
                                        @php
                                            $isLiburCol = isset($tanggals[$i])
                                                ? $agendaStatusMap[$tanggals[$i]] ?? false
                                                : false;
                                        @endphp
                                        {{ $isLiburCol ? '-' : (isset($tanggals[$i]) ? $totalHadirPerMinggu[$i] : '-') }}
                                    </td>
                                @endfor
                            </tr>
                        @endif
                    </tbody>
                </table>
            @endforeach
        @endforeach

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
                            Tabanan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                            Mengetahui,
                        </div>
                        <div class="signature-title">
                            Kepala Sekolah Minggu Buddha
                        </div>
                        <div class="signature-name">
                            {{ $namaKepalaSekolah }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </main>
</body>

</html>
