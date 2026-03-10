<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; } 
        table { width: 100%; border-collapse: collapse; margin-top: 10px; } 
        th, td { border: 1px solid #000; padding: 5px; text-align: center; } 
        th { background-color: #f2f2f2; } 
        .left { text-align: left; }
        /* Tambahan style untuk baris total */
        .row-total { background-color: #e6e6e6; font-weight: bold; }
        .text-right { text-align: right; padding-right: 10px; }
    </style>
</head>
<body>
    <h2 style="text-align: center; margin-bottom: 0;">Laporan Tingkat Keaktifan Siswa</h2>
    <p style="text-align: center; margin-top: 5px;">Periode: {{ \Carbon\Carbon::parse($mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($selesai)->format('d/m/Y') }} | Kelas: {{ $nama_kelas }}</p>
    
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

        {{-- BAGIAN REKAPITULASI TOTAL & RATA-RATA --}}
        @if($siswas->count() > 0)
        <tfoot>
            
            {{-- BARIS 1: TOTAL KESELURUHAN --}}
            <tr class="row-total">
                <td colspan="3" class="text-right">TOTAL KESELURUHAN</td>
                <td>{{ $siswas->sum('total_hadir') }}</td>
                <td>{{ $siswas->sum('total_izin') }}</td>
                <td>{{ $siswas->sum('total_sakit') }}</td>
                <td>{{ $siswas->sum('total_alpa') }}</td>
                <td>-</td> {{-- Strip, karena total persentase tidak logis --}}
            </tr>
            
            {{-- BARIS 2: RATA-RATA --}}
            <tr class="row-total">
                <td colspan="3" class="text-right">RATA-RATA</td>
                {{-- 
                    Kita tambahkan (float) dan round(..., 1) untuk mengamankan DomPDF dari crash
                    dan menampilkan maksimal 1 angka di belakang koma (misal: 2.5)
                --}}
                <td>{{ round((float) $siswas->avg('total_hadir'), 1) }}</td>
                <td>{{ round((float) $siswas->avg('total_izin'), 1) }}</td>
                <td>{{ round((float) $siswas->avg('total_sakit'), 1) }}</td>
                <td>{{ round((float) $siswas->avg('total_alpa'), 1) }}</td>
                {{-- Rata-rata persentase dibulatkan utuh tanpa koma --}}
                <td>{{ round((float) $siswas->avg('persentase')) }}%</td>
            </tr>
            
        </tfoot>
        @endif

    </table>
</body>
</html>