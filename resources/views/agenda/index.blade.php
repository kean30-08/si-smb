<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Agenda & Jadwal Kegiatan') }}
            </h2>
            <div class="w-full sm:w-auto flex">
                <a href="{{ route('agenda.create') }}"
                    class="w-full sm:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                    + Tambah Jadwal
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 bg-gray-50 md:bg-white">

                    <p class="text-gray-700 text-sm mb-1">Cari Jadwal Berdasarkan Urutan Tahun, Bulan, Hari</p>
                    {{-- Form Pencarian Tanggal --}}
                    <form id="searchForm" action="{{ route('agenda.index') }}" method="GET" class="mb-6 flex w-full">
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                            placeholder="Contoh: 2023-06-28"
                            class="w-full md:w-1/3 border-gray-300 rounded-l-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:italic">
                        <button type="submit"
                            class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-r-md flex items-center justify-center transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                        </button>
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

        searchInput.addEventListener('keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(updateData, doneTypingInterval);
        });

        searchInput.addEventListener('keydown', function() {
            clearTimeout(typingTimer);
        });

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
