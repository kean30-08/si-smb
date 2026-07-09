<x-app-layout>
    <x-slot name="header">
        {{-- Flex-col untuk HP, tombol memanjang --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Materi Pembelajaran') }}
            </h2>


            <div class="w-full sm:w-auto flex">
                <a href="{{ route('materi.create') }}"
                    class="w-full sm:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                    + Tambah Materi
                </a>
            </div>


        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 bg-gray-50 md:bg-white">

                    {{-- Form Search & Filter Kelas --}}
                    <form id="searchForm" action="{{ route('materi.index') }}" method="GET"
                        class="mb-6 flex flex-col md:flex-row gap-4 w-full">
                        {{-- Kolom Pencarian --}}
                        <div class="flex-1 flex w-full">
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                                placeholder="Cari judul materi..."
                                class="w-full border-gray-300 rounded-l-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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

                        {{-- Filter Kelas --}}
                        <select id="kelasFilter" name="kelas_id"
                            class="w-full md:w-48 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}"
                                    {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Tombol Reset (Dikendalikan oleh Javascript) --}}
                        <div id="resetButtonContainer"
                            class="w-full sm:w-auto {{ request('search') || request('kelas_id') ? '' : 'hidden' }}">
                            <a href="{{ route('materi.index') }}"
                                class="block text-center bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 text-sm font-bold py-2 px-4 rounded-md transition shadow-sm h-full flex items-center justify-center">
                                Reset
                            </a>
                        </div>
                    </form>

                    <div id="table-container">
                        @include('materi.partials._table')
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        let searchInput = document.getElementById('searchInput');
        let searchForm = document.getElementById('searchForm');
        let kelasFilter = document.getElementById('kelasFilter');
        let tableContainer = document.getElementById('table-container');
        let resetButtonContainer = document.getElementById('resetButtonContainer');

        // Fungsi baru untuk memunculkan/menyembunyikan tombol Reset secara realtime
        function toggleResetButton() {
            if (searchInput.value.trim() !== '' || kelasFilter.value !== '') {
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
        kelasFilter.addEventListener('change', updateData);

        // 2. SEARCH BAR MENJADI MANUAL
        // (Hanya memicu pencarian saat tombol "Enter" ditekan atau ikon "Cari" diklik)
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
