<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pusat Cetak Laporan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Notifikasi Error --}}
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- KARTU 1: Laporan Siswa --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-blue-500">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500 mr-2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <h3 class="text-lg font-bold">Laporan Data Siswa</h3>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Cetak daftar lengkap siswa Sekolah Minggu beserta informasi detail berdasarkan kelas yang dipilih.</p>
                        
                        <form action="{{ route('laporan.cetakSiswa') }}" method="POST" target="_blank">
                            @csrf
                            <label class="block font-medium text-sm text-gray-700 mb-1">Filter Kelas:</label>
                            <select name="kelas_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 mb-4" required>
                                <option value="semua">-- Semua Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded transition flex justify-center items-center">
                                Cetak PDF
                            </button>
                        </form>
                    </div>
                </div>

                {{-- KARTU 2: Laporan Agenda --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-green-500">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500 mr-2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                            <h3 class="text-lg font-bold">Laporan Rekap Kegiatan</h3>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Cetak rekapitulasi riwayat agenda dan jadwal kegiatan berdasarkan rentang tanggal tertentu.</p>
                        
                        <form action="{{ route('laporan.cetakAgenda') }}" method="POST" target="_blank">
                            @csrf
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block font-medium text-sm text-gray-700 mb-1">Mulai Tanggal:</label>
                                    <input type="date" name="tanggal_mulai" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500" required>
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-gray-700 mb-1">Sampai Tanggal:</label>
                                    <input type="date" name="tanggal_selesai" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500" required>
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-800 text-white font-bold py-2 px-4 rounded transition flex justify-center items-center">
                                Cetak PDF
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>