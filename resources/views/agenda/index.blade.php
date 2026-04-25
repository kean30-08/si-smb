<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Agenda & Jadwal Kegiatan') }}
            </h2>
            @if ($isAdmin)
                <div class="w-full sm:w-auto flex">
                    <a href="{{ route('agenda.create') }}"
                        class="w-full sm:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                        + Tambah Jadwal
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 bg-gray-50 md:bg-white">

                    {{-- Form Pencarian & Filter Tahun Ajaran --}}
                    <form id="searchForm" action="{{ route('agenda.index') }}" method="GET"
                        class="mb-6 flex flex-col md:flex-row items-center gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">

                        {{-- Dropdown Filter Tahun Ajaran --}}
                        <div class="flex-1 w-full">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Filter Tahun Ajaran:</label>
                            <select id="tahunSelect" name="tahun_ajaran_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-semibold text-indigo-700">
                                <option value="">-- Semua Tahun Ajaran --</option>
                                @foreach ($tahunAjarans as $ta)
                                    <option value="{{ $ta->id }}" {{ $filterTahun == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }} {{ $ta->status == 'aktif' ? '(Sedang Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Search Bar Tanggal --}}
                        <div class="flex-1 w-full">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Cari Tanggal:</label>
                            <div class="flex">
                                <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                                    placeholder="Contoh: 2023-06-28"
                                    class="w-full border-gray-300 rounded-l-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:italic">
                                <button type="submit"
                                    class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-r-md flex items-center justify-center transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8" />
                                        <path d="m21 21-4.3-4.3" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Tempat Tabel di-Render (AJAX) --}}
                    <div id="table-container">
                        @include('agenda.partials._table')
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Script Live Search AJAX --}}
    <script>
        let typingTimer;
        let doneTypingInterval = 500;
        let searchInput = document.getElementById('searchInput');
        let tahunSelect = document.getElementById('tahunSelect');
        let searchForm = document.getElementById('searchForm');
        let tableContainer = document.getElementById('table-container');

        function fetchTableData(url) {
            tableContainer.style.opacity = '0.5';

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    tableContainer.innerHTML = html;
                    tableContainer.style.opacity = '1';
                })
                .catch(error => console.error('Error:', error));
        }

        function updateData() {
            let url = new URL(searchForm.action);
            let params = new URLSearchParams(new FormData(searchForm));
            url.search = params.toString();

            fetchTableData(url);
            window.history.pushState({}, '', url);
        }

        // Trigger AJAX saat mengetik di Search Bar
        searchInput.addEventListener('keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(updateData, doneTypingInterval);
        });

        searchInput.addEventListener('keydown', function() {
            clearTimeout(typingTimer);
        });

        // Trigger AJAX saat Dropdown Tahun Ajaran diubah
        tahunSelect.addEventListener('change', function() {
            updateData();
        });

        // Trigger AJAX saat tombol Enter/Cari ditekan pada form
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateData();
        });

        // Event listener untuk klik pagination tanpa reload halaman
        document.addEventListener('click', function(e) {
            if (e.target.closest('.pagination-container a')) {
                e.preventDefault();
                let url = e.target.closest('.pagination-container a').href;
                fetchTableData(url);
                window.history.pushState({}, '', url);
            }
        });
    </script>
</x-app-layout>
