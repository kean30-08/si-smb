<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nilai Siswa - Tahun Ajaran: ') }} {{ $tahunAktif->tahun_ajaran }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- KOTAK BACKGROUND PUTIH DITAMBAHKAN DI SINI --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-4 md:p-6 text-gray-900">

                    {{-- Form Pencarian & Filter Kelas --}}
                    <form method="GET" action="{{ route('nilai_kehadiran.index') }}"
                        class="mb-6 flex flex-col md:flex-row items-center gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">

                        {{-- Dropdown Filter Kelas --}}
                        <div class="w-full md:w-auto flex-shrink-0">
                            <select name="kelas_id" onchange="this.form.submit()"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-semibold text-gray-700">
                                <option value="">-- Semua Kelas --</option>
                                @foreach ($daftarKelas as $k)
                                    <option value="{{ $k->id }}" {{ $kelas_id == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Search Bar Nama/NIS --}}
                        <div class="flex-1 w-full">
                            <div class="flex">
                                <input type="text" name="search" value="{{ $search ?? '' }}"
                                    placeholder="Cari Nama atau NIS..."
                                    class="w-full border-gray-300 rounded-l-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm placeholder:italic">
                                <button type="submit"
                                    class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-r-md flex items-center justify-center transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8" />
                                        <path d="m21 21-4.3-4.3" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Tombol Reset --}}
                        @if (!empty($search) || !empty($kelas_id))
                            <div class="w-full md:w-auto">
                                <a href="{{ route('nilai_kehadiran.index') }}"
                                    class="block text-center bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 text-sm font-bold py-2 px-4 rounded-md transition shadow-sm">
                                    Reset
                                </a>
                            </div>
                        @endif
                    </form>

                    <div class="w-full">
                        <table class="w-full text-sm text-left text-gray-500">
                            {{-- HEADER TABEL --}}
                            <thead
                                class="hidden md:table-header-group text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th scope="col" class="py-3 px-6">No</th>
                                    <th scope="col" class="py-3 px-6">Nama Siswa</th>
                                    <th scope="col" class="py-3 px-6">NIS</th>
                                    <th scope="col" class="py-3 px-6">Kelas</th>
                                    <th scope="col" class="py-3 px-6 text-center">Total Poin</th>
                                    <th scope="col" class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>

                            {{-- BODY TABEL --}}
                            <tbody class="block md:table-row-group">
                                @forelse($nilai_siswas as $index => $nilai)
                                    <tr
                                        class="block md:table-row bg-white border border-gray-200 md:border-0 md:border-b hover:bg-gray-50 transition mb-4 md:mb-0 rounded-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0">

                                        <td class="hidden md:table-cell py-4 px-6">
                                            {{ $nilai_siswas->firstItem() + $index }}</td>

                                        <td
                                            class="block md:table-cell py-2 md:py-4 px-2 md:px-6 border-b md:border-none border-dashed border-gray-200 mb-3 md:mb-0 pb-3 md:pb-4">
                                            <div class="font-bold text-gray-900 text-base md:text-sm whitespace-nowrap">
                                                {{ $nilai->siswa->nama_lengkap ?? 'Siswa Terhapus' }}
                                            </div>
                                            <div class="text-xs text-gray-500 md:hidden mt-1 font-medium">NIS:
                                                {{ $nilai->siswa->nis ?? '-' }}</div>
                                        </td>

                                        <td class="hidden md:table-cell py-4 px-6">{{ $nilai->siswa->nis ?? '-' }}</td>

                                        <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6 mb-2 md:mb-0">
                                            <div class="flex items-center justify-between md:justify-start">
                                                <span
                                                    class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas</span>
                                                <span
                                                    class="text-sm text-gray-800">{{ $nilai->kelas->nama_kelas ?? 'Belum Ada Kelas' }}</span>
                                            </div>
                                        </td>

                                        <td
                                            class="block md:table-cell py-2 md:py-4 px-2 md:px-6 md:text-center mb-2 md:mb-0">
                                            <div class="flex items-center justify-between md:justify-center">
                                                <span
                                                    class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Total
                                                    Poin</span>
                                                <span
                                                    class="px-2 py-1 font-bold text-orange-600 bg-orange-50 rounded-full text-xs border border-orange-100">
                                                    {{ $nilai->total_poin }}
                                                </span>
                                            </div>
                                        </td>

                                        <td
                                            class="block md:table-cell py-3 md:py-4 px-2 md:px-6 text-right md:text-center mt-2 md:mt-0 border-t md:border-none border-gray-50 pt-3 md:pt-4">
                                            <div class="flex justify-end md:justify-center">
                                                {{-- TOMBOL EDIT (ICON PENCIL) --}}
                                                <a href="{{ route('nilai_kehadiran.edit', $nilai->id) }}"
                                                    class="text-blue-500 hover:text-blue-700 transition p-1"
                                                    title="Edit Manual">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                        height="20" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path
                                                            d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                        <path
                                                            d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="block md:table-row bg-white border border-gray-200 rounded-lg p-4">
                                        <td colspan="6"
                                            class="block md:table-cell py-4 px-6 text-center text-gray-500">
                                            Tidak ada data siswa yang cocok dengan pencarian/kelas tersebut.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $nilai_siswas->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
