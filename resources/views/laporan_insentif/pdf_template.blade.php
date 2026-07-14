<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Insentif Guru</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 16px;
            margin: 0;
            color: #000;
        }

        .page-break {
            page-break-after: always;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        /* COVER STYLES */
        .cover-page {
            padding-top: 50px;
        }

        .cover-title {
            font-size: 25px;
            font-weight: bold;
            text-align: center;
            line-height: 1.5;
            margin-bottom: 60px;
        }

        .cover-logo {
            text-align: center;
            margin-bottom: 60px;
        }

        .cover-author {
            text-align: center;
            font-size: 20px;
            margin-bottom: 120px;
            line-height: 1.5;
        }

        .cover-footer {
            text-align: center;
            font-size: 25px;
            font-weight: bold;
            line-height: 1.5;
            position: absolute;
            bottom: 50px;
            left: 0;
            right: 0;
        }

        /* KOP SURAT */
        .kop-surat {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .kop-surat td {
            vertical-align: middle;
        }

        .kop-title-1,
        .kop-title-2 {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }

        .kop-address {
            font-size: 13px;
            text-align: center;
            font-weight: bold;
        }

        .garis-kop {
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 1px;
            margin-top: 5px;
            margin-bottom: 30px;
        }

        /* TABEL STYLES */
        table.tabel-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-family: 'Times New Roman', Times, serif;
            font-size: 15px;
        }

        table.tabel-data th,
        table.tabel-data td {
            border: 1px solid #000;
            padding: 8px 6px;
            text-align: center;
            vertical-align: middle;
        }

        table.tabel-data th {
            font-weight: bold;
        }

        .text-left {
            text-align: left !important;
            padding-left: 8px !important;
        }

        /* TTD STYLES */
        .ttd-container {
            width: 100%;
            margin-top: 50px;
            font-family: 'Times New Roman', Times, serif;
            font-size: 15px;
        }

        .ttd-left {
            float: left;
            width: 50%;
            text-align: center;
        }

        .ttd-right {
            float: right;
            width: 50%;
            text-align: center;
        }

        .clear {
            clear: both;
        }

        /* CSS KHUSUS GRID ABSEN SISWA */
        .tgl-kecil {
            font-size: 11px;
            font-weight: normal;
            display: block;
            margin-top: 2px;
        }

        .cell-libur {
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            font-size: 11px;
            line-height: 1.4;
        }

        /* GAMBAR DOKUMENTASI */
        .img-grid {
            width: 100%;
            text-align: center;
            margin-top: 20px;
        }

        .img-item {
            width: 45%;
            margin: 10px;
            display: inline-block;
            vertical-align: top;
        }

        .img-item img {
            width: 100%;
            max-height: 250px;
            object-fit: contain;
            border: 1px solid #ccc;
            padding: 3px;
        }
    </style>
</head>

<body>

    @php
        $pathHd = public_path('img/logo_smb_hd.jpg');
        $base64Hd = '';
        if (file_exists($pathHd)) {
            $base64Hd = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($pathHd));
        }

        $pathSm = public_path('img/logo2_smb.jpg');
        $base64Sm = '';
        if (file_exists($pathSm)) {
            $base64Sm = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($pathSm));
        }
    @endphp

    {{-- HALAMAN 1: COVER --}}
    <div class="cover-page">
        <div class="cover-title">
            <h1>LAPORAN</h1><br>
            BANTUAN INTENSIF<br>
            GURU SEKOLAH MINGGU BUDDHA
        </div>

        <div class="cover-logo">
            @if ($base64Hd)
                <img src="{{ $base64Hd }}" width="250" alt="Logo SMB HD">
            @endif
        </div>

        <div class="cover-author">
            <span class="bold">DISUSUN OLEH :</span><br>
            {{ $pengajar->nama_lengkap }}
        </div>

        <div class="cover-footer">
            SEKOLAH MINGGU BUDDHA (SMB)<br>
            VIHARA DHARMA CATTRA<br>
            TABANAN<br>
            {{ $year }}
        </div>
    </div>

    <div class="page-break"></div>

    {{-- HALAMAN 2: DAFTAR AGENDA (RINGKASAN MATERI) --}}
    <table class="kop-surat">
        <tr>
            <td width="90" class="center">
                @if ($base64Sm)
                    <img src="{{ $base64Sm }}" width="80">
                @endif
            </td>
            <td class="kop-text">
                <div class="kop-title-1">SEKOLAH MINGGU BUDDHA</div>
                <div class="kop-title-2">VIHARA DHARMA CATTRA TABANAN</div>
                <div class="kop-address">Jl. Melati No. 18, Tabanan - Bali. Telp. (0361) 811681</div>
            </td>
            <td width="90"></td>
        </tr>
    </table>
    <div class="garis-kop"></div>

    <div class="center bold" style="margin-bottom: 40px; font-size: 16px; line-height: 1.5;">
        LAPORAN BANTUAN INSENTIF GURU<br>
        PENDIDIKAN AGAMA DAN KEAGAMAAN BUDDHA<br>
        BUKAN PEGAWAI NEGERI SIPIL<br>
        KANTOR KEMENTERIAN AGAMA KABUPATEN TABANAN<br>
        TAHUN {{ $year }}
    </div>

    <table style="font-size: 16px; margin-bottom: 15px; font-weight: bold;">
        <tr>
            <td width="80">NAMA</td>
            <td>: {{ strtoupper($pengajar->nama_lengkap) }}</td>
        </tr>
        <tr>
            <td>BULAN</td>
            <td>: {{ strtoupper($namaBulan) }}</td>
        </tr>
    </table>

    <table class="tabel-data">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="20%">PERTEMUAN</th>
                <th width="35%">RINGKASAN MATERI</th>
                <th width="20%">TEMPAT<br>PELAKSANAAN</th>
                <th width="20%">JUMLAH PESERTA<br>DIDIK</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($agendas as $index => $agenda)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($agenda->tanggal)->translatedFormat('l, d F Y') }}</td>
                    <td class="text-left">{{ $agenda->nama_kegiatan }}</td>
                    <td>{{ $agenda->is_libur ? '-' : 'Vihara Dharma Cattra' }}</td>
                    <td>{{ $agenda->is_libur ? '-' : $agenda->jumlah_hadir . ' Orang' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="ttd-container">
        <div class="ttd-left">
            Mengetahui,<br>
            Ketua SMB Vihara Dharma Cattra<br><br><br><br><br><br>
            ( {{ $namaKepalaSekolah }} )
        </div>
        <div class="ttd-right">
            Tabanan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
            Pengajar<br><br><br><br><br><br>
            ( {{ $pengajar->nama_lengkap }} )
        </div>
        <div class="clear"></div>
    </div>

    <div class="page-break"></div>

    {{-- HALAMAN 3 DLL: ABSENSI SISWA (DENGAN CHUNKING ANTI BUG) --}}
    @php
        $tanggals = $agendas->pluck('tanggal')->toArray();
        $agendaIds = $agendas->pluck('id')->toArray();
        $jumlahKolom = count($tanggals);
        if ($jumlahKolom == 0) {
            $jumlahKolom = 1;
        }
        $totalHadirPerMinggu = array_fill(0, $jumlahKolom, 0);

        // Membelah tabel siswa menjadi 25 baris per halaman agar rowspan DOMPDF tidak hancur
        $siswaChunks = $siswas->chunk(15);
        $globalNo = 1;
    @endphp

    @foreach ($siswaChunks as $chunkIndex => $chunk)
        {{-- Cetak ulang Kop Surat di setiap halaman chunk --}}
        <table class="kop-surat">
            <tr>
                <td width="90" class="center">
                    @if ($base64Sm)
                        <img src="{{ $base64Sm }}" width="80">
                    @endif
                </td>
                <td class="kop-text">
                    <div class="kop-title-1">SEKOLAH MINGGU BUDDHA</div>
                    <div class="kop-title-2">VIHARA DHARMA CATTRA TABANAN</div>
                    <div class="kop-address">Jl. Melati No. 18, Tabanan - Bali. Telp. (0361) 811681</div>
                </td>
                <td width="90"></td>
            </tr>
        </table>
        <div class="garis-kop"></div>

        <div class="center bold" style="margin-bottom: 5px; font-size: 18px;">
            DAFTAR HADIR PESERTA DIDIK {{ $chunkIndex > 0 ? '(Lanjutan)' : '' }}
        </div>

        @if ($chunkIndex == 0)
            <div style="font-size: 15px; margin-bottom: 10px; font-family: Arial;">
                <strong>Keterangan:</strong> H = Hadir | I = Izin | S = Sakit | L = Libur | A = Alpa
            </div>
        @endif

        <table class="tabel-data">
            <thead>
                <tr>
                    <th rowspan="2" width="5%">NO</th>
                    <th rowspan="2" width="45%">NAMA SISWA</th>
                    <th rowspan="2" width="15%">TINGKATAN</th>
                    <th colspan="{{ $jumlahKolom }}">PERTEMUAN KE -</th>
                </tr>
                <tr>
                    @for ($i = 0; $i < $jumlahKolom; $i++)
                        <th>
                            {{ $i + 1 }}
                            <span class="tgl-kecil">
                                {{ isset($tanggals[$i]) ? \Carbon\Carbon::parse($tanggals[$i])->format('d M') : '-' }}
                            </span>
                        </th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @php $indexInChunk = 0; @endphp
                @foreach ($chunk as $siswa)
                    <tr>
                        <td>{{ $globalNo++ }}</td>
                        <td class="text-left">{{ $siswa->nama_lengkap }}</td>
                        <td>{{ preg_replace('/Kelas /i', '', $siswa->kelas_laporan) }}</td>

                        @for ($i = 0; $i < $jumlahKolom; $i++)
                            @if (isset($tanggals[$i]))
                                @php
                                    $tgl = $tanggals[$i];
                                    $a_id = $agendaIds[$i];
                                    $isLibur = $agendaStatusMap[$tgl] ?? false;
                                    $tglDaftar = \Carbon\Carbon::parse($siswa->created_at)->format('Y-m-d');
                                    $isBelumDaftar = $tgl < $tglDaftar;
                                @endphp

                                @if ($isLibur)
                                    {{-- Cetak Rowspan hanya di baris pertama pada halaman (chunk) ini --}}
                                    @if ($indexInChunk == 0)
                                        @php
                                            $agendaLibur = $agendas->firstWhere('id', $a_id);
                                            $desc = $agendaLibur ? $agendaLibur->nama_kegiatan : 'LIBUR';
                                            // Mengganti spasi dengan Enter (<br>) agar teks menurun kata demi kata
                                            $vertHTML = strtoupper(str_replace(' ', '<br>', $desc));
                                        @endphp
                                        <td rowspan="{{ count($chunk) }}" class="cell-libur">
                                            {!! $vertHTML !!}
                                        </td>
                                    @endif
                                @elseif ($isBelumDaftar)
                                    <td>-</td>
                                @else
                                    @php
                                        $status = $siswa->absen_map[$a_id] ?? null;
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
                    @php $indexInChunk++; @endphp
                @endforeach

                {{-- Baris Jumlah hanya tampil di halaman chunk terakhir --}}
                @if ($loop->last)
                    <tr>
                        <td colspan="3" class="text-left bold">JUMLAH KEHADIRAN (H)</td>
                        @for ($i = 0; $i < $jumlahKolom; $i++)
                            <td style="font-weight:bold;">
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

        <div class="page-break"></div>
    @endforeach

    {{-- HALAMAN ABSENSI GURU --}}
    <table class="kop-surat">
        <tr>
            <td width="90" class="center">
                @if ($base64Sm)
                    <img src="{{ $base64Sm }}" width="80">
                @endif
            </td>
            <td class="kop-text">
                <div class="kop-title-1">SEKOLAH MINGGU BUDDHA</div>
                <div class="kop-title-2">VIHARA DHARMA CATTRA TABANAN</div>
                <div class="kop-address">Jl. Melati No. 18, Tabanan - Bali. Telp. (0361) 811681</div>
            </td>
            <td width="90"></td>
        </tr>
    </table>
    <div class="garis-kop"></div>

    <div class="center bold" style="margin-bottom: 20px; font-size: 16px;">DAFTAR HADIR GURU</div>

    <table class="tabel-data" style="width: 90%; margin: 0 auto;">
        <thead>
            <tr>
                <th rowspan="2" width="10%">NO</th>
                <th rowspan="2" width="50%">NAMA</th>
                <th colspan="{{ $jumlahKolom }}">Pertemuan ke -</th>
            </tr>
            <tr>
                @for ($i = 0; $i < $jumlahKolom; $i++)
                    <th width="10%">{{ $i + 1 }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1.</td>
                <td class="text-left">{{ $pengajar->nama_lengkap }}</td>
                @for ($i = 0; $i < $jumlahKolom; $i++)
                    @php
                        $a_id = $agendaIds[$i] ?? null;
                        $tgl = $tanggals[$i] ?? null;
                        $isLibur = $agendaStatusMap[$tgl] ?? false;
                        $simbolGuru = '-';
                        if (!$isLibur && $a_id) {
                            $statusGuru = $absenPengajars[$a_id] ?? null;
                            if ($statusGuru == 'hadir') {
                                $simbolGuru = 'H';
                            } elseif ($statusGuru == 'izin') {
                                $simbolGuru = 'I';
                            } elseif ($statusGuru == 'sakit') {
                                $simbolGuru = 'S';
                            } else {
                                $simbolGuru = 'A';
                            }
                        }
                    @endphp
                    <td><strong>{{ $isLibur ? '-' : $simbolGuru }}</strong></td>
                @endfor
            </tr>
        </tbody>
    </table>

    <div class="ttd-container">
        <div class="ttd-left">
            Mengetahui,<br>
            Ketua SMB Vihara Dharma Cattra<br><br><br><br><br><br>
            ( {{ $namaKepalaSekolah }} )
        </div>
        <div class="ttd-right">
            Tabanan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
            Pengajar<br><br><br><br><br><br>
            ( {{ $pengajar->nama_lengkap }} )
        </div>
        <div class="clear"></div>
    </div>

    <div class="page-break"></div>

    {{-- HALAMAN DOKUMENTASI GAMBAR --}}
    <div class="center bold" style="margin-bottom: 30px; font-size: 16px;">LAMPIRAN DOKUMENTASI</div>

    <div class="img-grid">
        @foreach ($base64Images as $imgBase64)
            <div class="img-item">
                <img src="{{ $imgBase64 }}" alt="Dokumentasi">
            </div>
        @endforeach
    </div>

</body>

</html>
