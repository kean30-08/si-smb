<x-app-layout>
    <x-slot name="header">
        {{-- PERBAIKAN HEADER: Flex-col untuk HP, tombol memanjang --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Siswa') }}
            </h2>

            @if ($isAdmin)
                <div class="w-full sm:w-auto flex">
                    <a href="{{ route('siswa.create') }}"
                        class="w-full sm:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                        + Tambah Siswa
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 bg-gray-50 md:bg-white">

                    {{-- Form Search & Filter --}}
                    <form id="searchForm" action="{{ route('siswa.index') }}" method="GET"
                        class="mb-6 flex flex-col md:flex-row gap-4 w-full">

                        {{-- Input Search dengan Logo Lucide --}}
                        <div class="flex-1 flex w-full">
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama atau NIS siswa..."
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

                        {{-- Dropdown Filter --}}
                        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                            {{-- Filter Kelas --}}
                            <select id="kelasFilter" name="kelas_id"
                                class="w-full sm:w-48 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Kelas</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}"
                                        {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Filter Status --}}
                            <select id="statusFilter" name="status"
                                class="w-full sm:w-48 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="tidak aktif" {{ request('status') == 'tidak aktif' ? 'selected' : '' }}>
                                    Tidak Aktif</option>
                                <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus
                                </option>
                            </select>
                        </div>
                    </form>

                    {{-- Tabel Data --}}
                    <div id="table-container">
                        @include('siswa.partials._table')
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk Auto-Submit (Live Search & Filter) --}}
    <script>
        let typingTimer;
        let doneTypingInterval = 500;
        let searchInput = document.getElementById('searchInput');
        let searchForm = document.getElementById('searchForm');
        let kelasFilter = document.getElementById('kelasFilter');
        let statusFilter = document.getElementById('statusFilter');
        let tableContainer = document.getElementById('table-container');

        // Fungsi untuk menarik data tanpa reload
        function fetchTableData(url) {
            // Tampilkan indikator loading ringan (opsional, bisa diganti efek lain)
            tableContainer.style.opacity = '0.5';

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Penting! Agar Laravel mendeteksi ini sebagai AJAX
                    }
                })
                .then(response => response.text())
                .then(html => {
                    // Ganti isi HTML lama dengan HTML baru dari controller
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

            // Opsional: Update URL di browser tanpa reload halamannya
            window.history.pushState({}, '', url);
        }

        // Event Listeners
        searchInput.addEventListener('keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(updateData, doneTypingInterval);
        });

        searchInput.addEventListener('keydown', function() {
            clearTimeout(typingTimer);
        });

        kelasFilter.addEventListener('change', updateData);
        statusFilter.addEventListener('change', updateData);

        // Mencegah form disubmit secara tradisional jika dienter
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateData();
        });

        // Menangani klik Pagination agar tidak full reload
        document.addEventListener('click', function(e) {
            // Jika yang diklik adalah link pagination
            if (e.target.closest('.pagination-container a')) {
                e.preventDefault();
                let url = e.target.closest('.pagination-container a').href;
                fetchTableData(url);
                window.history.pushState({}, '', url);
                // Scroll sedikit ke atas otomatis saat pindah halaman
                // window.scrollTo({
                //     top: 0,
                //     behavior: 'smooth'
                // });
            }
        });
    </script>
</x-app-layout>
