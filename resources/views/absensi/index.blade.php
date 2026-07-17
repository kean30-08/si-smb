<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Kelola Kehadiran Harian') }}
                </h2>
                {{-- INFO TAHUN AJARAN AKTIF --}}
                @php
                    $tahunAktif = \App\Models\TahunAjaran::where('status', 'aktif')->first();
                @endphp
                <p class="text-sm text-indigo-600 font-bold mt-1 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    TA Aktif Saat Ini: {{ $tahunAktif ? $tahunAktif->tahun_ajaran : 'Belum Ada TA Aktif' }}
                </p>
            </div>
            {{-- TOMBOL SCANNER HANYA MUNCUL JIKA USER ADALAH PIC / ADMIN --}}
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                {{-- TOMBOL INPUT GRID (BARU) --}}
                <a href="{{ route('absensi.grid') }}"
                    class="w-full sm:w-auto justify-center bg-emerald-600 hover:bg-emerald-800 text-white font-bold py-2 px-4 rounded shadow transition flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="mr-2">
                        <path d="M3 3h18v18H3z" />
                        <path d="M3 9h18" />
                        <path d="M3 15h18" />
                        <path d="M9 3v18" />
                        <path d="M15 3v18" />
                    </svg>
                    Input Cepat (Grid)
                </a>

                {{-- TOMBOL SCANNER --}}
                @if ($type == 'siswa' && $selectedAgenda && $isPic && !$isLibur)
                    <a href="{{ route('absensi.scanner', ['agenda_id' => $agenda_id]) }}" id="btnScanner"
                        class="w-full sm:w-auto justify-center bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded shadow transition flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="mr-2">
                            <rect width="18" height="18" x="3" y="3" rx="2" />
                            <path d="M7 7h.01" />
                            <path d="M17 7h.01" />
                            <path d="M7 17h.01" />
                            <path d="M17 17h.01" />
                            <path d="M12 7v10" />
                            <path d="M7 12h10" />
                        </svg>
                        Kamera Scanner
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                {{-- NAVIGASI TAB MENU --}}
                <div class="border-b border-gray-200 bg-gray-50 pt-2 px-2 sm:px-4">
                    <ul class="flex flex-wrap -mb-px text-xs sm:text-sm font-medium text-center">
                        <li class="mr-1 sm:mr-2">
                            <a href="{{ route('absensi.index', ['type' => 'siswa', 'tanggal' => $tanggal]) }}"
                                class="inline-block p-3 sm:p-4 border-b-2 rounded-t-lg transition {{ $type == 'siswa' ? 'border-indigo-600 text-indigo-600 font-bold bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Absensi Siswa
                            </a>
                        </li>
                        <li class="mr-1 sm:mr-2">
                            <a href="{{ route('absensi.index', ['type' => 'pengajar', 'tanggal' => $tanggal]) }}"
                                class="inline-block p-3 sm:p-4 border-b-2 rounded-t-lg transition {{ $type == 'pengajar' ? 'border-amber-500 text-amber-600 font-bold bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Absensi Pengajar
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="p-4 sm:p-6 text-gray-900">
                    {{-- BANNER HARI LIBUR --}}
                    @if ($isLibur)
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
                            <div class="flex items-center font-bold mb-1">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd" />
                                </svg>
                                Pemberitahuan Hari Libur
                            </div>
                            <p class="text-sm">
                                Kegiatan pada tanggal ini ditetapkan sebagai Hari Libur. Sistem secara otomatis
                                menonaktifkan fitur absensi.
                            </p>
                        </div>
                    @endif
                    {{-- INFORMASI TAHUN AJARAN & PERINGATAN BEDA TAHUN --}}
                    @php
                        $tahunAktif = \App\Models\TahunAjaran::where('status', 'aktif')->first();
                        $agendaTa = $selectedAgenda ? $selectedAgenda->tahunAjaran : null;

                        // Cek apakah agenda ini berada di tahun ajaran yang berbeda dengan tahun ajaran aktif saat ini
                        $isBedaTahun = $tahunAktif && $agendaTa && $tahunAktif->id !== $agendaTa->id;
                    @endphp

                    @if ($agendaTa)
                        <div class="mb-4">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Tahun Ajaran: {{ $agendaTa->tahun_ajaran }}
                            </span>
                        </div>

                        {{-- Peringatan Beda Tahun Ajaran --}}
                        @if ($isBedaTahun)
                            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
                                <div class="flex items-center font-bold mb-1">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Peringatan Keamanan Data!
                                </div>
                                <p class="text-sm">
                                    Tanggal ini berada pada <strong>Tahun Ajaran {{ $agendaTa->tahun_ajaran }}</strong>.
                                    Anda <strong>tidak dapat</strong> mengubah data absensi ini karena sistem saat ini
                                    berada pada Tahun Ajaran {{ $tahunAktif->tahun_ajaran }}.
                                </p>
                            </div>
                        @endif
                    @endif

                    {{-- PERINGATAN JADWAL LAMPAU --}}
                    @php
                        $isLewat = false;

                    @endphp

                    @if ($selectedAgenda)
                        @php
                            $tglAgenda = \Carbon\Carbon::parse($tanggal);
                            // Cek apakah tanggal sudah lewat dan bukan hari ini
                            $isLewat = $tglAgenda->isPast() && !$tglAgenda->isToday();
                        @endphp

                        @if ($isLewat)
                            <div
                                class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 rounded shadow-sm">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                                    <div class="mb-3 sm:mb-0">
                                        <div class="flex items-center font-bold">
                                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11H9v-2h2v2zm0-4H9V7h2v2z" />
                                            </svg>
                                            Informasi Data Historis
                                        </div>
                                        <p class="text-sm mt-1">
                                            Data kehadiran untuk tanggal
                                            <strong>{{ $tglAgenda->translatedFormat('d F Y') }}</strong> sudah berlalu.
                                        </p>
                                    </div>
                                    @if ($isPic)
                                        <button type="button" id="btnToggleEdit" onclick="toggleEditMode()"
                                            class="bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-bold py-2 px-4 rounded shadow transition whitespace-nowrap">
                                            Aktifkan Edit
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- FILTER TANGGAL, KELAS, CARI --}}
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
                        @if ($selectedAgenda && $penanggungJawab->isNotEmpty())
                            <div class="w-full lg:w-auto hidden md:block">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Penanggung Jawab
                                    Absensi</label>
                                <div
                                    class="px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-md text-indigo-800 font-semibold text-sm flex items-center min-h-[42px]">
                                    <i data-lucide="users" class="w-4 h-4 mr-2 shrink-0"></i>
                                    {{ $penanggungJawab->pluck('nama_lengkap')->join(', ') }}
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
                                <a href="{{ route('absensi.index', ['type' => $type, 'tanggal' => $tanggal, 'kelas_id' => $kelas_id]) }}"
                                    class="w-full lg:w-auto text-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium border border-indigo-600">Reset</a>
                            </div>
                        @endif
                    </form>

                    {{-- JIKA TIDAK ADA JANGKAR KEGIATAN --}}
                    @if (!$selectedAgenda)
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-sm">
                            <p class="font-bold">Perhatian</p>
                            <p>Tidak ada jadwal terdaftar pada tanggal
                                <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</strong>.
                                Pastikan setidaknya ada satu agenda pada hari ini agar sistem dapat menyimpan data
                                absensi.
                            </p>
                        </div>
                    @else
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
                                            @php
                                                $absenSiswa = $absensis->where('siswa_id', $siswa->id)->first();
                                                $statusSaatIni = $absenSiswa ? $absenSiswa->status_kehadiran : 'alpa';

                                                // Ambil nama kelas tepat di tahun ajaran agenda ini
                                                $historiLaporan = $siswa->riwayatHistori->first();
                                                $namaKelasLaporan =
                                                    $historiLaporan && $historiLaporan->kelas
                                                        ? $historiLaporan->kelas->nama_kelas
                                                        : '-';
                                            @endphp

                                            <tr
                                                class="block md:table-row bg-white border border-gray-200 md:border-0 md:border-b hover:bg-gray-50 transition mb-4 md:mb-0 rounded-lg md:rounded-none p-4 md:p-0">
                                                <td class="hidden md:table-cell py-4 px-4">
                                                    {{ $siswas->firstItem() + $index }}</td>
                                                <td
                                                    class="block md:table-cell py-2 md:py-4 px-2 md:px-4 border-b md:border-none border-dashed border-gray-200 mb-3 md:mb-0 pb-3 md:pb-4">
                                                    <div class="font-bold text-gray-900 text-base md:text-sm">
                                                        {{ $siswa->nama_lengkap }}</div>
                                                    <div class="text-xs text-indigo-600 md:hidden mt-1 font-semibold">
                                                        {{ $namaKelasLaporan }}
                                                    </div>
                                                </td>
                                                <td
                                                    class="hidden md:table-cell py-4 px-4 font-semibold text-indigo-600">
                                                    {{ $namaKelasLaporan }}</td>

                                                {{-- STATUS KEHADIRAN (FORM PER BARIS) --}}
                                                <td
                                                    class="block md:table-cell py-2 md:py-4 px-2 md:px-4 md:text-center mb-2 md:mb-0">
                                                    <div class="flex items-center justify-between md:justify-center">
                                                        <span
                                                            class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Status
                                                            Absensi</span>

                                                        @if ($isLibur)
                                                            {{-- TAMPILAN JIKA HARI LIBUR --}}
                                                            <div
                                                                class="px-3 py-1 rounded-full text-xs font-bold shadow-sm text-center bg-red-100 text-red-800 border border-red-200">
                                                                LIBUR
                                                            </div>
                                                        @elseif ($isPic)
                                                            <form action="{{ route('absensi.manual') }}"
                                                                method="POST" class="m-0 w-full sm:w-auto">
                                                                @csrf
                                                                <input type="hidden" name="siswa_id"
                                                                    value="{{ $siswa->id }}">
                                                                <input type="hidden" name="agenda_id"
                                                                    value="{{ $agenda_id }}">
                                                                <input type="hidden" name="tanggal"
                                                                    value="{{ $tanggal }}">

                                                                <select name="status"
                                                                    onchange="simpanAbsenOtomatis(this)"
                                                                    {{ $isLewat || $isBedaTahun ? 'disabled' : '' }}
                                                                    class="status-dropdown text-xs font-bold rounded-full border-gray-300 shadow-sm cursor-pointer focus:ring-0
                                                                    @if ($statusSaatIni == 'alpa') bg-red-100 text-red-800 border-red-200
                                                                    @elseif($statusSaatIni == 'hadir') bg-green-100 text-green-800 border-green-200
                                                                    @elseif($statusSaatIni == 'izin') bg-blue-100 text-blue-800 border-blue-200
                                                                    @elseif($statusSaatIni == 'sakit') bg-yellow-100 text-yellow-800 border-yellow-200 @endif"
                                                                    style="opacity: {{ $isLewat || $isBedaTahun ? '0.5' : '1' }};">
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
                                                        @else
                                                            <div
                                                                class="px-3 py-1 rounded-full text-xs font-bold border shadow-sm text-center
                                                                @if ($statusSaatIni == 'alpa') bg-red-100 text-red-800 border-red-200
                                                                @elseif($statusSaatIni == 'hadir') bg-green-100 text-green-800 border-green-200
                                                                @elseif($statusSaatIni == 'izin') bg-blue-100 text-blue-800 border-blue-200
                                                                @elseif($statusSaatIni == 'sakit') bg-yellow-100 text-yellow-800 border-yellow-200 @endif">
                                                                {{ ucfirst($statusSaatIni) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>

                                                {{-- WAKTU KEHADIRAN --}}
                                                <td
                                                    class="block md:table-cell py-2 md:py-4 px-2 md:px-4 md:text-center text-xs text-gray-600">
                                                    <div class="flex items-center justify-between md:justify-center">
                                                        <span
                                                            class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu
                                                            Kehadiran</span>
                                                        <div
                                                            class="text-right md:text-center font-medium text-gray-800 md:text-gray-600 waktu-container">
                                                            @if ($absenSiswa && $absenSiswa->waktu_hadir)
                                                                @php $dt = \Carbon\Carbon::parse($absenSiswa->waktu_hadir); @endphp
                                                                <span
                                                                    class="block md:inline">{{ $dt->format('d/m/Y') }}</span>
                                                                <span
                                                                    class="hidden md:inline mx-1 text-gray-400 font-light">|</span>
                                                                <span
                                                                    class="block md:inline">{{ $dt->format('H:i:s') }}</span>
                                                                <div class="text-[10px] text-gray-400 mt-1">
                                                                    ({{ ucfirst($absenSiswa->metode_absen) }})
                                                                </div>
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
                                            @php
                                                $absenPengajar = $absensiPengajars
                                                    ->where('pengajar_id', $pengajar->id)
                                                    ->first();
                                                $statusSaatIni = $absenPengajar
                                                    ? $absenPengajar->status_kehadiran
                                                    : 'alpa';
                                            @endphp
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

                                                {{-- STATUS KEHADIRAN (FORM PER BARIS) --}}
                                                <td
                                                    class="block md:table-cell py-2 md:py-4 px-2 md:px-4 md:text-center mb-2 md:mb-0">
                                                    <div class="flex items-center justify-between md:justify-center">
                                                        <span
                                                            class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Status
                                                            Absensi</span>

                                                        @if ($isLibur)
                                                            {{-- TAMPILAN JIKA HARI LIBUR --}}
                                                            <div
                                                                class="px-3 py-1 rounded-full text-xs font-bold shadow-sm text-center bg-red-100 text-red-800 border border-red-200">
                                                                LIBUR
                                                            </div>
                                                        @elseif ($isPic)
                                                            <form action="{{ route('absensi.manualPengajar') }}"
                                                                method="POST" class="m-0 w-full sm:w-auto">
                                                                @csrf
                                                                <input type="hidden" name="pengajar_id"
                                                                    value="{{ $pengajar->id }}">
                                                                <input type="hidden" name="agenda_id"
                                                                    value="{{ $agenda_id }}">
                                                                <input type="hidden" name="tanggal"
                                                                    value="{{ $tanggal }}">

                                                                {{-- KUNCI PERBAIKAN DROPDOWN: Hapus 'disabled' hardcoded, gunakan kondisi $isLewat --}}
                                                                <select name="status"
                                                                    onchange="simpanAbsenOtomatis(this)"
                                                                    {{ $isLewat ? 'disabled' : '' }}
                                                                    class="status-dropdown text-xs font-bold rounded-full border-gray-300 shadow-sm cursor-pointer focus:ring-0
                                                                    @if ($statusSaatIni == 'alpa') bg-red-100 text-red-800 border-red-200
                                                                    @elseif($statusSaatIni == 'hadir') bg-green-100 text-green-800 border-green-200
                                                                    @elseif($statusSaatIni == 'izin') bg-blue-100 text-blue-800 border-blue-200
                                                                    @elseif($statusSaatIni == 'sakit') bg-yellow-100 text-yellow-800 border-yellow-200 @endif"
                                                                    style="opacity: {{ $isLewat ? '0.5' : '1' }};">
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
                                                        @else
                                                            <div
                                                                class="px-3 py-1 rounded-full text-xs font-bold border shadow-sm text-center
                                                                @if ($statusSaatIni == 'alpa') bg-red-100 text-red-800 border-red-200
                                                                @elseif($statusSaatIni == 'hadir') bg-amber-100 text-amber-800 border-amber-200
                                                                @elseif($statusSaatIni == 'izin') bg-blue-100 text-blue-800 border-blue-200
                                                                @elseif($statusSaatIni == 'sakit') bg-yellow-100 text-yellow-800 border-yellow-200 @endif">
                                                                {{ ucfirst($statusSaatIni) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>

                                                {{-- WAKTU KEHADIRAN --}}
                                                <td
                                                    class="block md:table-cell py-2 md:py-4 px-2 md:px-4 md:text-center text-xs text-gray-600">
                                                    <div class="flex items-center justify-between md:justify-center">
                                                        <span
                                                            class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu
                                                            Kehadiran</span>
                                                        <div
                                                            class="text-right md:text-center font-medium text-gray-800 md:text-gray-600 waktu-container">
                                                            @if ($absenPengajar && $absenPengajar->waktu_hadir)
                                                                @php $dt = \Carbon\Carbon::parse($absenPengajar->waktu_hadir); @endphp
                                                                <span
                                                                    class="block md:inline">{{ $dt->format('d/m/Y') }}</span>
                                                                <span
                                                                    class="hidden md:inline mx-1 text-gray-400 font-light">|</span>
                                                                <span
                                                                    class="block md:inline">{{ $dt->format('H:i:s') }}</span>
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

    {{-- SCRIPT UNTUK MEMPERTAHANKAN STATUS EDIT MODE --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnToggleEdit = document.getElementById('btnToggleEdit');

            // Jika tidak ada tombol "Aktifkan Edit" (berarti hari ini/mendatang), 
            // Jangan jalankan applyEditMode() agar dropdown terbuka otomatis.
            if (!btnToggleEdit) {
                localStorage.removeItem('editMode'); // Bersihkan state sisa dari hari lampau
                return;
            }

            const isEditMode = localStorage.getItem('editMode') === 'true';
            applyEditMode(isEditMode);
        });

        function toggleEditMode() {
            const isCurrentlyEnabled = localStorage.getItem('editMode') === 'true';
            const newState = !isCurrentlyEnabled;
            localStorage.setItem('editMode', newState);
            applyEditMode(newState);
        }

        function applyEditMode(enabled) {
            const dropdowns = document.querySelectorAll('.status-dropdown');
            const btnToggleEdit = document.getElementById('btnToggleEdit');
            const btnScanner = document.getElementById('btnScanner');

            dropdowns.forEach(el => {
                el.disabled = !enabled;
                el.style.opacity = enabled ? "1" : "0.5";
            });

            if (btnToggleEdit) {
                btnToggleEdit.innerText = enabled ? 'Tutup Mode Edit' : 'Aktifkan Edit';
                btnToggleEdit.className = enabled ?
                    'bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2 px-3 rounded shadow transition' :
                    'bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-bold py-2 px-3 rounded shadow transition';
            }

            if (btnScanner) {
                btnScanner.style.display = enabled ? 'flex' : 'none';
            }
        }
    </script>
    {{-- SCRIPT UNTUK AJAX SUBMIT ABSENSI TANPA RELOAD --}}
    <script>
        function simpanAbsenOtomatis(selectElement) {
            const form = selectElement.closest('form');
            const formData = new FormData(form);
            const url = form.action;

            // Beri efek transparan sedikit saat sedang loading menyimpan
            selectElement.style.opacity = '0.5';

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Penting agar Laravel tahu ini AJAX
                    }
                })
                .then(response => response.json())
                .then(data => {
                    selectElement.style.opacity = '1';

                    if (data.success) {
                        // 1. UBAH WARNA DROPDOWN
                        // Bersihkan class warna lama
                        selectElement.classList.remove(
                            'bg-red-100', 'text-red-800', 'border-red-200',
                            'bg-green-100', 'text-green-800', 'border-green-200',
                            'bg-blue-100', 'text-blue-800', 'border-blue-200',
                            'bg-yellow-100', 'text-yellow-800', 'border-yellow-200'
                        );

                        // Pasang class warna baru
                        if (data.status === 'alpa') {
                            selectElement.classList.add('bg-red-100', 'text-red-800', 'border-red-200');
                        } else if (data.status === 'hadir') {
                            selectElement.classList.add('bg-green-100', 'text-green-800', 'border-green-200');
                        } else if (data.status === 'izin') {
                            selectElement.classList.add('bg-blue-100', 'text-blue-800', 'border-blue-200');
                        } else if (data.status === 'sakit') {
                            selectElement.classList.add('bg-yellow-100', 'text-yellow-800', 'border-yellow-200');
                        }

                        // 2. UBAH TEKS WAKTU HADIR (Jam & Tanggal)
                        const tr = selectElement.closest('tr');
                        const waktuContainer = tr.querySelector('.waktu-container');

                        if (waktuContainer) {
                            if (data.status === 'hadir' && data.waktu_hadir) {
                                waktuContainer.innerHTML = `
                                <span class="block md:inline">${data.tanggal}</span>
                                <span class="hidden md:inline mx-1 text-gray-400 font-light">|</span>
                                <span class="block md:inline">${data.waktu_hadir}</span>
                                <div class="text-[10px] text-gray-400 mt-1">(${data.metode})</div>
                            `;
                            } else {
                                // Jika diganti menjadi alpa/izin/sakit, hilangkan jamnya menjadi strip
                                waktuContainer.innerHTML = `<span class="text-gray-400">-</span>`;
                            }
                        }
                    } else {
                        alert(data.message || 'Terjadi kesalahan saat menyimpan data.');
                    }
                })
                .catch(error => {
                    selectElement.style.opacity = '1';
                    console.error('Error:', error);
                    alert('Gagal menghubungi server. Periksa koneksi Anda.');
                });
        }
    </script>
</x-app-layout>
