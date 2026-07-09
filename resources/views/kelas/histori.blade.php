<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Histori & Statistik Kelas') }}
            </h2>
            <a href="{{ route('kelas.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm transition shadow-sm">
                &larr; Kembali ke Kelas
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">

                {{-- FORM FILTER TAHUN AJARAN --}}
                <div
                    class="mb-8 bg-indigo-50 border border-indigo-100 rounded-lg p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-indigo-900 text-lg">Pilih Tahun Ajaran</h3>
                        <p class="text-sm text-indigo-700">Pilih tahun ajaran untuk melihat perbandingan statistik antar
                            kelas.</p>
                    </div>

                    <form method="GET" action="{{ route('kelas.histori') }}" class="w-full md:w-1/3">
                        <select name="tahun_ajaran_id" onchange="this.form.submit()"
                            class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm font-semibold text-gray-700">
                            @if ($tahunAjarans->isEmpty())
                                <option value="">Belum ada Tahun Ajaran</option>
                            @endif
                            @foreach ($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $selectedTaId == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->tahun_ajaran }} {{ $ta->status == 'aktif' ? '(Aktif Saat Ini)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- INFORMASI STATISTIK BERBENTUK ACCORDION --}}
                <div class="space-y-4">
                    @forelse ($kelasList as $kelas)
                        <div x-data="{ open: false }"
                            class="border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow transition">
                            {{-- Accordion Header --}}
                            <button @click="open = !open"
                                class="w-full px-6 py-4 flex justify-between items-center bg-gray-50 hover:bg-indigo-50 transition-colors focus:outline-none">
                                <div class="flex items-center gap-3">
                                    <div class="bg-indigo-100 text-indigo-600 p-2 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <span class="font-bold text-gray-800 text-lg">{{ $kelas->nama_kelas }}</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded border border-blue-400">
                                        Total: {{ $kelas->jumlah_murid }}
                                    </span>
                                    <svg class="h-5 w-5 text-gray-500 transform transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </button>

                            {{-- Accordion Content (Table) --}}
                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-y-95"
                                x-transition:enter-end="opacity-100 transform scale-y-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 transform scale-y-100"
                                x-transition:leave-end="opacity-0 transform scale-y-95"
                                class="border-t border-gray-200 p-4 bg-white" style="display: none;">

                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left text-gray-600">
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-center">Total Murid Saat Ini
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center text-blue-600">Murid
                                                    Tambahan (Baru)</th>
                                                <th scope="col" class="px-6 py-3 text-center text-green-600">Status
                                                    Aktif</th>
                                                <th scope="col" class="px-6 py-3 text-center text-red-600">Status
                                                    Tidak Aktif / Lulus</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="bg-white hover:bg-gray-50 transition border-b border-gray-50">
                                                <td class="px-6 py-4 font-bold text-gray-900 text-center text-lg">
                                                    {{ $kelas->jumlah_murid }}
                                                </td>
                                                <td class="px-6 py-4 font-bold text-blue-600 text-center text-lg">
                                                    +{{ $kelas->murid_tambahan }}
                                                </td>
                                                <td class="px-6 py-4 font-bold text-green-600 text-center text-lg">
                                                    {{ $kelas->murid_aktif }}
                                                </td>
                                                <td class="px-6 py-4 font-bold text-red-500 text-center text-lg">
                                                    {{ $kelas->murid_tidak_aktif }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <a href="{{ route('kelas.histori.rincian', ['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $selectedTaId]) }}"
                                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                                    Lihat Rincian Daftar Siswa &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 bg-gray-50 rounded-lg border border-gray-200">
                            <p class="text-gray-500 font-medium">Belum ada data kelas yang terdaftar.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
