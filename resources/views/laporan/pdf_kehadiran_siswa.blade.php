<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi Siswa</title>
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

        .title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .legend-box {
            font-size: 10px;
            margin-bottom: 5px;
        }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }

        table.main-table th {
            background-color: #f2f2f2;
        }

        .left {
            text-align: left;
            padding-left: 6px !important;
        }

        .sub-header {
            background-color: #e6e6e6;
            font-weight: bold;
            text-align: left;
            padding-left: 6px !important;
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

        /* PERBAIKAN: CSS Khusus untuk sel Libur tanpa Rowspan */
        .cell-libur {
            font-weight: bold;
            font-style: italic;
            vertical-align: middle;
            text-align: center;
        }

        /* Modifikasi Border untuk Libur agar terkesan menyatu */
        .libur-top {
            border-bottom: none !important;
        }

        .libur-middle {
            border-top: none !important;
            border-bottom: none !important;
            color: transparent !important;
        }

        /* Teks disembunyikan agar bersih */
        .libur-bottom {
            border-top: none !important;
            color: transparent !important;
        }

        /* Class khusus jika di kelas itu cuma ada 1 murid */
        .libur-single {
            border: 1px solid #000 !important;
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
            @endphp

            @if (!$isFirstPage)
                <div class="page-break"></div>
            @endif
            @php $isFirstPage = false; @endphp

            <h2 style="text-align: center; margin-top: 0; margin-bottom: 5px;">LAPORAN ABSENSI SISWA SMB VDC</h2>
            <p style="text-align: center; margin-top: 0; margin-bottom: 10px; font-weight: bold; font-size: 12px;">TAHUN
                AJARAN: {{ strtoupper($nama_ta) }}</p>

            <div class="legend-box">
                <strong>Keterangan:</strong> H = Hadir &nbsp;|&nbsp; I = Izin &nbsp;|&nbsp; S = Sakit &nbsp;|&nbsp; L =
                Libur &nbsp;|&nbsp; A = Alpa
            </div>

            <table class="main-table">
                <thead>
                    <tr>
                        <th rowspan="2" width="4%">NO</th>
                        <th rowspan="2" width="28%">NAMA</th>
                        <th rowspan="2" width="8%">KELAS</th>
                        <th rowspan="2" width="25%">ASAL SEKOLAH</th>
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
                        $siswaPerKelas = $siswas->groupBy('kelas_laporan');
                        $totalHadirPerMinggu = array_fill(0, $jumlahKolom, 0);
                    @endphp

                    @foreach ($siswaPerKelas as $namaKelas => $siswasKelas)
                        @php
                            $jumlahSiswaDiKelas = count($siswasKelas);
                        @endphp

                        <tr>
                            <td colspan="{{ 4 + $jumlahKolom }}" class="sub-header">{{ strtoupper($namaKelas) }}</td>
                        </tr>

                        @foreach ($siswasKelas as $index => $siswa)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="left">{{ $siswa->nama_lengkap }}</td>
                                <td>{{ preg_replace('/Kelas /i', '', $namaKelas) }}</td>
                                <td class="left" style="font-size: 9px;">{{ $siswa->asal_sekolah ?? '-' }}</td>

                                @for ($i = 0; $i < $jumlahKolom; $i++)
                                    @if (isset($tanggals[$i]))
                                        @php
                                            $tgl = $tanggals[$i];
                                            $isLibur = $agendaStatusMap[$tgl] ?? false;

                                            // Logika Pendaftaran Siswa
                                            $tglDaftar = \Carbon\Carbon::parse($siswa->created_at)->format('Y-m-d');
                                            $isBelumDaftar = $tgl < $tglDaftar;
                                        @endphp

                                        @if ($isLibur)
                                            @php
                                                // Logika CSS Border untuk menciptakan ilusi sel gabungan
                                                $cssClass = 'cell-libur ';
                                                if ($jumlahSiswaDiKelas == 1) {
                                                    $cssClass .= 'libur-single';
                                                } elseif ($index == 0) {
                                                    $cssClass .= 'libur-top';
                                                } elseif ($index == $jumlahSiswaDiKelas - 1) {
                                                    $cssClass .= 'libur-bottom';
                                                } else {
                                                    $cssClass .= 'libur-middle';
                                                }
                                            @endphp

                                            <td class="{{ $cssClass }}">LIBUR</td>
                                        @elseif ($isBelumDaftar)
                                            <td>-</td>
                                        @else
                                            @php
                                                $status = $siswa->absen_map[$tgl] ?? null;
                                                $simbol = 'A';

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
                        @endforeach
                    @endforeach

                    <tr>
                        <td colspan="4" class="left" style="font-weight:bold;">JUMLAH KEHADIRAN (H)</td>
                        @for ($i = 0; $i < $jumlahKolom; $i++)
                            <td style="font-weight:bold;">
                                @php
                                    $isLiburCol = isset($tanggals[$i])
                                        ? $agendaStatusMap[$tanggals[$i]] ?? false
                                        : false;
                                @endphp

                                @if ($isLiburCol)
                                    -
                                @else
                                    {{ isset($tanggals[$i]) ? $totalHadirPerMinggu[$i] : '-' }}
                                @endif
                            </td>
                        @endfor
                    </tr>
                </tbody>
            </table>
        @endforeach
    </main>
</body>

</html>
