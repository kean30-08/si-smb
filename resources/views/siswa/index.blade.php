<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Siswa') }}
            </h2>
            
            {{-- Tombol Tambah Siswa (Nanti kita buat fiturnya) --}}
            <a href="{{ route('siswa.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                + Tambah Siswa
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Form Search & Filter --}}
                    <form id="searchForm" action="{{ route('siswa.index') }}" method="GET" class="mb-6 flex flex-col md:flex-row gap-4">
                        
                        {{-- Input Search dengan Logo Lucide --}}
                        <div class="flex-1 flex">
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" autofocus placeholder="Cari nama atau NIS siswa..." class="w-full border-gray-300 rounded-l-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            
                            {{-- Tombol Search diganti Logo Lucide --}}
                            <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-r-md flex items-center justify-center transition" title="Cari">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="m21 21-4.3-4.3"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Dropdown Filter Kelas --}}
                        <select id="kelasFilter" name="kelas_id" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 md:w-48">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Dropdown Filter Status --}}
                        <select id="statusFilter" name="status" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 md:w-48">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak aktif" {{ request('status') == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                        </select>
                        
                    </form>

                    {{-- Tabel Tailwind --}}
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3 px-6">No</th>
                                    <th scope="col" class="py-3 px-6">Nama Lengkap</th>
                                    <th scope="col" class="py-3 px-6">NIS</th>
                                    <th scope="col" class="py-3 px-6">Kelas</th>
                                    <th scope="col" class="py-3 px-6">Status</th>
                                    <th scope="col" class="py-3 px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($siswas as $index => $siswa)
                                {{-- Membuat baris bisa diklik --}}
                                <tr onclick="window.location='{{ route('siswa.show', $siswa->id) }}'" class="bg-white border-b hover:bg-gray-100 cursor-pointer transition">
                                    <td class="py-4 px-6">{{ $siswas->firstItem() + $index }}</td>
                                    <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">{{ $siswa->nama_lengkap }}</td>
                                    <td class="py-4 px-6">{{ $siswa->nis }}</td>
                                    <td class="py-4 px-6">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                                    
                                    {{-- Kolom Status --}}
                                    <td class="py-4 px-6">
                                        @if ($siswa->status == 'aktif')
                                            <span class="px-2 py-1 font-semibold text-green-700 bg-green-100 rounded-full">Aktif</span>
                                        @elseif ($siswa->status == 'tidak aktif')
                                            <span class="px-2 py-1 font-semibold text-red-700 bg-red-100 rounded-full">Tidak Aktif</span>
                                        @elseif ($siswa->status == 'lulus')
                                            <span class="px-2 py-1 font-semibold text-blue-700 bg-blue-100 rounded-full">Lulus</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Aksi --}}
                                    {{-- onlick stopPropagation digunakan agar ketika tombol Edit/Hapus diklik, dia tidak ikut membuka halaman Show --}}
                                    <td class="py-4 px-6 flex justify-center space-x-4" onclick="event.stopPropagation();">
                                        
                                        {{-- Tombol Edit Lucide --}}
                                        <a href="{{ route('siswa.edit', $siswa->id) }}" class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/></svg>
                                        </a>
                                        
                                        {{-- Tombol Hapus Lucide --}}
                                        <form action="{{ route('siswa.destroy', $siswa->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-4 px-6 text-center text-gray-500">
                                        Belum ada data siswa. Silakan tambahkan data baru.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $siswas->links() }}
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

        // Fitur Live Search untuk teks
        searchInput.addEventListener('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        });

        searchInput.addEventListener('keydown', function () {
            clearTimeout(typingTimer);
        });

        function doneTyping () {
            searchForm.submit();
        }

        // Fitur Auto-Submit untuk Dropdown Filter
        kelasFilter.addEventListener('change', function() {
            searchForm.submit();
        });

        statusFilter.addEventListener('change', function() {
            searchForm.submit();
        });

        // Kembalikan kursor ke akhir teks setelah reload
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