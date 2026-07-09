<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Detail Siswa') }}: {{ $siswa->nama_lengkap }}
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

            <div class="w-full sm:w-auto flex">
                <a href="{{ route('siswa.index') }}"
                    class="w-full sm:w-auto text-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- TOMBOL RIWAYAT --}}
                    <div class="mb-6 flex justify-end">
                        <a href="{{ route('siswa.histori', $siswa->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 rounded-lg text-sm font-bold shadow-sm transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="mr-2">
                                <path d="M3 3v18h18" />
                                <path d="m19 9-5 5-4-4-3 3" />
                            </svg>
                            Lihat Histori Kehadiran
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Nama Lengkap</p>
                            <p class="text-lg font-medium">{{ $siswa->nama_lengkap }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">NIK</p>
                            <p class="text-lg font-medium">{{ $siswa->nis }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Jenis Kelamin</p>
                            <p class="text-lg font-medium">
                                {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                        <div>
                            {{-- LABEL DIUBAH MENJADI KELAS TERBARU --}}
                            <p class="text-sm text-gray-500 font-semibold">Kelas (Terbaru)</p>
                            <p class="text-lg font-medium">
                                {{ $siswa->historiAktif->kelas->nama_kelas ?? 'Belum terdaftar di kelas manapun' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Tempat, Tanggal Lahir</p>
                            <p class="text-lg font-medium">
                                {{ $siswa->tempat_lahir ?? '-' }},
                                {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Status</p>
                            <p class="text-lg font-medium uppercase text-blue-600">{{ $siswa->status }}</p>
                        </div>

                        {{-- TAHUN AJARAN PERTAMA KALI DIBUAT (ANGKATAN) --}}
                        <div class="col-span-1 md:col-span-2 mt-2">
                            <div
                                class="inline-flex items-center px-3 py-1.5 rounded-md bg-green-50 border border-green-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="text-green-600 mr-2">
                                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20" />
                                </svg>
                                <span class="text-sm font-semibold text-green-800">
                                    Terdaftar Pada Tahun Ajaran:
                                    @php
                                        $historiAwal = $siswa
                                            ->riwayatHistori()
                                            ->with('tahunAjaran')
                                            ->orderBy('id', 'asc')
                                            ->first();
                                    @endphp
                                    {{ $historiAwal && $historiAwal->tahunAjaran ? $historiAwal->tahunAjaran->tahun_ajaran : \Carbon\Carbon::parse($siswa->created_at)->format('Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="col-span-1 md:col-span-2 border-t pt-4 mt-2">
                            <h4 class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-wider">Pendidikan &
                                Kontak Pribadi</h4>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Asal Sekolah</p>
                            <p class="text-lg font-medium">{{ $siswa->asal_sekolah ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Nomor HP Pribadi (Siswa)</p>
                            <p class="text-lg font-medium">{{ $siswa->nomor_hp_siswa ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-2 border-t pt-4 mt-6">
                        <h4 class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-wider">Informasi Orang
                            Tua/Wali
                        </h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Nama Orang Tua/Wali</p>
                            <p class="text-lg font-medium">{{ $siswa->nama_orang_tua ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Email Orang Tua/Wali</p>
                            <p class="text-lg font-medium">{{ $siswa->email_orang_tua ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Nomor HP / WA OrangTua/Wali</p>
                            <p class="text-lg font-medium">{{ $siswa->nomor_hp_orang_tua ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Alamat Lengkap</p>
                            <p class="text-lg font-medium">{{ $siswa->alamat ?? '-' }}</p>
                        </div>
                    </div>


                    <div class="mt-8 border-t pt-6">
                        <h4 class="text-sm font-bold text-gray-500 mb-3">QR Code Identitas Siswa</h4>
                        <div class="flex items-center space-x-6">
                            <div class="p-2 bg-white inline-block rounded border border-gray-300 shadow-sm text-center">
                                {!! QrCode::size(100)->generate('SMB-' . $siswa->id) !!}
                                <p class="text-[10px] font-bold mt-1 text-gray-800 tracking-widest">
                                    SMB-{{ $siswa->id }}</p>
                            </div>
                            <div>
                                <a href="{{ route('siswa.cetakKartu', $siswa->id) }}" target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition ease-in-out duration-150 shadow">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="mr-2" viewBox="0 0 16 16">
                                        <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z" />
                                        <path
                                            d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
                                    </svg>
                                    Cetak Kartu Pelajar (ID)
                                </a>
                                <p class="text-xs text-gray-500 mt-2 max-w-xs">Gunakan kartu ini untuk absensi
                                    otomatis melalui kamera scanner Vihara.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
