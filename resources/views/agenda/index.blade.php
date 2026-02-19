<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Agenda & Jadwal Kegiatan') }}
            </h2>
            <div class="space-x-2 flex">

                <a href="{{ route('agenda.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                    + Tambah Jadwal
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                {{-- Pesan Sukses --}}
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Pesan Error --}}
                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        {{ session('error') }}
                    </div>
                @endif
                    {{-- Form Pencarian Tanggal --}}
                    <form id="searchForm" action="{{ route('agenda.index') }}" method="GET" class="mb-6 flex">
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}" autofocus placeholder="Cari tanggal kegiatan (Format: YYYY-MM-DD)..." class="w-full md:w-1/3 border-gray-300 rounded-l-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-r-md flex items-center justify-center transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </button>
                    </form>

                    {{-- Tabel Grouping Tanggal --}}
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="py-3 px-6">Tanggal Kegiatan</th>
                                    <th class="py-3 px-6">Total Rangkaian Acara</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($agendasGrouped as $group)
                                {{-- Baris bisa diklik untuk melihat detail --}}
                                <tr onclick="window.location='{{ route('agenda.showDate', $group->tanggal) }}'" class="bg-white border-b hover:bg-gray-100 cursor-pointer transition">
                                    <td class="py-4 px-6 font-bold text-gray-900 text-base">
                                        {{ \Carbon\Carbon::parse($group->tanggal)->translatedFormat('l, d F Y') }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="bg-blue-100 text-blue-800 font-semibold px-2.5 py-0.5 rounded border border-blue-400">
                                            {{ $group->total_kegiatan }} Kegiatan
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center text-blue-600 font-medium">
                                        Lihat Detail Rundown &rarr;
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="py-4 px-6 text-center text-gray-500">Belum ada jadwal kegiatan.</td>
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