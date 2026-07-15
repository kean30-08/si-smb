<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Histori Kehadiran') }}: {{ $siswa->nama_lengkap }} (NIS: {{ $siswa->nis }})
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

            <div class="w-full sm:w-auto flex">
                <a href="{{ route('siswa.show', $siswa->id) }}"
                    class="w-full sm:w-auto text-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow transition">
                    &larr; Kembali ke Profil
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Keterangan Histori Siswa</h3>
                        <p class="text-sm text-gray-500 mt-2">
                            Berikut adalah rekam jejak kehadiran siswa yang diurutkan berdasarkan tingkat
                            <strong>Kelas</strong> yang pernah diduduki. Klik pada masing-masing baris kelas untuk
                            melihat rincian Tahun Ajaran dan Poin Kehadiran. Untuk 1x Kehadiran <b>5 Poin</b>, 1x Sakit
                            atau Izin <b>1 Poin</b></b>, dan 1x Alpa <b>0 Poin</b>.
                        </p>
                    </div>

                    {{-- ACCORDION CONTAINER (Alpine.js) --}}
                    <div x-data="{ activeAccordion: null }" class="space-y-4">

                        @forelse ($historisGrouped as $kelasName => $items)
                            <div class="border border-gray-200 rounded-lg bg-white overflow-hidden shadow-sm">

                                {{-- Accordion Header (Tombol) --}}
                                <button
                                    @click="activeAccordion === '{{ $kelasName }}' ? activeAccordion = null : activeAccordion = '{{ $kelasName }}'"
                                    class="w-full px-6 py-4 flex justify-between items-center bg-gray-50 hover:bg-gray-100 transition focus:outline-none">

                                    <span
                                        class="font-bold text-gray-800 text-base md:text-lg">{{ $kelasName }}</span>

                                    {{-- Ikon Panah Putar --}}
                                    <svg :class="{ 'rotate-180': activeAccordion === '{{ $kelasName }}' }"
                                        class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                {{-- Accordion Body (Konten Tabel) --}}
                                <div x-show="activeAccordion === '{{ $kelasName }}'" x-collapse x-cloak>
                                    <div class="p-0 md:p-6 overflow-x-auto">
                                        <table class="w-full text-sm text-left text-gray-600">
                                            <thead
                                                class="text-xs text-gray-500 uppercase bg-white border-b border-gray-200">
                                                <tr>
                                                    <th class="py-3 px-4 whitespace-nowrap">Tahun Ajaran</th>
                                                    <th class="py-3 px-4 text-center whitespace-nowrap">Hadir</th>
                                                    <th class="py-3 px-4 text-center whitespace-nowrap">Sakit</th>
                                                    <th class="py-3 px-4 text-center whitespace-nowrap">Izin</th>
                                                    <th class="py-3 px-4 text-center whitespace-nowrap">Alpa</th>
                                                    <th class="py-3 px-4 text-center whitespace-nowrap">Total Poin</th>
                                                    {{-- TAMBAHAN KOLOM AKSI --}}
                                                    <th class="py-3 px-4 text-center whitespace-nowrap">Aksi</th>
                                                </tr>
                                            </thead>
                                            @foreach ($items as $histori)
                                                <tbody class="divide-y divide-gray-100" x-data="{ editing: false, detailBuka: false }">

                                                    {{-- ALPINE JS STATE UNTUK INLINE EDIT --}}
                                                    <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                                                        <td
                                                            class="py-3 px-4 font-bold text-indigo-700 whitespace-nowrap">

                                                            {{-- TAMPILAN NORMAL (Teks Biasa) --}}
                                                            <span
                                                                x-show="!editing">{{ $histori->tahunAjaran->tahun_ajaran ?? '-' }}</span>

                                                            {{-- TAMPILAN EDIT (Dropdown Form) --}}
                                                            <form x-show="editing"
                                                                action="{{ route('histori_siswa.update', $histori->id) }}"
                                                                method="POST" class="flex items-center gap-2" x-cloak>
                                                                @csrf
                                                                @method('PUT')

                                                                {{-- TAMBAHAN: DROPDOWN PILIH KELAS --}}
                                                                <select name="kelas_id"
                                                                    class="text-xs border-gray-300 rounded shadow-sm py-1 px-2 focus:ring-indigo-500 focus:border-indigo-500 font-semibold text-gray-700">
                                                                    @foreach (\App\Models\Kelas::all() as $k)
                                                                        <option value="{{ $k->id }}"
                                                                            {{ $histori->kelas_id == $k->id ? 'selected' : '' }}>
                                                                            {{ $k->nama_kelas }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>

                                                                {{-- DROPDOWN TAHUN AJARAN (YANG SUDAH ADA) --}}
                                                                <select name="tahun_ajaran_id"
                                                                    class="text-xs border-gray-300 rounded shadow-sm py-1 px-2 focus:ring-indigo-500 focus:border-indigo-500 font-semibold text-gray-700">
                                                                    @foreach ($semuaTahunAjaran as $ta)
                                                                        <option value="{{ $ta->id }}"
                                                                            {{ $histori->tahun_ajaran_id == $ta->id ? 'selected' : '' }}>
                                                                            {{ $ta->tahun_ajaran }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>

                                                                <button type="submit"
                                                                    class="p-1 bg-green-500 text-white rounded hover:bg-green-600 transition"
                                                                    title="Simpan Perubahan">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-4 w-4" viewBox="0 0 20 20"
                                                                        fill="currentColor">
                                                                        <path fill-rule="evenodd"
                                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                </button>
                                                                <button type="button" @click="editing = false"
                                                                    class="p-1 bg-gray-400 text-white rounded hover:bg-gray-500 transition"
                                                                    title="Batal">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-4 w-4" viewBox="0 0 20 20"
                                                                        fill="currentColor">
                                                                        <path fill-rule="evenodd"
                                                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                </button>
                                                            </form>

                                                        </td>
                                                        <td class="py-3 px-4 text-center font-medium">
                                                            {{ $histori->hadir }}</td>
                                                        <td class="py-3 px-4 text-center font-medium">
                                                            {{ $histori->sakit }}</td>
                                                        <td class="py-3 px-4 text-center font-medium">
                                                            {{ $histori->izin }}</td>
                                                        <td class="py-3 px-4 text-center font-medium text-red-500">
                                                            {{ $histori->alpa }}</td>
                                                        <td
                                                            class="py-3 px-4 text-center font-black text-indigo-600 text-base">
                                                            {{ $histori->poin }} <span
                                                                class="text-xs font-normal text-gray-400">Pts</span>
                                                        </td>

                                                        {{-- TOMBOL AKSI --}}
                                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                                            <div class="flex justify-center items-center gap-3"
                                                                x-show="!editing">
                                                                {{-- Tombol Lihat Rincian Bulanan --}}
                                                                <button type="button" @click="detailBuka = !detailBuka"
                                                                    class="text-indigo-500 hover:text-indigo-800 transition"
                                                                    title="Lihat Rincian Kehadiran Per Bulan">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="18" height="18"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round"
                                                                        stroke-linejoin="round">
                                                                        <path
                                                                            d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                                                        <circle cx="12" cy="12" r="3" />
                                                                    </svg>
                                                                </button>
                                                                {{-- Tombol Edit Tampil Inline --}}
                                                                <button type="button" @click="editing = true"
                                                                    class="text-blue-500 hover:text-blue-700 transition"
                                                                    title="Koreksi Tahun Ajaran">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="16" height="16"
                                                                        viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round"
                                                                        stroke-linejoin="round">
                                                                        <path d="M12 20h9" />
                                                                        <path
                                                                            d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                                                    </svg>
                                                                </button>

                                                                {{-- Tombol Hapus Histori --}}
                                                                <form
                                                                    action="{{ route('histori_siswa.destroy', $histori->id) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Hapus histori ini? Jika ini dihapus, riwayat kelas dan poin kehadiran siswa di tahun ajaran ini akan ikut lenyap.');"
                                                                    class="m-0 p-0 inline-block">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="text-red-500 hover:text-red-700 transition mt-1.5"
                                                                        title="Hapus Histori Kelas Ini">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            width="16" height="16"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            stroke="currentColor" stroke-width="2"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path d="M3 6h18" />
                                                                            <path
                                                                                d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                                                            <path
                                                                                d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                                                            <line x1="10" y1="11"
                                                                                x2="10" y2="17" />
                                                                            <line x1="14" y1="11"
                                                                                x2="14" y2="17" />
                                                                        </svg>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    {{-- BARIS RINCIAN BULANAN (Tersembunyi secara default) --}}
                                                    <tr x-show="detailBuka" x-cloak
                                                        class="bg-indigo-50 border-b border-indigo-200">
                                                        <td colspan="7" class="py-4 px-6">
                                                            <div class="text-sm text-gray-700">
                                                                <h4
                                                                    class="font-bold mb-3 text-indigo-800 flex items-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-4 w-4 mr-1" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor"
                                                                        stroke-width="2">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                    </svg>
                                                                    Rincian Kehadiran per Bulan
                                                                </h4>

                                                                @if (isset($histori->detail_absensi) && $histori->detail_absensi->isEmpty())
                                                                    <p
                                                                        class="italic text-gray-500 bg-white p-3 rounded border">
                                                                        Belum ada data absensi yang tercatat untuk tahun
                                                                        ajaran ini.</p>
                                                                @else
                                                                    <div
                                                                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                                        @foreach ($histori->detail_absensi as $bulan => $absensiBulan)
                                                                            <div
                                                                                class="bg-white p-3 rounded shadow-sm border border-indigo-100">
                                                                                <div
                                                                                    class="font-bold text-indigo-600 border-b pb-1 mb-2">
                                                                                    {{ $bulan }}</div>
                                                                                <ul class="space-y-1">
                                                                                    @foreach ($absensiBulan as $absen)
                                                                                        <li
                                                                                            class="flex justify-between items-center text-xs p-1 hover:bg-gray-50 rounded">
                                                                                            <span
                                                                                                class="font-medium text-gray-600">
                                                                                                {{ \Carbon\Carbon::parse($absen->agenda->tanggal)->translatedFormat('d M Y') }}
                                                                                                @if ($absen->agenda->is_libur)
                                                                                                    <span
                                                                                                        class="text-red-500 italic ml-1">(Libur)</span>
                                                                                                @endif
                                                                                            </span>

                                                                                            @php
                                                                                                if (
                                                                                                    $absen->agenda
                                                                                                        ->is_libur
                                                                                                ) {
                                                                                                    $teksStatus =
                                                                                                        'LIBUR';
                                                                                                    $bgStat =
                                                                                                        'bg-gray-100 text-gray-500 border border-gray-200';
                                                                                                } else {
                                                                                                    $teksStatus =
                                                                                                        $absen->status_kehadiran;
                                                                                                    $bgStat =
                                                                                                        'bg-gray-200 text-gray-700';
                                                                                                    if (
                                                                                                        $teksStatus ==
                                                                                                        'hadir'
                                                                                                    ) {
                                                                                                        $bgStat =
                                                                                                            'bg-green-100 text-green-700';
                                                                                                    } elseif (
                                                                                                        $teksStatus ==
                                                                                                        'sakit'
                                                                                                    ) {
                                                                                                        $bgStat =
                                                                                                            'bg-yellow-100 text-yellow-700';
                                                                                                    } elseif (
                                                                                                        $teksStatus ==
                                                                                                        'izin'
                                                                                                    ) {
                                                                                                        $bgStat =
                                                                                                            'bg-blue-100 text-blue-700';
                                                                                                    } elseif (
                                                                                                        $teksStatus ==
                                                                                                        'alpa'
                                                                                                    ) {
                                                                                                        $bgStat =
                                                                                                            'bg-red-100 text-red-700';
                                                                                                    }
                                                                                                }
                                                                                            @endphp
                                                                                            <span
                                                                                                class="px-2 py-0.5 rounded font-bold uppercase {{ $bgStat }}">
                                                                                                {{ $teksStatus }}
                                                                                            </span>

                                                                                        </li>
                                                                                    @endforeach
                                                                                </ul>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div
                                class="p-8 text-center text-gray-500 bg-gray-50 border border-dashed border-gray-300 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-3"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                Belum ada rekam jejak kelas & kehadiran untuk siswa ini.
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
