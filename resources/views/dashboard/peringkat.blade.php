<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Peringkat Kehadiran') }}
            </h2>
            <a href="{{ route('dashboard') }}"
                class="w-full sm:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="px-4 sm:px-6 lg:px-8">

            {{-- Bagian Filter & Pencarian --}}
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-gray-100">
                <form method="GET" action="{{ route('dashboard.peringkat') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    {{-- Search --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Cari Siswa</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Nama atau NIS..."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                    </div>

                    {{-- Rentang Waktu --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                    </div>

                    {{-- Urutan --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Urutkan Berdasarkan</label>
                        <div class="flex gap-2">
                            <select name="sort"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                <option value="desc" {{ $sort == 'desc' ? 'selected' : '' }}>Tertinggi ke Terendah
                                </option>
                                <option value="asc" {{ $sort == 'asc' ? 'selected' : '' }}>Terendah ke Tertinggi
                                </option>
                            </select>
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tabel Data --}}
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200">
                <div class="w-full overflow-x-auto">
                    <table class="w-full min-w-full divide-y divide-gray-200 table-fixed">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="w-16 px-4 md:px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    No</th>
                                <th scope="col"
                                    class="px-4 md:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Nama Siswa</th>
                                {{-- Kolom ini hanya tampil di Desktop (md:table-cell) --}}
                                <th scope="col"
                                    class="hidden md:table-cell px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Kelas</th>
                                <th scope="col"
                                    class="hidden md:table-cell px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Detail Kehadiran</th>
                                <th scope="col"
                                    class="px-4 md:px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Poin</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">

                            @php
                                // Menyesuaikan perhitungan total untuk paginasi
                                $isPaginated = method_exists($peringkat, 'total');
                                $totalSiswa = $isPaginated ? $peringkat->total() : count($peringkat);
                                $startIndex = $isPaginated ? ($peringkat->currentPage() - 1) * $peringkat->perPage() : 0;
                            @endphp

                            @forelse ($peringkat as $index => $siswa)
                                @php
                                    // Logika Peringkat Berkelanjutan: Jika 'asc' maka hitung mundur, jika 'desc' hitung maju
                                    $rank = $sort == 'asc' ? $totalSiswa - $startIndex - $index : $startIndex + $index + 1;
                                @endphp

                                <tr class="hover:bg-gray-50 transition">
                                    {{-- Peringkat --}}
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                        <div class="flex justify-center">
                                            <span
                                                class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shadow-sm
                                                @if ($rank == 1) bg-yellow-400 text-yellow-900 
                                                @elseif($rank == 2) bg-gray-300 text-gray-800 
                                                @elseif($rank == 3) bg-orange-300 text-orange-900 
                                                @else bg-gray-100 text-gray-600 @endif
                                            ">
                                                {{ $rank }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Nama Siswa (dan info tambahan untuk mobile) --}}
                                    <td class="px-4 md:px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $siswa->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-500">{{ $siswa->nis }}</div>

                                        {{-- INFO KHUSUS MOBILE (Muncul di layar kecil, hilang di layar besar) --}}
                                        <div class="block md:hidden mt-2 border-t border-gray-100 pt-2">
                                            <span
                                                class="px-2 mb-2 inline-block text-[10px] leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                {{ $siswa->historiAktif->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                                            </span>
                                            <div class="flex flex-wrap gap-1 text-[10px] font-bold">
                                                <span class="text-green-600 bg-green-50 px-1.5 py-1 rounded"
                                                    title="Hadir">H: {{ $siswa->total_hadir }}</span>
                                                <span class="text-yellow-600 bg-yellow-50 px-1.5 py-1 rounded"
                                                    title="Sakit">S: {{ $siswa->total_sakit }}</span>
                                                <span class="text-blue-600 bg-blue-50 px-1.5 py-1 rounded"
                                                    title="Izin">I: {{ $siswa->total_izin }}</span>
                                                <span class="text-red-600 bg-red-50 px-1.5 py-1 rounded"
                                                    title="Alpa">A: {{ $siswa->total_alpa }}</span>
                                                <span
                                                    class="text-gray-600 ml-1 py-1">({{ $siswa->persentase }}%)</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kelas (Desktop) --}}
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                            {{ $siswa->historiAktif->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                                        </span>
                                    </td>

                                    {{-- Detail Kehadiran (Desktop) --}}
                                    <td class="hidden md:table-cell px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center gap-2 text-xs font-bold">
                                            <span class="text-green-600 bg-green-50 px-2 py-1 rounded" title="Hadir">H:
                                                {{ $siswa->total_hadir }}</span>
                                            <span class="text-yellow-600 bg-yellow-50 px-2 py-1 rounded"
                                                title="Sakit">S: {{ $siswa->total_sakit }}</span>
                                            <span class="text-blue-600 bg-blue-50 px-2 py-1 rounded" title="Izin">I:
                                                {{ $siswa->total_izin }}</span>
                                            <span class="text-red-600 bg-red-50 px-2 py-1 rounded" title="Alpa">A:
                                                {{ $siswa->total_alpa }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1 font-medium">Tingkat Kehadiran:
                                            {{ $siswa->persentase }}%</div>
                                    </td>

                                    {{-- Total Poin --}}
                                    <td
                                        class="px-4 md:px-6 py-4 whitespace-nowrap text-center align-top md:align-middle">
                                        <span class="text-lg font-black text-indigo-600">
                                            {{ $siswa->poin_keaktifan }}
                                        </span>
                                        <span class="text-xs text-gray-400 block md:hidden">pts</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 italic">
                                        Tidak ada data yang ditemukan untuk kriteria pencarian ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Menampilkan Tombol Paginasi jika data menggunakan fitur Paginasi --}}
                @if(method_exists($peringkat, 'links'))
                    <div class="p-4 border-t border-gray-200">
                        {{ $peringkat->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>