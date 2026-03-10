<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pusat Cetak Laporan & Rekapitulasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- KARTU 1: Keaktifan Siswa (Apresiasi) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-2 text-indigo-700">Rekap Keaktifan Siswa</h3>
                        <p class="text-xs text-gray-500 mb-4">Laporan persentase kehadiran untuk penentuan apresiasi/penghargaan siswa.</p>
                        
                        <form action="{{ route('laporan.cetakKehadiranSiswa') }}" method="POST" target="_blank">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Mulai Tanggal</label>
                                <input type="date" name="tanggal_mulai" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" class="w-full text-sm border-gray-300 rounded" required>
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date" name="tanggal_selesai" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" class="w-full text-sm border-gray-300 rounded" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Filter Kelas</label>
                                <select name="kelas_id" class="w-full text-sm border-gray-300 rounded" required>
                                    <option value="semua">Semua Kelas</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded text-sm transition">Cetak PDF</button>
                        </form>
                    </div>
                </div>

                {{-- KARTU 2: Statistik Agenda --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-green-500">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-2 text-green-700">Agenda Kegiatan</h3>
                        <p class="text-xs text-gray-500 mb-4">Mencetak rekapitulasi riwayat kegiatan beserta statistik kehadiran jumlah siswa.</p>
                        
                        <form action="{{ route('laporan.cetakAgenda') }}" method="POST" target="_blank">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Mulai Tanggal</label>
                                <input type="date" name="tanggal_mulai" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" class="w-full text-sm border-gray-300 rounded" required>
                            </div>
                            <div class="mb-5">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date" name="tanggal_selesai" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" class="w-full text-sm border-gray-300 rounded" required>
                            </div>
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-800 text-white font-bold py-2 px-4 rounded text-sm transition mt-[68px]">Cetak PDF</button>
                        </form>
                    </div>
                </div>

                {{-- KARTU 3: Data Pengurus --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-amber-500">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-2 text-amber-700">Data Pengurus Vihara</h3>
                        <p class="text-xs text-gray-500 mb-4">Mencetak daftar rekapitulasi pengurus dan pengajar Sekolah Minggu yang aktif.</p>
                        
                        <form action="{{ route('laporan.cetakPengajar') }}" method="POST" target="_blank">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Mulai Tanggal</label>
                                <input type="date" name="tanggal_mulai" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" class="w-full text-sm border-gray-300 rounded" required>
                            </div>
                            <div class="mb-5">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date" name="tanggal_selesai" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" class="w-full text-sm border-gray-300 rounded" required>
                            </div>
                            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded text-sm transition mt-[68px]">Cetak PDF</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>