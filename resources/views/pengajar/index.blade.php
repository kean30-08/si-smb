<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Pengajar') }}
            </h2>

            @if ($isAdmin)
                <div class="w-full sm:w-auto flex">
                    <a href="{{ route('pengajar.create') }}"
                        class="w-full sm:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                        + Tambah Pengajar
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 bg-gray-50 md:bg-white">

                    {{-- Form Manual Search & Filter --}}
                    <form id="searchForm" action="{{ route('pengajar.index') }}" method="GET"
                        class="mb-6 flex flex-col md:flex-row gap-4 w-full">

                        {{-- Dropdown Status --}}
                        <div class="w-full md:w-1/4">
                            <select id="statusFilter" name="status"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="tidak aktif" {{ request('status') == 'tidak aktif' ? 'selected' : '' }}>
                                    Tidak Aktif</option>
                            </select>
                        </div>

                        {{-- Search Bar --}}
                        <div class="flex flex-1">
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama pengajar..."
                                class="w-full border-gray-300 rounded-l-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <button type="submit"
                                class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-r-md flex items-center justify-center transition"
                                title="Cari">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="m21 21-4.3-4.3" />
                                </svg>
                            </button>
                        </div>

                        {{-- Tombol Reset (Dikendalikan oleh Javascript) --}}
                        <div id="resetButtonContainer"
                            class="w-full sm:w-auto {{ request('search') || request('status') ? '' : 'hidden' }}">
                            <a href="{{ route('pengajar.index') }}"
                                class="block text-center bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 text-sm font-bold py-2 px-4 rounded-md transition shadow-sm h-full flex items-center justify-center">
                                Reset
                            </a>
                        </div>
                    </form>

                    {{-- Tabel Data --}}
                    <div id="table-container">
                        @include('pengajar.partials._table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk Manual Submit & Filter (AJAX Tanpa Full Reload) --}}
    <script>
        let searchInput = document.getElementById('searchInput');
        let searchForm = document.getElementById('searchForm');
        let statusFilter = document.getElementById('statusFilter');
        let tableContainer = document.getElementById('table-container');
        let resetButtonContainer = document.getElementById('resetButtonContainer');

        // Fungsi baru untuk memunculkan/menyembunyikan tombol Reset secara realtime
        function toggleResetButton() {
            if (searchInput.value.trim() !== '' || statusFilter.value !== '') {
                resetButtonContainer.classList.remove('hidden');
            } else {
                resetButtonContainer.classList.add('hidden');
            }
        }

        // Fungsi untuk menarik data tanpa reload
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

        // Fungsi untuk mengambil parameter form dan membuat URL
        function updateData() {
            let url = new URL(searchForm.action);
            let params = new URLSearchParams(new FormData(searchForm));
            url.search = params.toString();

            fetchTableData(url);
            window.history.pushState({}, '', url);

            toggleResetButton(); // Panggil fungsi reset setiap kali data diupdate
        }

        // 1. FILTER DROPDOWN TETAP OTOMATIS 
        statusFilter.addEventListener('change', updateData);

        // 2. SEARCH BAR MENJADI MANUAL
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateData();
        });

        // 3. PAGINATION AJAX
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
