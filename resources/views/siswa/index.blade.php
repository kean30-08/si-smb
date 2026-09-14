<x-app-layout>
    <x-slot name="header">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        
        <!-- Kiri: Title & Info TA -->
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Daftar Siswa') }}
            </h2>
            @php
                $tahunAktif = \App\Models\TahunAjaran::where('status', 'aktif')->first();
            @endphp
            <p class="text-sm text-gray-500 font-medium mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                TA Aktif: <strong class="text-indigo-600 ml-1">{{ $tahunAktif ? $tahunAktif->tahun_ajaran : 'Belum Ada TA' }}</strong>
            </p>
        </div>

        <!-- Kanan: Grouped Action Buttons -->
        <div class="w-full lg:w-auto flex flex-wrap items-center gap-2">
            
            <!-- 1. Button Ulang Tahun (Secondary Action) -->
            <a href="{{ route('siswa.ulangTahun') }}"
               class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 hover:text-gray-900 transition">
                🎂 Ulang Tahun
            </a>

            <!-- 2. Dropdown Cetak & Export (Alpine.js) -->
            <div x-data="{ open: false }" class="relative inline-block text-left" @click.outside="open = false">
                <button @click="open = !open" 
                        type="button" 
                        class="inline-flex items-center justify-between px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 hover:text-gray-900 transition focus:outline-none">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        📄 Cetak & Export
                    </span>
                    <svg class="w-4 h-4 ml-2 text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Isi Dropdown Menu -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="origin-top-right absolute right-0 mt-2 w-60 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-50"
                     style="display: none;">
                    
                    <!-- Export Group -->
                    <div class="py-1">
                        <a href="{{ route('siswa.export') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition">
                            <svg class="mr-3 h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Excel
                        </a>
                    </div>

                    <!-- Cetak Kartu Group -->
                    <div class="py-1">
                        <a href="{{ route('siswa.cetakKartuBaru') }}" target="_blank" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition">
                            <span class="mr-3">🪪</span> Cetak Kartu (Murid Baru)
                        </a>
                        <a href="{{ route('siswa.cetakMassal') }}" target="_blank" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition">
                            <span class="mr-3">🪪</span> Cetak Semua Kartu
                        </a>
                    </div>

                    <!-- Cetak Barcode Group -->
                    <div class="py-1">
                        <a href="{{ route('siswa.cetakBarcodeBaru') }}" target="_blank" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition">
                            <span class="mr-3">📊</span> Cetak Barcode (Murid Baru)
                        </a>
                        <a href="{{ route('siswa.cetakBarcodeMassal') }}" target="_blank" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition">
                            <span class="mr-3">📊</span> Cetak Semua Barcode
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Primary Action (+ Tambah Siswa) -->
            <a href="{{ route('siswa.create') }}"
               class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg shadow-sm transition">
                + Tambah Siswa
            </a>

        </div>
    </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 bg-gray-50 md:bg-white">

                    {{-- CATATAN INFORMASI TAHUN AJARAN --}}
                    <div class="mb-5 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg shadow-sm">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-600 mt-0.5" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-blue-800">Informasi Penting</h3>
                                <p class="text-sm text-blue-700 mt-1">
                                    @php
                                        // Mengambil 1 data Tahun Ajaran dengan nama/angka paling besar (terbaru)
                                        $taTerbaru = \App\Models\TahunAjaran::orderBy('tahun_ajaran', 'desc')->first();
                                    @endphp
                                    Data siswa yang ditampilkan pada halaman ini selalu merepresentasikan data
                                    <strong>Tahun
                                        Ajaran Baru</strong>,
                                    yaitu
                                    <strong>{{ $taTerbaru ? $taTerbaru->tahun_ajaran : 'Belum ada data' }}</strong>.
                                    <br class="hidden sm:block">Untuk melihat riwayat daftar siswa di Tahun Ajaran
                                    lampau,
                                    silakan
                                    kunjungi menu <strong>Kelas &rarr; Histori Kelas</strong>.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Form Search & Filter --}}
                    <form id="searchForm" action="{{ route('siswa.index') }}" method="GET" ... {{-- Form Search & Filter --}}
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
                                <option value="">Semua Kelas</option>
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
