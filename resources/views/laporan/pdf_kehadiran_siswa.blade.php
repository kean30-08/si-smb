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

        .legend-box {
            font-size: 10px;
            margin-bottom: 5px;
        }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
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

        .tgl-kecil {
            font-size: 8px;
            font-weight: normal;
            display: block;
            margin-top: 2px;
        }

        .page-break {
            page-break-after: always;
        }

        /* Gaya Khusus Hari Libur */
        .cell-libur {
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            font-size: 10px;
            line-height: 1.5;
            background-color: #ffffff !important; /* Mencegah warisan warna abu-abu dari row divider */
        }

        .row-divider td {
            background-color: #e2e8f0;
            font-weight: bold;
            text-align: left;
        }

        .row-total td {
            background-color: #cbd5e1 !important; /* Warna background lebih pekat (abu-abu gelap) */
            font-weight: bold !important;         /* Teks tebal */
            font-size: 12px !important;           /* Ukuran huruf lebih besar dari standar */
            color: #000 !important;
        }

        .row-grand-total td {
            background-color: #94a3b8 !important; /* Warna abu-abu lebih pekat */
            font-weight: bold !important;
            font-size: 13px !important;
            color: #000 !important;
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
                    <div class="kop-address">Jl. Melati No.18, Delod Peken, Kec. Tabanan, Kabupaten Tabanan, Bali 82121</div>
                </td>
                <td width="90"></td>
            </tr>
        </table>
        <div class="garis-kop"></div>
    </header>

    <main>
        @php 
            $isFirstPage = true; 
            
            // Hitung Grand Total per tanggal
            $grandTotalPerTgl = [];
            foreach ($rekapHadirPerKelas as $kls => $tgls) {
                foreach ($tgls as $t => $jml) {
                    if (!isset($grandTotalPerTgl[$t])) $grandTotalPerTgl[$t] = 0;
                    $grandTotalPerTgl[$t] += $jml;
                }
            }
        @endphp

        @foreach ($agendasPerBulan as $bulanKey => $tanggalArray)
            @php
                $namaBulan = \Carbon\Carbon::createFromFormat('Y-m', $bulanKey)->translatedFormat('F Y');
                $tanggals = $tanggalArray->values()->all();
                $jumlahKolom = count($tanggals) ?: 1;
                $romawiMinggu = ['I', 'II', 'III', 'IV', 'V', 'VI'];

                // MEMECAH DATA MENJADI 22 SISWA PER HALAMAN
                $siswaChunks = $siswas->chunk(22);
                
                $globalNo = 1;
                $globalPrevKelas = null;
                $classNo = 1;
            @endphp

            @foreach ($siswaChunks as $chunkIndex => $chunk)
                @if (!$isFirstPage)
                    <div class="page-break"></div>
                @endif
                @php $isFirstPage = false; @endphp

                <h2 style="text-align: center; margin-top: 0; margin-bottom: 5px;">
                    LAPORAN ABSENSI SISWA SMB VDC {{ $chunkIndex > 0 ? '(Lanjutan)' : '' }}
                </h2>
                <p style="text-align: center; margin-top: 0; margin-bottom: 10px; font-weight: bold; font-size: 12px;">
                    TAHUN AJARAN: {{ strtoupper($nama_ta) }} <br>
                    BULAN: {{ strtoupper($namaBulan) }}
                </p>

                <div class="legend-box">
                    <strong>Keterangan:</strong> H = Hadir &nbsp;|&nbsp; I = Izin &nbsp;|&nbsp; S = Sakit &nbsp;|&nbsp; L = Libur &nbsp;|&nbsp; A = Alpa
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
                            // MENGKALKULASI JUMLAH BARIS ROWSPAN LIBUR YANG PAS UNTUK CHUNK/HALAMAN INI
                            $rowspanValue = 0;
                            $tempKelas = $globalPrevKelas;
                            
                            foreach ($chunk as $s) {
                                $cKelas = $s->kelas_laporan;
                                if ($tempKelas !== null && $tempKelas !== $cKelas) {
                                    $rowspanValue++; // +1 Baris Total Kelas Lama
                                }
                                if ($tempKelas !== $cKelas) {
                                    $rowspanValue++; // +1 Baris Pembatas Kelas Baru
                                    $tempKelas = $cKelas;
                                }
                                $rowspanValue++; // +1 Baris Nama Siswa
                            }

                            
                            // Jika ini chunk terakhir di data, tambah baris untuk baris total terakhir & grand total
                            if ($loop->last) {
                                $rowspanValue += 2; 
                            }

                            $isFirstRowPrinted = false;
                        @endphp

                        @foreach ($chunk as $siswa)
                            @php $currentKelas = $siswa->kelas_laporan; @endphp

                            {{-- JIKA GANTI KELAS, CETAK TOTAL KEHADIRAN KELAS SEBELUMNYA --}}
                            @if ($globalPrevKelas !== null && $globalPrevKelas !== $currentKelas)
                                <tr class="row-total">
                                    <td colspan="4" class="left">JUMLAH KEHADIRAN (H) - KELAS {{ strtoupper(preg_replace('/Kelas /i', '', $globalPrevKelas)) }}</td>
                                    @for ($i = 0; $i < $jumlahKolom; $i++)
                                        @php $tgl = $tanggals[$i] ?? null; $isLibur = $tgl ? ($agendaStatusMap[$tgl] ?? false) : false; @endphp
                                        @if ($isLibur)
                                            @if (!$isFirstRowPrinted)
                                                <td rowspan="{{ $rowspanValue }}" class="cell-libur">{!! strtoupper(str_replace(' ', '<br>', $isLibur)) !!}</td>
                                            @endif
                                        @else
                                            <td>{{ $tgl ? ($rekapHadirPerKelas[$globalPrevKelas][$tgl] ?? 0) : '-' }}</td>
                                        @endif
                                    @endfor
                                </tr>
                                @php $isFirstRowPrinted = true; @endphp
                            @endif

                            {{-- BARIS PEMBATAS UNTUK KELAS BARU --}}
                            @if ($globalPrevKelas !== $currentKelas)
                                <tr class="row-divider">
                                    <td colspan="4" class="left">KELAS: {{ strtoupper(preg_replace('/Kelas /i', '', $currentKelas)) }}</td>
                                    @for ($i = 0; $i < $jumlahKolom; $i++)
                                        @php $tgl = $tanggals[$i] ?? null; $isLibur = $tgl ? ($agendaStatusMap[$tgl] ?? false) : false; @endphp
                                        @if ($isLibur)
                                            @if (!$isFirstRowPrinted)
                                                <td rowspan="{{ $rowspanValue }}" class="cell-libur">{!! strtoupper(str_replace(' ', '<br>', $isLibur)) !!}</td>
                                            @endif
                                        @else
                                            <td></td>
                                        @endif
                                    @endfor
                                </tr>
                                @php 
                                    $globalPrevKelas = $currentKelas;
                                    $classNo = 1;
                                    $isFirstRowPrinted = true;
                                @endphp
                            @endif

                            {{-- BARIS DATA SISWA --}}
                            <tr>
                                <td>{{ $classNo++ }}</td>
                                <td class="left">{{ $siswa->nama_lengkap }}</td>
                                <td>{{ preg_replace('/Kelas /i', '', $siswa->kelas_laporan) }}</td>
                                <td class="left" style="font-size: 9px;">{{ $siswa->asal_sekolah ?? '-' }}</td>

                                @for ($i = 0; $i < $jumlahKolom; $i++)
                                    @php $tgl = $tanggals[$i] ?? null; $isLibur = $tgl ? ($agendaStatusMap[$tgl] ?? false) : false; @endphp
                                    @if ($isLibur)
                                        @if (!$isFirstRowPrinted)
                                            <td rowspan="{{ $rowspanValue }}" class="cell-libur">{!! strtoupper(str_replace(' ', '<br>', $isLibur)) !!}</td>
                                        @endif
                                    @else
                                        @php
                                            $tglDaftar = \Carbon\Carbon::parse($siswa->created_at)->format('Y-m-d');
                                            $isBelumDaftar = $tgl && ($tgl < $tglDaftar);
                                            $status = $tgl ? ($siswa->absen_map[$tgl] ?? null) : null;
                                            $simbol = 'A';
                                            if ($status == 'hadir') $simbol = 'H';
                                            elseif ($status == 'sakit') $simbol = 'S';
                                            elseif ($status == 'izin') $simbol = 'I';
                                        @endphp
                                        
                                        @if ($isBelumDaftar)
                                            <td>-</td>
                                        @else
                                            {{-- Menghapus tag <strong> agar huruf tidak tebal --}}
                                            <td>{{ $simbol }}</td>
                                        @endif
                                    @endif
                                @endfor
                            </tr>
                            @php $isFirstRowPrinted = true; @endphp
                            
                        @endforeach

                        {{-- CETAK TOTAL KELAS TERAKHIR HANYA DI HALAMAN/CHUNK TERAKHIR --}}
                        {{-- CETAK TOTAL KELAS TERAKHIR & GRAND TOTAL HANYA DI HALAMAN/CHUNK TERAKHIR --}}
                        @if ($loop->last)
                            <tr class="row-total">
                                <td colspan="4" class="left">JUMLAH KEHADIRAN (H) - KELAS {{ strtoupper(preg_replace('/Kelas /i', '', $currentKelas)) }}</td>
                                @for ($i = 0; $i < $jumlahKolom; $i++)
                                    @php $tgl = $tanggals[$i] ?? null; $isLibur = $tgl ? ($agendaStatusMap[$tgl] ?? false) : false; @endphp
                                    @if ($isLibur)
                                        @if (!$isFirstRowPrinted)
                                            <td rowspan="{{ $rowspanValue }}" class="cell-libur">{!! strtoupper(str_replace(' ', '<br>', $isLibur)) !!}</td>
                                        @endif
                                    @else
                                        <td>{{ $tgl ? ($rekapHadirPerKelas[$currentKelas][$tgl] ?? 0) : '-' }}</td>
                                    @endif
                                @endfor
                            </tr>

                            {{-- BARIS GRAND TOTAL KESELURUHAN --}}
                            <tr class="row-grand-total">
                                <td colspan="4" class="left" style="text-align: right; padding-right: 15px !important;">TOTAL KESELURUHAN KEHADIRAN (SEMUA KELAS)</td>
                                @for ($i = 0; $i < $jumlahKolom; $i++)
                                    @php $tgl = $tanggals[$i] ?? null; $isLibur = $tgl ? ($agendaStatusMap[$tgl] ?? false) : false; @endphp
                                    @if (!$isLibur)
                                        <td>{{ $tgl ? ($grandTotalPerTgl[$tgl] ?? 0) : '-' }}</td>
                                    @endif
                                @endfor
                            </tr>
                        @endif

                    </tbody>
                </table>
            @endforeach
        @endforeach
    </main>
</body>

</html>