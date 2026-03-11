<x-app-layout>
    <x-slot name="header">
        {{-- Flex-col untuk HP, tombol memanjang --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Materi Pembelajaran') }}
            </h2>
            @php
            $isAdmin = !\App\Models\Pengajar::where('user_id', auth()->id())->exists();
            @endphp
            @if ($isAdmin)
                <div class="w-full sm:w-auto flex">
                <a href="{{ route('materi.create') }}" class="w-full sm:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                    + Tambah Materi
                </a>
            </div>
            @endif
            
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 bg-gray-50 md:bg-white">

                    {{-- Form Search & Filter Kelas --}}
                    <form id="searchForm" action="{{ route('materi.index') }}" method="GET" class="mb-6 flex flex-col md:flex-row gap-4 w-full">
                        {{-- Kolom Pencarian --}}
                        <div class="flex-1 flex w-full">
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" autofocus placeholder="Cari judul materi..." class="w-full border-gray-300 rounded-l-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-r-md flex items-center justify-center transition" title="Cari">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </button>
                        </div>
                        
                        {{-- Filter Kelas --}}
                        <select id="kelasFilter" name="kelas_id" class="w-full md:w-48 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    {{-- Tabel Data --}}
                    <div class="w-full md:shadow-md md:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500">
                            
                            {{-- HEADER TABEL (Sembunyi di HP) --}}
                            <thead class="hidden md:table-header-group text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="py-3 px-6">No</th>
                                    <th class="py-3 px-6">Judul Materi</th>
                                    <th class="py-3 px-6">Kelas</th>
                                    <th class="py-3 px-6">Lampiran File</th>
                                    @if ($isAdmin)
                                        <th class="py-3 px-6 text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            
                            {{-- BODY TABEL (Berubah jadi susunan kartu di HP) --}}
                            <tbody class="block md:table-row-group">
                                @forelse ($materis as $index => $materi)
                                
                                {{-- BARIS TABEL --}}
                                <tr class="block md:table-row bg-white border border-gray-200 md:border-0 md:border-b hover:bg-gray-50 transition mb-4 md:mb-0 rounded-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0">
                                    
                                    {{-- Kolom No (Sembunyi di HP) --}}
                                    <td class="hidden md:table-cell py-4 px-6">{{ $materis->firstItem() + $index }}</td>
                                    
                                    {{-- Kolom Judul & Deskripsi --}}
                                    <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6 border-b md:border-none border-dashed border-gray-200 mb-3 md:mb-0 pb-3 md:pb-4">
                                        <p class="font-bold text-gray-900 text-base md:text-sm">{{ $materi->judul }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($materi->deskripsi, 50) }}</p>
                                    </td>
                                    
                                    {{-- Kolom Kelas --}}
                                    <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6 font-medium text-blue-600 mb-2 md:mb-0">
                                        <div class="flex items-center justify-between md:justify-start">
                                            <span class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Peruntukkan Kelas</span>
                                            <span>{{ $materi->kelas->nama_kelas ?? 'Tanpa Kelas' }}</span>
                                        </div>
                                    </td>
                                    
                                    {{-- Kolom Lampiran (Download) --}}
                                    <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6 mb-2 md:mb-0">
                                        <div class="flex items-center justify-between md:justify-start">
                                            <span class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">File Materi</span>
                                            @if($materi->file_materi)
                                                <a href="{{ asset('storage/' . $materi->file_materi) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-[10px] md:text-xs font-semibold rounded-full hover:bg-green-200 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                                                    Download File
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-xs italic">Tidak ada file</span>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    {{-- Kolom Aksi (Edit & Hapus) --}}
                                    @if ($isAdmin)
                                        <td class="block md:table-cell py-3 md:py-4 px-2 md:px-6 text-right md:text-center mt-2 md:mt-0 border-t md:border-none border-gray-50 pt-3 md:pt-4">
                                        <div class="flex justify-end md:justify-center space-x-5">
                                            <a href="{{ route('materi.edit', $materi->id) }}" class="text-blue-500 hover:text-blue-700 transition p-1" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/></svg>
                                            </a>
                                            <form action="{{ route('materi.destroy', $materi->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus materi ini beserta file lampirannya?');" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition p-1" title="Hapus">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    @endif
                                    
                                </tr>
                                @empty
                                <tr class="block md:table-row bg-white border border-gray-200 rounded-lg p-4">
                                    <td colspan="5" class="block md:table-cell py-4 px-6 text-center text-gray-500">Belum ada materi pembelajaran.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">{{ $materis->links() }}</div>

                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk Auto Submit Live Search & Filter --}}
    <script>
        let typingTimer;                
        let doneTypingInterval = 500;   
        let searchInput = document.getElementById('searchInput');
        let searchForm = document.getElementById('searchForm');
        let kelasFilter = document.getElementById('kelasFilter');

        searchInput.addEventListener('keyup', () => { 
            clearTimeout(typingTimer); 
            typingTimer = setTimeout(() => searchForm.submit(), doneTypingInterval); 
        });
        
        searchInput.addEventListener('keydown', () => clearTimeout(typingTimer));
        
        kelasFilter.addEventListener('change', () => searchForm.submit());

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