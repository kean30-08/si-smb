<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; } 
        table { width: 100%; border-collapse: collapse; margin-top: 10px; } 
        th, td { border: 1px solid #000; padding: 5px; text-align: center; } 
        th { background-color: #f2f2f2; } 
        .left { text-align: left; }
        
        /* Tambahan style untuk baris total (Konsisten dengan laporan siswa) */
        .row-total { background-color: #e6e6e6; font-weight: bold; }
        .text-right { text-align: right; padding-right: 10px; }
    </style>
</head>
<body>
    <h2 style="text-align: center; margin-bottom: 0;">Laporan Data & Kehadiran Pengurus Vihara</h2>
    <p style="text-align: center; margin-top: 5px;">Periode: {{ \Carbon\Carbon::parse($mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($selesai)->format('d/m/Y') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th class="left">Nama Pengajar / Pengurus</th>
                <th>Jabatan</th>
                <th>Hadir</th>
                <th>Izin</th>
                <th>Sakit</th>
                <th>Alpa</th>
                <th>Persentase Keaktifan</th>
            </tr>
        </thead>
        <tbody>
            {{-- Menggunakan forelse agar lebih aman jika data kosong --}}
            @forelse ($pengajars as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="left">{{ $p->nama_lengkap }}</td>
                <td>{{ $p->jabatan ?? '-' }}</td>
                <td>{{ $p->total_hadir }}</td>
                <td>{{ $p->total_izin }}</td>
                <td>{{ $p->total_sakit }}</td>
                <td>{{ $p->total_alpa }}</td>
                <td><strong>{{ $p->persentase }}%</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="8">Tidak ada data pengajar/pengurus pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>

        {{-- BAGIAN REKAPITULASI TOTAL & RATA-RATA --}}
        @if($pengajars->count() > 0)
        <tfoot>
            
            {{-- BARIS 1: TOTAL KESELURUHAN --}}
            <tr class="row-total">
                <td colspan="3" class="text-right">TOTAL KESELURUHAN</td>
                <td>{{ $pengajars->sum('total_hadir') }}</td>
                <td>{{ $pengajars->sum('total_izin') }}</td>
                <td>{{ $pengajars->sum('total_sakit') }}</td>
                <td>{{ $pengajars->sum('total_alpa') }}</td>
                <td>-</td> {{-- Strip, karena total persentase tidak relevan --}}
            </tr>
            
            {{-- BARIS 2: RATA-RATA --}}
            <tr class="row-total">
                <td colspan="3" class="text-right">RATA-RATA</td>
                {{-- 
                    Kita gunakan (float) untuk mencegah Error 500 (null reference)
                    Lalu dibulatkan 1 angka di belakang koma menggunakan round(..., 1)
                --}}
                <td>{{ round((float) $pengajars->avg('total_hadir'), 1) }}</td>
                <td>{{ round((float) $pengajars->avg('total_izin'), 1) }}</td>
                <td>{{ round((float) $pengajars->avg('total_sakit'), 1) }}</td>
                <td>{{ round((float) $pengajars->avg('total_alpa'), 1) }}</td>
                
                {{-- Rata-rata persentase dibulatkan utuh tanpa koma --}}
                <td>{{ round((float) $pengajars->avg('persentase')) }}%</td>
            </tr>
            
        </tfoot>
        @endif

    </table>
</body>
</html>