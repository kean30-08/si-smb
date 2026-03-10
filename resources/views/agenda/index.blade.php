<x-app-layout>
    <x-slot name="header">
        {{-- Flex-col agar tombol turun ke bawah saat di HP --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Agenda & Jadwal Kegiatan') }}
            </h2>
            <div class="w-full sm:w-auto flex">
                {{-- w-full agar tombol memenuhi layar HP --}}
                <a href="{{ route('agenda.create') }}" class="w-full sm:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                    + Tambah Jadwal
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 bg-gray-50 md:bg-white">
                
                    {{-- Form Pencarian Tanggal --}}
                    <form id="searchForm" action="{{ route('agenda.index') }}" method="GET" class="mb-6 flex w-full">
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}" autofocus placeholder="Cari tanggal kegiatan..." class="w-full md:w-1/3 border-gray-300 rounded-l-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-r-md flex items-center justify-center transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </button>
                    </form>

                    {{-- Tabel Grouping Tanggal --}}
                    <div class="w-full md:shadow-md md:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500">
                            
                            {{-- HEADER TABEL (Sembunyi di HP) --}}
                            <thead class="hidden md:table-header-group text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="py-3 px-6">Tanggal Kegiatan</th>
                                    <th class="py-3 px-6">Total Rangkaian Acara</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            
                            {{-- BODY TABEL --}}
                            <tbody class="block md:table-row-group">
                                @forelse ($agendasGrouped as $group)
                                {{-- BARIS TABEL: Berubah jadi Kartu di HP --}}
                                <tr onclick="window.location='{{ route('agenda.showDate', $group->tanggal) }}'" class="block md:table-row bg-white border border-gray-200 md:border-0 md:border-b hover:bg-gray-100 cursor-pointer transition mb-4 md:mb-0 rounded-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0">
                                    
                                    {{-- TANGGAL --}}
                                    <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6 border-b md:border-none border-dashed border-gray-200 mb-3 md:mb-0 pb-3 md:pb-4 font-bold text-gray-900 text-base md:text-sm">
                                        {{ \Carbon\Carbon::parse($group->tanggal)->translatedFormat('l, d F Y') }}
                                    </td>
                                    
                                    {{-- TOTAL KEGIATAN --}}
                                    <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6">
                                        <div class="flex items-center justify-between md:justify-start">
                                            {{-- Label khusus untuk HP --}}
                                            <span class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Total Acara</span>
                                            <span class="bg-blue-100 text-blue-800 font-semibold px-2.5 py-0.5 rounded border border-blue-400">
                                                {{ $group->total_kegiatan }} Kegiatan
                                            </span>
                                        </div>
                                    </td>
                                    
                                    {{-- AKSI --}}
                                    <td class="block md:table-cell py-3 md:py-4 px-2 md:px-6 text-right md:text-center text-blue-600 font-medium mt-2 md:mt-0 border-t md:border-none border-gray-50 pt-3 md:pt-4">
                                        <span class="inline-flex items-center text-sm md:text-base">
                                            Lihat Detail Rundown &rarr;
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr class="block md:table-row bg-white border border-gray-200 rounded-lg p-4">
                                    <td colspan="3" class="block md:table-cell py-4 px-6 text-center text-gray-500">Belum ada jadwal kegiatan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $agendasGrouped->links() }}</div>

                </div>
            </div>
        </div>
    </div>

    {{-- Script Auto Submit Live Search --}}
    <script>
        let typingTimer;                
        let doneTypingInterval = 500;   
        let searchInput = document.getElementById('searchInput');
        let searchForm = document.getElementById('searchForm');

        searchInput.addEventListener('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => searchForm.submit(), doneTypingInterval);
        });
        searchInput.addEventListener('keydown', () => clearTimeout(typingTimer));

        window.onload = function() {
            let input = document.getElementById('searchInput');
            if (input.value.length > 0) {
                input.focus();
                let val = input.value;
                input.value = '';
                input.value = val;
            }
        }
    </script>
</x-app-layout>