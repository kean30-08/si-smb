<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Input Absensi (Grid Cepat)') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Centang kotak untuk "Hadir". Kosongkan untuk "Alpa".</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('absensi.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow-sm text-sm transition">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg border-t-4 border-emerald-500 overflow-hidden">

                {{-- NAVIGASI & PENCARIAN --}}
                <div
                    class="bg-gray-50 border-b border-gray-200 p-4 flex flex-col md:flex-row justify-between items-end gap-4">
                    <form action="{{ route('absensi.grid') }}" method="GET"
                        class="flex flex-col sm:flex-row items-end gap-4 w-full md:w-auto">
                        <div class="w-full sm:w-auto min-w-[250px]">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Pilih Bulan & Tahun:</label>
                            <div class="flex">
                                {{-- Input dengan sudut melengkung di kiri saja (rounded-l-md) --}}
                                <input type="month" name="bulan" value="{{ $bulanVal }}"
                                    class="w-full border-gray-300 rounded-l-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    onchange="this.form.submit()">

                                {{-- Tombol Cari menempel di kanan (rounded-r-md) --}}
                                <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-r-md transition shadow-sm flex items-center justify-center font-bold text-sm cursor-pointer">
                                    Cari
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- SEARCH BAR (Filter Klien/JS agar centangan tidak hilang) --}}
                    <div class="w-full md:w-1/3 flex">
                        <input type="text" id="cariGrid" placeholder="Cari Nama Siswa/Pengajar..."
                            class="w-full border-gray-300 rounded-l-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <button type="button" onclick="filterGrid()"
                            class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-r-md transition shadow-sm flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <polyline points="21 21 16.65 16.65"></polyline>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-0 overflow-x-auto">
                    @if ($agendas->isEmpty())
                        <div class="p-8 text-center text-gray-500">
                            Tidak ada jadwal kegiatan (Agenda) pada bulan ini.
                        </div>
                    @else
                        {{-- FORM PENYIMPANAN MASSAL --}}
                        <form action="{{ route('absensi.storeGrid') }}" method="POST" id="gridForm">
                            @csrf
                            <input type="hidden" name="bulan" value="{{ $bulanVal }}">

                            <table class="w-full text-sm text-left text-gray-600 min-w-max border-collapse">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-100 border-b-2 border-gray-300 sticky top-0 z-10">
                                    <tr>
                                        <th class="py-3 px-4 border-r border-gray-200 w-10 text-center">No</th>
                                        <th
                                            class="py-3 px-4 border-r border-gray-200 min-w-[200px] sticky left-0 bg-gray-100 z-20">
                                            Nama Siswa</th>

                                        {{-- KOLOM MINGGU --}}
                                        @foreach ($agendas as $index => $agenda)
                                            <th class="py-3 px-2 border-r border-gray-200 text-center min-w-[80px]">
                                                <div>M.{{ $index + 1 }}</div>
                                                <div class="font-normal text-[10px] text-gray-500 mt-1">
                                                    {{ \Carbon\Carbon::parse($agenda->tanggal)->format('d M y') }}
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $globalNo = 1;
                                        // Variabel untuk array siswa rahasia agar bisa menangkap siswa yang TIDAK dicentang sama sekali
                                        $allSiswaIds = [];
                                    @endphp

                                    @foreach ($siswaPerKelas as $namaKelas => $siswasKelas)
                                        {{-- Header Pemisah Kelas --}}
                                        <tr class="bg-gray-200/60 border-b border-gray-300">
                                            <td colspan="{{ 2 + count($agendas) }}"
                                                class="py-2 px-4 font-bold text-gray-800 text-xs">
                                                {{ strtoupper($namaKelas) }}
                                            </td>
                                        </tr>

                                        {{-- Baris Siswa --}}
                                        @foreach ($siswasKelas as $siswa)
                                            @php $allSiswaIds[] = $siswa->id; @endphp
                                            <tr
                                                class="border-b border-gray-100 hover:bg-emerald-50 transition grid-row-search">
                                                <td class="py-2 px-4 border-r border-gray-100 text-center">
                                                    {{ $globalNo++ }}</td>
                                                <td
                                                    class="py-2 px-4 font-bold text-gray-800 border-r border-gray-100 sticky left-0 bg-white z-10 row-name">
                                                    {{ $siswa->nama_lengkap }}
                                                </td>

                                                {{-- Kolom Checkbox Absensi --}}
                                                @foreach ($agendas as $agenda)
                                                    @php
                                                        // LOGIKA BARU
                                                        $tglDaftar = \Carbon\Carbon::parse($siswa->created_at)->format(
                                                            'Y-m-d',
                                                        );
                                                        $isBelumDaftar = $agenda->tanggal < $tglDaftar;
                                                    @endphp

                                                    @if ($agenda->is_libur)
                                                        <td
                                                            class="py-2 px-2 border-r border-gray-100 text-center bg-red-50 text-red-600 font-bold text-[10px] italic">
                                                            LIBUR
                                                        </td>
                                                    @elseif($isBelumDaftar)
                                                        <td class="py-2 px-2 border-r border-gray-100 text-center bg-gray-100 text-gray-300 font-bold"
                                                            title="Belum Terdaftar">
                                                            -
                                                        </td>
                                                    @else
                                                        @php
                                                            $status = $siswa->absen_map[$agenda->id] ?? null;
                                                        @endphp
                                                        <td
                                                            class="py-2 px-2 border-r border-gray-100 text-center cursor-pointer hover:bg-emerald-100 cell-clicker">
                                                            <input type="checkbox"
                                                                name="kehadiran[{{ $agenda->id }}][{{ $siswa->id }}]"
                                                                value="hadir"
                                                                {{ $status == 'hadir' ? 'checked' : '' }}
                                                                class="w-5 h-5 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 cursor-pointer pointer-events-none">
                                                        </td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @endforeach

                                    {{-- BAGIAN PENGAJAR DI BAWAH SISWA --}}
                                    @if (isset($pengajars) && $pengajars->isNotEmpty())
                                        <tr class="bg-indigo-200/80 border-b border-gray-300">
                                            <td colspan="{{ 2 + count($agendas) }}"
                                                class="py-3 px-4 font-black text-indigo-900 text-xs tracking-wider">
                                                DATA PENGAJAR / PENGURUS
                                            </td>
                                        </tr>
                                        @foreach ($pengajars as $p)
                                            <tr
                                                class="border-b border-gray-100 hover:bg-emerald-50 transition grid-row-search">
                                                <td class="py-2 px-4 border-r border-gray-100 text-center">
                                                    {{ $globalNo++ }}</td>
                                                <td
                                                    class="py-2 px-4 font-bold text-indigo-800 border-r border-gray-100 sticky left-0 bg-white z-10 row-name">
                                                    {{ $p->nama_lengkap }}
                                                    <span
                                                        class="block text-[10px] font-normal text-gray-500">{{ $p->jabatan->nama_jabatan ?? '-' }}</span>
                                                </td>
                                                @foreach ($agendas as $agenda)
                                                    @php $statusP = $p->absen_map[$agenda->id] ?? null; @endphp
                                                    @if ($agenda->is_libur)
                                                        <td
                                                            class="py-2 px-2 border-r border-gray-100 text-center bg-red-50 text-red-600 font-bold text-[10px] italic">
                                                            LIBUR</td>
                                                    @else
                                                        <td
                                                            class="py-2 px-2 border-r border-gray-100 text-center cursor-pointer hover:bg-emerald-100 cell-clicker">
                                                            <input type="checkbox"
                                                                name="kehadiran_pengajar[{{ $agenda->id }}][{{ $p->id }}]"
                                                                value="hadir"
                                                                {{ $statusP == 'hadir' ? 'checked' : '' }}
                                                                class="w-5 h-5 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 cursor-pointer pointer-events-none">
                                                        </td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>

                            {{-- Kirim semua ID rahasia ke controller agar sistem tahu siapa yang tidak dicentang --}}
                            @foreach ($allSiswaIds as $s_id)
                                <input type="hidden" name="siswa_ids[]" value="{{ $s_id }}">
                            @endforeach
                            @if (isset($pengajars))
                                @foreach ($pengajars as $p)
                                    <input type="hidden" name="pengajar_ids[]" value="{{ $p->id }}">
                                @endforeach
                            @endif

                            <div class="p-6 bg-gray-50 flex justify-end sticky bottom-0 border-t border-gray-200">
                                <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg flex items-center transition z-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                        <polyline points="17 21 17 13 7 13 7 21" />
                                        <polyline points="7 3 7 8 15 8" />
                                    </svg>
                                    Simpan Semua Absensi
                                </button>
                            </div>
                        </form>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Script Klik & Filter Grid --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cells = document.querySelectorAll('.cell-clicker');
            cells.forEach(cell => {
                cell.addEventListener('click', function() {
                    const checkbox = this.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.checked = !checkbox.checked;
                    }
                });
            });
        });

        // FUNGSI PENCARIAN CLIENT-SIDE (Sangat Cepat & Tidak menghilangkan centang user)
        function filterGrid() {
            const query = document.getElementById('cariGrid').value.toLowerCase();
            const rows = document.querySelectorAll('.grid-row-search');

            rows.forEach(row => {
                const name = row.querySelector('.row-name').textContent.toLowerCase();
                if (name.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Panggil fungsi search otomatis setiap user mengetik
        document.getElementById('cariGrid').addEventListener('keyup', filterGrid);
    </script>
</x-app-layout>
