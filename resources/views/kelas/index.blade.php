<x-app-layout>
    <x-slot name="header">
        {{-- PERBAIKAN HEADER: Flex-col untuk HP, tombol memanjang --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Kelas') }}
            </h2>
            <div class="w-full sm:w-auto flex">
                <a href="{{ route('kelas.create') }}"
                    class="w-full sm:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                    + Tambah Kelas
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 bg-gray-50 md:bg-white">

                    {{-- Tabel Data --}}
                    <div class="w-full md:shadow-md md:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500">

                            {{-- HEADER TABEL (Sembunyi di HP) --}}
                            <thead class="hidden md:table-header-group text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="py-3 px-6">No</th>
                                    <th class="py-3 px-6">Nama Kelas</th>
                                    <th class="py-3 px-6">Jumlah Siswa</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>

                            {{-- BODY TABEL (Berubah jadi susunan kartu di HP) --}}
                            <tbody class="block md:table-row-group">
                                @forelse ($kelas as $index => $item)
                                    {{-- BARIS TABEL --}}
                                    <tr onclick="window.location='{{ route('siswa.index', ['kelas_id' => $item->id]) }}'"
                                        class="block md:table-row bg-white border border-gray-200 md:border-0 md:border-b hover:bg-gray-100 cursor-pointer transition mb-4 md:mb-0 rounded-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0">

                                        {{-- Kolom No (Disembunyikan di HP) --}}
                                        <td class="hidden md:table-cell py-4 px-6">{{ $index + 1 }}</td>

                                        {{-- Kolom Nama Kelas (Lebih besar di HP) --}}
                                        <td
                                            class="block md:table-cell py-2 md:py-4 px-2 md:px-6 border-b md:border-none border-dashed border-gray-200 mb-3 md:mb-0 pb-3 md:pb-4">
                                            <div class="font-bold text-gray-900 text-base md:text-sm whitespace-nowrap">
                                                {{ $item->nama_kelas }}</div>
                                        </td>

                                        {{-- Kolom Jumlah Siswa --}}
                                        <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6 mb-2 md:mb-0">
                                            <div class="flex items-center justify-between md:justify-start">
                                                <span
                                                    class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Jumlah
                                                    Siswa</span>
                                                <span
                                                    class="bg-blue-100 text-blue-800 font-semibold px-2.5 py-0.5 rounded border border-blue-400 text-sm">
                                                    {{ $item->nilai_kehadirans_count }} Siswa
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Kolom Aksi --}}
                                        <td class="block md:table-cell py-3 md:py-4 px-2 md:px-6 text-right md:text-center mt-2 md:mt-0 border-t md:border-none border-gray-50 pt-3 md:pt-4"
                                            onclick="event.stopPropagation();">
                                            <div class="flex justify-end md:justify-center space-x-5">
                                                {{-- Tombol Edit --}}
                                                <a href="{{ route('kelas.edit', $item->id) }}"
                                                    class="text-blue-500 hover:text-blue-700 transition p-1"
                                                    title="Edit Kelas">
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
                                                {{-- Tombol Hapus --}}
                                                <form action="{{ route('kelas.destroy', $item->id) }}" method="POST"
                                                    onsubmit="return confirm('Hapus kelas ini? Siswa di kelas ini akan kehilangan status kelasnya.');"
                                                    class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-500 hover:text-red-700 transition p-1"
                                                        title="Hapus Kelas">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                            height="20" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M3 6h18" />
                                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                                            <line x1="10" x2="10" y1="11"
                                                                y2="17" />
                                                            <line x1="14" x2="14" y1="11"
                                                                y2="17" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="block md:table-row bg-white border border-gray-200 rounded-lg p-4">
                                        <td colspan="4"
                                            class="block md:table-cell py-4 px-6 text-center text-gray-500">Belum ada
                                            data kelas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
