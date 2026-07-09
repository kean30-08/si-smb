<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Daftar Siswa') }}
                </h2>
                {{-- INFO TAHUN AJARAN AKTIF --}}
                @php
                    $tahunAktif = \App\Models\TahunAjaran::where('status', 'aktif')->first();
                @endphp
                <p class="text-sm text-indigo-600 font-bold mt-1 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    TA Aktif Saat Ini: {{ $tahunAktif ? $tahunAktif->tahun_ajaran : 'Belum Ada TA Aktif' }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-2">
                <a href="{{ route('siswa.cetakMassal') }}"
                    class="w-full sm:w-auto text-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow transition">
                    Cetak Semua Kartu
                </a>

                <a href="{{ route('siswa.create') }}"
                    class="w-full sm:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                    + Tambah Siswa
                </a>

            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 bg-gray-50 md:bg-white">

                    {{-- Form Search & Filter --}}
                    <form id="searchForm" action="{{ route('siswa.index') }}" method="GET"
                        class="mb-6 flex flex-col md:flex-row gap-4 w-full flex-wrap">

                        {{-- Input Search --}}
                        <div class="flex-1 flex w-full md:w-auto min-w-[200px]">
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama atau NIK siswa..."
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

                        {{-- Dropdown Filter Kelas --}}
                        <div class="w-full md:w-auto">
                            <select id="kelasFilter" name="kelas_id"
                                class="w-full md:w-40 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Semua Kelas --</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}"
                                        {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Dropdown Filter Status --}}
                        <div class="w-full md:w-auto">
                            <select id="statusFilter" name="status"
                                class="w-full md:w-40 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif
                                </option>
                                <option value="tidak aktif" {{ request('status') == 'tidak aktif' ? 'selected' : '' }}>
                                    Tidak Aktif</option>
                                <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus
                                </option>
                            </select>
                        </div>

                        {{-- Tombol Reset --}}
                        <div id="resetButtonContainer"
                            class="w-full sm:w-auto {{ request('search') || request('kelas_id') || request('status') ? '' : 'hidden' }}">
                            <a href="{{ route('siswa.index') }}"
                                class="block text-center bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 text-sm font-bold py-2 px-4 rounded-md transition shadow-sm h-full flex items-center justify-center">
                                Reset
                            </a>
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

    <script>
        let searchInput = document.getElementById('searchInput');
        let searchForm = document.getElementById('searchForm');
        let statusFilter = document.getElementById('statusFilter');
        let kelasFilter = document.getElementById('kelasFilter');
        let tableContainer = document.getElementById('table-container');
        let resetButtonContainer = document.getElementById('resetButtonContainer');

        function toggleResetButton() {
            if (searchInput.value.trim() !== '' || statusFilter.value !== '' || kelasFilter.value !== '') {
                resetButtonContainer.classList.remove('hidden');
            } else {
                resetButtonContainer.classList.add('hidden');
            }
        }

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
            toggleResetButton();
        }

        // Tambahkan event listener untuk semua dropdown filter yang tersisa
        statusFilter.addEventListener('change', updateData);
        kelasFilter.addEventListener('change', updateData);

        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateData();
        });

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
