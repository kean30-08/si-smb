<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Data Kehadiran') }}
            </h2>
            @if ($type == 'siswa' && isset($selectedAgenda))
                {{-- PERBAIKAN: Tombol scanner membawa parameter agenda_id yang dipilih --}}
                <a href="{{ route('absensi.scanner', ['agenda_id' => $selectedAgenda->id]) }}"
                    class="w-full sm:w-auto justify-center bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded shadow transition flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="mr-2">
                        <rect width="18" height="18" x="3" y="3" rx="2" />
                        <path d="M7 7h.01" />
                        <path d="M17 7h.01" />
                        <path d="M7 17h.01" />
                        <path d="M17 17h.01" />
                        <path d="M12 7v10" />
                        <path d="M7 12h10" />
                    </svg>
                    Buka Kamera Scanner (Siswa)
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                {{-- NAVIGASI TAB MENU --}}
                <div class="border-b border-gray-200 bg-gray-50 pt-2 px-2 sm:px-4">
                    <ul class="flex flex-wrap -mb-px text-xs sm:text-sm font-medium text-center">
                        <li class="mr-1 sm:mr-2">
                            <a href="{{ route('absensi.index', ['type' => 'siswa', 'tanggal' => $tanggal, 'agenda_id' => $agenda_id]) }}"
                                class="inline-block p-3 sm:p-4 border-b-2 rounded-t-lg transition {{ $type == 'siswa' ? 'border-indigo-600 text-indigo-600 font-bold bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Absensi Siswa
                            </a>
                        </li>
                        <li class="mr-1 sm:mr-2">
                            <a href="{{ route('absensi.index', ['type' => 'pengajar', 'tanggal' => $tanggal, 'agenda_id' => $agenda_id]) }}"
                                class="inline-block p-3 sm:p-4 border-b-2 rounded-t-lg transition {{ $type == 'pengajar' ? 'border-amber-500 text-amber-600 font-bold bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Absensi Pengajar
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="p-4 sm:p-6 text-gray-900">

                    {{-- FILTER BARIS 1: TANGGAL, KELAS, CARI --}}
                    <form action="{{ route('absensi.index') }}" method="GET"
                        class="mb-4 flex flex-col lg:flex-row gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200 lg:items-end">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="hidden" name="agenda_id" value="{{ $agenda_id }}">

                        <div class="w-full lg:w-auto">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $tanggal }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                onchange="this.form.submit()">
                        </div>

                        @if ($type == 'siswa')
                            <div class="w-full lg:w-auto">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Kelas</label>
                                <select name="kelas_id"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    onchange="this.form.submit()">
                                    <option value="">Semua Kelas</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}"
                                            {{ $kelas_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- TAMPILAN NAMA PIC ABSENSI --}}
                        @if ($agendas->isNotEmpty() && $penanggungJawab)
                            <div class="w-full lg:w-auto hidden md:block">
                                <label class="block text-sm font-medium text-gray-700 mb-1">PIC Absensi</label>
                                <div
                                    class="px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-md text-indigo-800 font-semibold text-sm flex items-center h-[42px]">
                                    <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                                    {{ $penanggungJawab->nama_lengkap }}
                                </div>
                            </div>
                        @endif

                        {{-- SEARCH BAR --}}
                        <div class="flex-1 w-full lg:w-auto mt-2 lg:mt-0">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cari
                                {{ $type == 'siswa' ? 'Siswa' : 'Pengajar' }}</label>
                            <div class="flex">
                                <input type="text" name="search" value="{{ $search ?? '' }}"
                                    placeholder="Ketik nama..."
                                    class="w-full border-gray-300 rounded-l-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="submit"
                                    class="bg-black hover:bg-gray-700 text-white px-4 py-2 rounded-r-md font-medium flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                        <circle cx="11" cy="11" r="8" />
                                        <line x1="21" x2="16.65" y1="21" y2="16.65" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        @if (!empty($search))
                            <div class="flex justify-end w-full lg:w-auto">
                                <a href="{{ route('absensi.index', ['type' => $type, 'tanggal' => $tanggal, 'kelas_id' => $kelas_id, 'agenda_id' => $agenda_id]) }}"
                                    class="w-full lg:w-auto text-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium border border-indigo-600">Reset</a>
                            </div>
                        @endif
                    </form>

                    {{-- JIKA TIDAK ADA JADWAL --}}
                    @if ($agendas->isEmpty())
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-sm">
                            <p class="font-bold">Perhatian</p>
                            <p>Tidak ada jadwal kegiatan yang terdaftar pada tanggal
                                <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</strong>.
                            </p>
                        </div>
                    @else
                        {{-- FILTER BARIS 2: DETAIL AGENDA (BARU) --}}
                        <form action="{{ route('absensi.index') }}" method="GET"
                            class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                            <input type="hidden" name="type" value="{{ $type }}">
                            <input type="hidden" name="kelas_id" value="{{ $kelas_id }}">

                            {{-- Dropdown Nama Kegiatan --}}
                            <div class="md:col-span-1">
                                <label class="block text-sm font-bold text-indigo-700 mb-1">Pilih Sesi Kegiatan</label>
                                <select name="agenda_id"
                                    class="w-full border-indigo-300 bg-indigo-50 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-semibold text-sm"
                                    onchange="this.form.submit()">
                                    @foreach ($agendas as $agenda)
                                        <option value="{{ $agenda->id }}"
                                            {{ $agenda_id == $agenda->id ? 'selected' : '' }}>
                                            {{ $agenda->nama_kegiatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Status Read-Only --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                                <input type="text" disabled
                                    class="w-full rounded-md shadow-sm text-sm font-bold
                                    @if (!$selectedAgenda) bg-gray-100 text-gray-500 border-gray-200
                                    @elseif ($selectedAgenda->status == 'akan datang') bg-blue-100 text-blue-700 border-blue-300
                                    @elseif ($selectedAgenda->status == 'sedang berlangsung') bg-yellow-100 text-yellow-700 border-yellow-300
                                    @elseif ($selectedAgenda->status == 'selesai') bg-green-100 text-green-700 border-green-300
                                    @elseif ($selectedAgenda->status == 'batal') bg-red-100 text-red-700 border-red-300 @endif"
                                    value="{{ $selectedAgenda ? ucwords(str_replace('_', ' ', $selectedAgenda->status)) : '-' }}">
                            </div>

                            {{-- Waktu Mulai Read-Only --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Waktu Mulai</label>
                                <input type="text" disabled
                                    class="w-full border-gray-200 bg-gray-100 rounded-md shadow-sm text-sm text-gray-500"
                                    value="{{ $selectedAgenda ? \Carbon\Carbon::parse($selectedAgenda->waktu_mulai)->format('H:i') : '-' }}">
                            </div>

                            {{-- Waktu Selesai Read-Only --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Waktu Selesai</label>
                                <input type="text" disabled
                                    class="w-full border-gray-200 bg-gray-100 rounded-md shadow-sm text-sm text-gray-500"
                                    value="{{ $selectedAgenda && $selectedAgenda->waktu_selesai ? \Carbon\Carbon::parse($selectedAgenda->waktu_selesai)->format('H:i') : 'Selesai' }}">
                            </div>
                        </form>


                        {{-- ============================== --}}
                        {{-- TAB 1: KONTEN ABSENSI SISWA    --}}
                        {{-- ============================== --}}
                        @if ($type == 'siswa')
                            <div class="w-full md:border-t-2 md:border-indigo-500 md:shadow-md md:rounded-lg">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead
                                        class="hidden md:table-header-group text-xs text-gray-700 uppercase bg-indigo-50">
                                        <tr>
                                            <th class="py-3 px-4">No</th>
                                            <th class="py-3 px-4">Nama Siswa</th>
                                            <th class="py-3 px-4">Kelas</th>
                                            <th class="py-3 px-4 text-center">Status Kehadiran</th>
                                            <th class="py-3 px-4 text-center">Waktu Masuk</th>
                                        </tr>
                                    </thead>
                                    <tbody class="block md:table-row-group">
                                        @foreach ($siswas as $index => $siswa)
                                            @php $absenSiswa = $absensis->where('siswa_id', $siswa->id)->first(); @endphp
                                            <tr
                                                class="block md:table-row bg-white border border-gray-200 md:border-0 md:border-b hover:bg-gray-50 transition mb-4 md:mb-0 rounded-lg md:rounded-none p-4 md:p-0">
                                                <td class="hidden md:table-cell py-4 px-4">
                                                    {{ $siswas->firstItem() + $index }}</td>
                                                <td
                                                    class="block md:table-cell py-2 md:py-4 px-2 md:px-4 border-b md:border-none border-dashed border-gray-200 mb-3 md:mb-0 pb-3 md:pb-4">
                                                    <div class="font-bold text-gray-900 text-base md:text-sm">
                                                        {{ $siswa->nama_lengkap }}</div>
                                                    <div class="text-xs text-indigo-600 md:hidden mt-1 font-semibold">
                                                        {{ $siswa->kelas->nama_kelas ?? '-' }}</div>
                                                </td>
                                                <td class="hidden md:table-cell py-4 px-4">
                                                    {{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                                                <td
                                                    class="block md:table-cell py-2 md:py-4 px-2 md:px-4 md:text-center mb-2 md:mb-0">
                                                    <div class="flex items-center justify-between md:justify-center">
                                                        <span
                                                            class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Status
                                                            Absensi</span>
                                                        <form action="{{ route('absensi.manual') }}" method="POST"
                                                            class="m-0">
                                                            @csrf
                                                            <input type="hidden" name="siswa_id"
                                                                value="{{ $siswa->id }}">
                                                            <input type="hidden" name="agenda_id"
                                                                value="{{ $agenda_id }}"> {{-- PERBAIKAN PENTING DI SINI --}}
                                                            <input type="hidden" name="tanggal"
                                                                value="{{ $tanggal }}">
                                                            @php $statusSaatIni = $absenSiswa ? $absenSiswa->status_kehadiran : 'alpa'; @endphp
                                                            <select name="status" onchange="this.form.submit()"
                                                                class="text-xs font-bold rounded-full border-gray-300 shadow-sm cursor-pointer focus:ring-0
                                                                @if ($statusSaatIni == 'alpa') bg-red-100 text-red-800 border-red-200
                                                                @elseif($statusSaatIni == 'hadir') bg-green-100 text-green-800 border-green-200
                                                                @elseif($statusSaatIni == 'izin') bg-blue-100 text-blue-800 border-blue-200
                                                                @elseif($statusSaatIni == 'sakit') bg-yellow-100 text-yellow-800 border-yellow-200 @endif">
                                                                <option value="alpa" class="bg-white text-black"
                                                                    {{ $statusSaatIni == 'alpa' ? 'selected' : '' }}>
                                                                    Alpa</option>
                                                                <option value="hadir" class="bg-white text-black"
                                                                    {{ $statusSaatIni == 'hadir' ? 'selected' : '' }}>
                                                                    Hadir</option>
                                                                <option value="izin" class="bg-white text-black"
                                                                    {{ $statusSaatIni == 'izin' ? 'selected' : '' }}>
                                                                    Izin</option>
                                                                <option value="sakit" class="bg-white text-black"
                                                                    {{ $statusSaatIni == 'sakit' ? 'selected' : '' }}>
                                                                    Sakit</option>
                                                            </select>
                                                        </form>
                                                    </div>
                                                </td>
                                                <td
                                                    class="block md:table-cell py-2 md:py-4 px-2 md:px-4 md:text-center text-xs text-gray-600">
                                                    <div class="flex items-center justify-between md:justify-center">
                                                        <span
                                                            class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu
                                                            Kehadiran</span>
                                                        <div
                                                            class="text-right md:text-center font-medium text-gray-800 md:text-gray-600">
                                                            @if ($absenSiswa && $absenSiswa->waktu_hadir)
                                                                {{ \Carbon\Carbon::parse($absenSiswa->waktu_hadir)->format('H:i:s') }}
                                                                <span class="hidden md:inline"><br></span>
                                                                <span
                                                                    class="text-[10px] text-gray-400 ml-1 md:ml-0">({{ ucfirst($absenSiswa->metode_absen) }})</span>
                                                            @else
                                                                <span class="text-gray-400">-</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">{{ $siswas->links() }}</div>
                        @endif

                        {{-- ============================== --}}
                        {{-- TAB 2: KONTEN ABSENSI PENGAJAR --}}
                        {{-- ============================== --}}
                        @if ($type == 'pengajar')
                            <div class="w-full md:border-t-2 md:border-amber-500 md:shadow-md md:rounded-lg">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead
                                        class="hidden md:table-header-group text-xs text-gray-700 uppercase bg-amber-50">
                                        <tr>
                                            <th class="py-3 px-4">No</th>
                                            <th class="py-3 px-4">Nama Pengajar / Pengurus</th>
                                            <th class="py-3 px-4">Jabatan</th>
                                            <th class="py-3 px-4 text-center">Status Kehadiran</th>
                                            <th class="py-3 px-4 text-center">Waktu Masuk</th>
                                        </tr>
                                    </thead>
                                    <tbody class="block md:table-row-group">
                                        @foreach ($pengajars as $index => $pengajar)
                                            @php $absenPengajar = $absensiPengajars->where('pengajar_id', $pengajar->id)->first(); @endphp
                                            <tr
                                                class="block md:table-row bg-white border border-gray-200 md:border-0 md:border-b hover:bg-gray-50 transition mb-4 md:mb-0 rounded-lg md:rounded-none p-4 md:p-0">
                                                <td class="hidden md:table-cell py-4 px-4">
                                                    {{ $pengajars->firstItem() + $index }}</td>
                                                <td
                                                    class="block md:table-cell py-2 md:py-4 px-2 md:px-4 border-b md:border-none border-dashed border-gray-200 mb-3 md:mb-0 pb-3 md:pb-4">
                                                    <div class="font-bold text-gray-900 text-base md:text-sm">
                                                        {{ $pengajar->nama_lengkap }}</div>
                                                    <div class="text-xs text-amber-600 md:hidden mt-1 font-semibold">
                                                        {{ $pengajar->jabatan->nama_jabatan ?? '-' }}</div>
                                                </td>
                                                <td class="hidden md:table-cell py-4 px-4">
                                                    {{ $pengajar->jabatan->nama_jabatan ?? '-' }}</td>
                                                <td
                                                    class="block md:table-cell py-2 md:py-4 px-2 md:px-4 md:text-center mb-2 md:mb-0">
                                                    <div class="flex items-center justify-between md:justify-center">
                                                        <span
                                                            class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Status
                                                            Absensi</span>
                                                        <form action="{{ route('absensi.manualPengajar') }}"
                                                            method="POST" class="m-0">
                                                            @csrf
                                                            <input type="hidden" name="pengajar_id"
                                                                value="{{ $pengajar->id }}">
                                                            <input type="hidden" name="agenda_id"
                                                                value="{{ $agenda_id }}"> {{-- PERBAIKAN PENTING DI SINI --}}
                                                            <input type="hidden" name="tanggal"
                                                                value="{{ $tanggal }}">
                                                            @php $statusSaatIni = $absenPengajar ? $absenPengajar->status_kehadiran : 'alpa'; @endphp
                                                            <select name="status" onchange="this.form.submit()"
                                                                class="text-xs font-bold rounded-full border-gray-300 shadow-sm cursor-pointer focus:ring-0
                                                                @if ($statusSaatIni == 'alpa') bg-red-100 text-red-800 border-red-200
                                                                @elseif($statusSaatIni == 'hadir') bg-amber-100 text-amber-800 border-amber-200
                                                                @elseif($statusSaatIni == 'izin') bg-blue-100 text-blue-800 border-blue-200
                                                                @elseif($statusSaatIni == 'sakit') bg-yellow-100 text-yellow-800 border-yellow-200 @endif">
                                                                <option value="alpa" class="bg-white text-black"
                                                                    {{ $statusSaatIni == 'alpa' ? 'selected' : '' }}>
                                                                    Alpa</option>
                                                                <option value="hadir" class="bg-white text-black"
                                                                    {{ $statusSaatIni == 'hadir' ? 'selected' : '' }}>
                                                                    Hadir</option>
                                                                <option value="izin" class="bg-white text-black"
                                                                    {{ $statusSaatIni == 'izin' ? 'selected' : '' }}>
                                                                    Izin</option>
                                                                <option value="sakit" class="bg-white text-black"
                                                                    {{ $statusSaatIni == 'sakit' ? 'selected' : '' }}>
                                                                    Sakit</option>
                                                            </select>
                                                        </form>
                                                    </div>
                                                </td>
                                                <td
                                                    class="block md:table-cell py-2 md:py-4 px-2 md:px-4 md:text-center text-xs text-gray-600">
                                                    <div class="flex items-center justify-between md:justify-center">
                                                        <span
                                                            class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu
                                                            Kehadiran</span>
                                                        <div
                                                            class="text-right md:text-center font-medium text-gray-800 md:text-gray-600">
                                                            @if ($absenPengajar && $absenPengajar->waktu_hadir)
                                                                {{ \Carbon\Carbon::parse($absenPengajar->waktu_hadir)->format('H:i:s') }}
                                                            @else
                                                                <span class="text-gray-400">-</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">{{ $pengajars->links() }}</div>
                        @endif

                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
