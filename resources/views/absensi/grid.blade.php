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

                {{-- INFO BLOK: Penjelasan Fitur Grid --}}
                <div class="bg-emerald-50 border-b border-emerald-100 p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="h-5 w-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-emerald-800">Informasi Penggunaan Grid</h3>
                            <p class="text-sm text-emerald-700 mt-1">
                                Fitur Grid Absensi ini dirancang untuk memudahkan Anda <strong>mengisi kehadiran satu
                                    bulan penuh dengan sangat cepat</strong>. Sistem akan menampilkan seluruh jadwal
                                minggu dalam bulan yang Anda pilih secara berdampingan. Jika tidak mengisi absensi pada
                                minggu tertentu, maka secara otomatis siswa tersebut akan dianggap
                                <strong>Alpa</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- NAVIGASI & PENCARIAN --}}
                <div
                    class="bg-gray-50 border-b border-gray-200 p-4 flex flex-col md:flex-row justify-between items-end gap-4">
                    <form action="{{ route('absensi.grid') }}" method="GET"
                        class="flex flex-col sm:flex-row items-end gap-4 w-full md:w-auto">
                        <div class="w-full sm:w-auto min-w-[250px]">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Pilih Bulan & Tahun:</label>
                            <div class="flex">
                                <input type="month" name="bulan" value="{{ $bulanVal }}"
                                    class="w-full border-gray-300 rounded-l-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    onchange="this.form.submit()">

                                <button type="submit"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-r-md transition shadow-sm flex items-center justify-center font-bold text-sm cursor-pointer">
                                    Cari
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- SEARCH BAR --}}
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

                <div class="p-0 md:overflow-x-auto">
                    @if ($agendas->isEmpty())
                        <div class="p-8 text-center text-gray-500">
                            Tidak ada jadwal kegiatan (Agenda) pada bulan ini.
                        </div>
                    @else
                        {{-- FORM PENYIMPANAN MASSAL --}}
                        <form action="{{ route('absensi.storeGrid') }}" method="POST" id="gridForm">
                            @csrf
                            <input type="hidden" name="bulan" value="{{ $bulanVal }}">

                            {{-- TABEL RESPONSIVE --}}
                            <table class="w-full text-sm text-left text-gray-600 border-collapse">
                                <thead
                                    class="hidden md:table-header-group text-xs text-gray-700 uppercase bg-gray-100 border-b-2 border-gray-300 sticky top-0 z-10">
                                    <tr>
                                        <th class="py-3 px-4 border-r border-gray-200 w-10 text-center">No</th>
                                        <th
                                            class="py-3 px-4 border-r border-gray-200 min-w-[200px] sticky left-0 bg-gray-100 z-20">
                                            Nama Siswa
                                        </th>

                                        {{-- KOLOM MINGGU (Hanya muncul di Desktop) --}}
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

                                <tbody class="block md:table-row-group">
                                    @php
                                        $globalNo = 1;
                                        $allSiswaIds = [];
                                    @endphp

                                    @foreach ($siswaPerKelas as $namaKelas => $siswasKelas)
                                        {{-- Header Pemisah Kelas --}}
                                        <tr class="block md:table-row bg-gray-200/60 border-b border-gray-300">
                                            <td colspan="{{ 2 + count($agendas) }}"
                                                class="block md:table-cell py-2 px-4 font-bold text-gray-800 text-xs">
                                                {{ strtoupper($namaKelas) }}
                                            </td>
                                        </tr>

                                        {{-- Baris Siswa --}}
                                        @foreach ($siswasKelas as $siswa)
                                            @php $allSiswaIds[] = $siswa->id; @endphp
                                            <tr
                                                class="block md:table-row border-b md:border-gray-100 border-gray-300 hover:bg-emerald-50 transition grid-row-search p-4 md:p-0">

                                                {{-- No (Hilang di Mobile) --}}
                                                <td
                                                    class="hidden md:table-cell py-2 px-4 border-r border-gray-100 text-center">
                                                    {{ $globalNo++ }}
                                                </td>

                                                {{-- Nama Siswa (Block di Mobile agar di atas checkbox) --}}
                                                <td
                                                    class="block md:table-cell py-2 md:py-2 px-1 md:px-4 font-bold text-gray-800 border-b border-dashed md:border-solid md:border-b-0 md:border-r border-gray-200 md:border-gray-100 sticky md:left-0 bg-transparent md:bg-white z-10 row-name mb-3 md:mb-0 pb-3 md:pb-2">
                                                    <span
                                                        class="md:hidden text-xs text-gray-400 font-normal mr-1">#{{ $globalNo - 1 }}</span>
                                                    {{ $siswa->nama_lengkap }}
                                                </td>

                                                {{-- Label Pembantu di Mobile --}}
                                                <td class="block md:hidden text-xs text-gray-500 font-bold mb-2 px-1">
                                                    Checklist Kehadiran:
                                                </td>

                                                {{-- Kolom Checkbox Absensi (Inline-Block di Mobile agar menyamping ke bawah) --}}
                                                @foreach ($agendas as $index => $agenda)
                                                    @php
                                                        $tglDaftar = \Carbon\Carbon::parse($siswa->created_at)->format(
                                                            'Y-m-d',
                                                        );
                                                        $isBelumDaftar = $agenda->tanggal < $tglDaftar;
                                                        $status = $siswa->absen_map[$agenda->id] ?? null;
                                                    @endphp

                                                    @if ($agenda->is_libur)
                                                        <td
                                                            class="inline-block md:table-cell w-auto md:w-auto py-2 px-3 md:px-2 m-1 md:m-0 border md:border-0 border-gray-200 md:border-r md:border-gray-100 rounded md:rounded-none text-center bg-red-50 text-red-600 font-bold text-[10px] italic align-top">
                                                            <div class="md:hidden mb-1 font-bold text-red-400">
                                                                M.{{ $index + 1 }}</div>
                                                            LIBUR
                                                        </td>
                                                    @elseif($isBelumDaftar)
                                                        <td class="inline-block md:table-cell w-auto md:w-auto py-2 px-3 md:px-2 m-1 md:m-0 border md:border-0 border-gray-200 md:border-r md:border-gray-100 rounded md:rounded-none text-center bg-gray-100 text-gray-400 font-bold align-top"
                                                            title="Belum Terdaftar">
                                                            <div class="md:hidden mb-1 font-bold text-gray-400 text-xs">
                                                                M.{{ $index + 1 }}</div>
                                                            -
                                                        </td>
                                                    @else
                                                        <td
                                                            class="inline-block md:table-cell w-auto md:w-auto py-2 px-3 md:px-2 m-1 md:m-0 border md:border-0 border-gray-200 md:border-r md:border-gray-100 rounded-lg md:rounded-none bg-gray-50 md:bg-transparent text-center cursor-pointer hover:bg-emerald-100 cell-clicker align-top transition-colors">
                                                            {{-- Info Tanggal & Minggu untuk Mobile --}}
                                                            <div
                                                                class="md:hidden text-[10px] font-bold text-gray-500 mb-1">
                                                                M.{{ $index + 1 }} <br>
                                                                <span
                                                                    class="font-normal text-[9px]">{{ \Carbon\Carbon::parse($agenda->tanggal)->format('d M') }}</span>
                                                            </div>
                                                            <input type="checkbox"
                                                                name="kehadiran[{{ $agenda->id }}][{{ $siswa->id }}]"
                                                                value="hadir"
                                                                {{ $status == 'hadir' ? 'checked' : '' }}
                                                                class="w-6 h-6 md:w-5 md:h-5 text-emerald-600 bg-white md:bg-gray-100 border-gray-400 md:border-gray-300 rounded focus:ring-emerald-500 cursor-pointer pointer-events-none">
                                                        </td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @endforeach

                                    {{-- BAGIAN PENGAJAR DI BAWAH SISWA --}}
                                    @if (isset($pengajars) && $pengajars->isNotEmpty())
                                        <tr
                                            class="block md:table-row bg-indigo-200/80 border-b border-gray-300 mt-6 md:mt-0">
                                            <td colspan="{{ 2 + count($agendas) }}"
                                                class="block md:table-cell py-3 px-4 font-black text-indigo-900 text-xs tracking-wider">
                                                DATA PENGAJAR / PENGURUS
                                            </td>
                                        </tr>
                                        @foreach ($pengajars as $p)
                                            <tr
                                                class="block md:table-row border-b md:border-gray-100 border-gray-300 hover:bg-emerald-50 transition grid-row-search p-4 md:p-0">
                                                <td
                                                    class="hidden md:table-cell py-2 px-4 border-r border-gray-100 text-center">
                                                    {{ $globalNo++ }}
                                                </td>
                                                <td
                                                    class="block md:table-cell py-2 md:py-2 px-1 md:px-4 font-bold text-indigo-800 border-b border-dashed md:border-solid md:border-b-0 md:border-r border-gray-200 md:border-gray-100 sticky md:left-0 bg-transparent md:bg-white z-10 row-name mb-3 md:mb-0 pb-3 md:pb-2">
                                                    <span
                                                        class="md:hidden text-xs text-gray-400 font-normal mr-1">#{{ $globalNo - 1 }}</span>
                                                    {{ $p->nama_lengkap }}
                                                    <span
                                                        class="block text-[10px] font-normal text-gray-500 mt-0.5">{{ $p->jabatan->nama_jabatan ?? '-' }}</span>
                                                </td>

                                                <td class="block md:hidden text-xs text-gray-500 font-bold mb-2 px-1">
                                                    Checklist Kehadiran:
                                                </td>

                                                @foreach ($agendas as $index => $agenda)
                                                    @php $statusP = $p->absen_map[$agenda->id] ?? null; @endphp
                                                    @if ($agenda->is_libur)
                                                        <td
                                                            class="inline-block md:table-cell w-auto md:w-auto py-2 px-3 md:px-2 m-1 md:m-0 border md:border-0 border-gray-200 md:border-r md:border-gray-100 rounded md:rounded-none text-center bg-red-50 text-red-600 font-bold text-[10px] italic align-top">
                                                            <div class="md:hidden mb-1 font-bold text-red-400">
                                                                M.{{ $index + 1 }}</div>
                                                            LIBUR
                                                        </td>
                                                    @else
                                                        <td
                                                            class="inline-block md:table-cell w-auto md:w-auto py-2 px-3 md:px-2 m-1 md:m-0 border md:border-0 border-gray-200 md:border-r md:border-gray-100 rounded-lg md:rounded-none bg-gray-50 md:bg-transparent text-center cursor-pointer hover:bg-emerald-100 cell-clicker align-top transition-colors">
                                                            <div
                                                                class="md:hidden text-[10px] font-bold text-gray-500 mb-1">
                                                                M.{{ $index + 1 }} <br>
                                                                <span
                                                                    class="font-normal text-[9px]">{{ \Carbon\Carbon::parse($agenda->tanggal)->format('d M') }}</span>
                                                            </div>
                                                            <input type="checkbox"
                                                                name="kehadiran_pengajar[{{ $agenda->id }}][{{ $p->id }}]"
                                                                value="hadir"
                                                                {{ $statusP == 'hadir' ? 'checked' : '' }}
                                                                class="w-6 h-6 md:w-5 md:h-5 text-emerald-600 bg-white md:bg-gray-100 border-gray-400 md:border-gray-300 rounded focus:ring-emerald-500 cursor-pointer pointer-events-none">
                                                        </td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>

                            @foreach ($allSiswaIds as $s_id)
                                <input type="hidden" name="siswa_ids[]" value="{{ $s_id }}">
                            @endforeach
                            @if (isset($pengajars))
                                @foreach ($pengajars as $p)
                                    <input type="hidden" name="pengajar_ids[]" value="{{ $p->id }}">
                                @endforeach
                            @endif

                            <div
                                class="p-4 md:p-6 bg-gray-50 flex justify-end sticky bottom-0 border-t border-gray-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                                <button type="submit"
                                    class="w-full md:w-auto justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg flex items-center transition z-50">
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cells = document.querySelectorAll('.cell-clicker');
            cells.forEach(cell => {
                cell.addEventListener('click', function(e) {
                    // Mencegah double trigger jika user tidak sengaja klik tepat di kotaknya
                    if (e.target.tagName.toLowerCase() === 'input') return;

                    const checkbox = this.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.checked = !checkbox.checked;
                    }
                });
            });
        });

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

        document.getElementById('cariGrid').addEventListener('keyup', filterGrid);
    </script>
</x-app-layout>
