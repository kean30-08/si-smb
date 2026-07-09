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
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach ($items as $histori)
                                                    <tr class="hover:bg-gray-50 transition">
                                                        <td
                                                            class="py-3 px-4 font-bold text-indigo-700 whitespace-nowrap">
                                                            {{ $histori->tahunAjaran->tahun_ajaran }}
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
                                                    </tr>
                                                @endforeach
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
