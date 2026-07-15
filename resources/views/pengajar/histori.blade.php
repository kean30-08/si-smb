<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Histori Kehadiran Pengajar') }}: {{ $pengajar->nama_lengkap }}
                </h2>
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
                <a href="{{ route('pengajar.index') }}"
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
                        <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Keterangan Histori Pengajar</h3>
                        <p class="text-sm text-gray-500 mt-2">
                            Berikut adalah rekam jejak kehadiran pengajar yang diurutkan langsung berdasarkan
                            <strong>Tahun Ajaran</strong>.
                            Klik pada masing-masing Tahun Ajaran untuk melihat rincian Poin Kehadiran dan detail absensi
                            per bulannya.
                        </p>
                    </div>

                    <div x-data="{ activeAccordion: null }" class="space-y-4">
                        @forelse ($historiData as $ta => $histori)
                            <div class="border border-gray-200 rounded-lg bg-white overflow-hidden shadow-sm">
                                <button
                                    @click="activeAccordion === '{{ $ta }}' ? activeAccordion = null : activeAccordion = '{{ $ta }}'"
                                    class="w-full px-6 py-4 flex justify-between items-center bg-indigo-50 hover:bg-indigo-100 transition focus:outline-none">
                                    <span class="font-bold text-indigo-800 text-base md:text-lg">Tahun Ajaran:
                                        {{ $ta }}</span>
                                    <svg :class="{ 'rotate-180': activeAccordion === '{{ $ta }}' }"
                                        class="w-5 h-5 text-indigo-500 transform transition-transform duration-200"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="activeAccordion === '{{ $ta }}'" x-collapse x-cloak>
                                    <div class="p-0 md:p-6 overflow-x-auto">
                                        <table class="w-full text-sm text-left text-gray-600">
                                            <thead
                                                class="text-xs text-gray-500 uppercase bg-white border-b border-gray-200">
                                                <tr>
                                                    <th class="py-3 px-4 text-center whitespace-nowrap">Hadir</th>
                                                    <th class="py-3 px-4 text-center whitespace-nowrap">Sakit</th>
                                                    <th class="py-3 px-4 text-center whitespace-nowrap">Izin</th>
                                                    <th class="py-3 px-4 text-center whitespace-nowrap">Alpa</th>
                                                    <th class="py-3 px-4 text-center whitespace-nowrap">Total Poin</th>
                                                    <th class="py-3 px-4 text-center whitespace-nowrap">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100" x-data="{ detailBuka: false }">
                                                <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                                                    <td class="py-3 px-4 text-center font-medium">{{ $histori->hadir }}
                                                    </td>
                                                    <td class="py-3 px-4 text-center font-medium">{{ $histori->sakit }}
                                                    </td>
                                                    <td class="py-3 px-4 text-center font-medium">{{ $histori->izin }}
                                                    </td>
                                                    <td class="py-3 px-4 text-center font-medium text-red-500">
                                                        {{ $histori->alpa }}</td>
                                                    <td
                                                        class="py-3 px-4 text-center font-black text-indigo-600 text-base">
                                                        {{ $histori->poin }} <span
                                                            class="text-xs font-normal text-gray-400">Pts</span>
                                                    </td>
                                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                                        <button type="button" @click="detailBuka = !detailBuka"
                                                            class="text-indigo-500 hover:text-indigo-800 transition bg-indigo-100 px-3 py-1.5 rounded-full inline-flex items-center gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                height="16" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path
                                                                    d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                                                <circle cx="12" cy="12" r="3" />
                                                            </svg>
                                                            <span class="text-xs font-bold">Rincian</span>
                                                        </button>
                                                    </td>
                                                </tr>

                                                {{-- BARIS RINCIAN BULANAN --}}
                                                <tr x-show="detailBuka" x-cloak
                                                    class="bg-gray-50 border-b border-gray-200">
                                                    <td colspan="6" class="py-4 px-6">
                                                        <div class="text-sm text-gray-700">
                                                            <h4
                                                                class="font-bold mb-3 text-indigo-800 flex items-center">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    class="h-4 w-4 mr-1" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor"
                                                                    stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                                Rincian Kehadiran per Bulan
                                                            </h4>

                                                            @if (empty($histori->detail_absensi))
                                                                <p
                                                                    class="italic text-gray-500 bg-white p-3 rounded border">
                                                                    Belum ada data absensi yang tercatat untuk tahun
                                                                    ajaran ini.</p>
                                                            @else
                                                                <div
                                                                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                                    @foreach ($histori->detail_absensi as $bulan => $absensiBulan)
                                                                        <div
                                                                            class="bg-white p-3 rounded shadow-sm border border-gray-200">
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
                                                                                                $absen->agenda->is_libur
                                                                                            ) {
                                                                                                $teksStatus = 'LIBUR';
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
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Belum ada rekam jejak histori absensi untuk pengajar ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
