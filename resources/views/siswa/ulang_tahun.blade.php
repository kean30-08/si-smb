<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Ulang Tahun Siswa') }}
            </h2>
            <a href="{{ route('siswa.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow-sm transition">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- NOTIFIKASI / REMINDER ULANG TAHUN TERDEKAT (7 Hari Kedepan) --}}
            @if(isset($siswaMendekatiUltah) && $siswaMendekatiUltah->count() > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md shadow-sm mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                        </div>
                        <div class="ml-3 w-full">
                            <h3 class="text-sm font-bold text-yellow-800 uppercase tracking-wider">
                                Pengingat: Ulang Tahun Terdekat (7 Hari ke Depan)
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach($siswaMendekatiUltah as $ultahSiswa)
                                        @php
                                            $tglLahirObj = \Carbon\Carbon::parse($ultahSiswa->tanggal_lahir);
                                            $ultahTahunIni = $tglLahirObj->copy()->year(now()->year);
                                            if ($ultahTahunIni->isPast() && !$ultahTahunIni->isToday()) {
                                                $ultahTahunIni->addYear();
                                            }
                                            
                                            $selisihHari = \Carbon\Carbon::now()->startOfDay()->diffInDays($ultahTahunIni, false);
                                            
                                            if ($selisihHari == 0) {
                                                $keterangan = '<span class="font-bold text-red-600 bg-red-100 px-2 py-0.5 rounded">HARI INI!</span>';
                                            } elseif ($selisihHari == 1) {
                                                $keterangan = '<span class="font-bold text-orange-600">BESOK</span>';
                                            } else {
                                                $keterangan = "dalam " . ceil($selisihHari) . " hari";
                                            }
                                            
                                            $usiaBaru = $ultahTahunIni->year - $tglLahirObj->year;
                                        @endphp
                                        <li>
                                            <span class="font-bold">{{ $ultahSiswa->nama_lengkap }}</span> 
                                            akan berulang tahun yang ke-{{ $usiaBaru }} 
                                            ({!! $keterangan !!} - {{ $ultahTahunIni->translatedFormat('d F') }})
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @php
                $bulanSekarangNama = \Carbon\Carbon::now()->translatedFormat('F');
            @endphp
            
            {{-- Looping per Bulan --}}
            @forelse($siswas as $bulan => $daftarSiswa)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 {{ $bulan == $bulanSekarangNama ? 'border-2 border-indigo-400' : '' }}">
                    <div class="p-4 md:p-6 text-gray-900 border-b border-gray-200 {{ $bulan == $bulanSekarangNama ? 'bg-indigo-50' : 'bg-gray-50' }}">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold {{ $bulan == $bulanSekarangNama ? 'text-indigo-700' : 'text-pink-600' }} uppercase flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"></path>
                                </svg>
                                Bulan {{ $bulan }}
                                <span class="ml-2 text-sm {{ $bulan == $bulanSekarangNama ? 'text-indigo-500' : 'text-gray-500' }} font-normal lowercase">({{ $daftarSiswa->count() }} siswa)</span>
                            </h3>
                            
                            @if($bulan == $bulanSekarangNama)
                                <span class="px-3 py-1 bg-indigo-600 text-white text-xs font-bold rounded-full shadow-sm">
                                    Bulan Ini
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            
                            {{-- Looping Siswa di dalam bulan tersebut --}}
                            @foreach($daftarSiswa as $siswa)
                                @php
                                    // Hitung usia pada tahun ini
                                    $tahunLahir = \Carbon\Carbon::parse($siswa->tanggal_lahir)->year;
                                    $tahunSekarang = \Carbon\Carbon::now()->year;
                                    $usiaTahunIni = $tahunSekarang - $tahunLahir;
                                @endphp
                                <div class="border {{ $bulan == $bulanSekarangNama ? 'border-indigo-100' : 'border-pink-100' }} rounded-lg p-4 flex items-center space-x-4 shadow-sm hover:shadow-md transition bg-white">
                                    <div class="flex-shrink-0 {{ $bulan == $bulanSekarangNama ? 'bg-indigo-100 text-indigo-600' : 'bg-pink-100 text-pink-600' }} rounded-full font-bold text-center w-14 h-14 flex items-center justify-center flex-col">
                                        {{-- Menampilkan Tanggal Saja --}}
                                        <span class="text-xl leading-none">{{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d') }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-gray-800 truncate">{{ $siswa->nama_lengkap }}</h4>
                                        <p class="text-xs text-gray-500 truncate mt-0.5">
                                            Lahir: {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') }}
                                        </p>
                                        <p class="text-xs font-semibold {{ $bulan == $bulanSekarangNama ? 'text-indigo-500' : 'text-pink-500' }} mt-1">
                                            Usia tahun ini: {{ $usiaTahunIni }} Tahun
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                            
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-8 text-gray-500 text-center flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="text-lg font-medium">Belum ada data siswa aktif.</p>
                    </div>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>