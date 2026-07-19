<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
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

            <div class="w-full lg:w-auto flex flex-col sm:flex-row flex-wrap gap-2">
                <a href="{{ route('siswa.index') }}"
                    class="w-full sm:w-auto text-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow transition">
                    Kembali
                </a>

                {{-- TOMBOL HISTORI SISWA PINDAH KE HEADER --}}
                <a href="{{ route('siswa.histori', $siswa->id) }}"
                    class="w-full sm:w-auto justify-center bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 flex items-center rounded transition gap-2 shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M3 3v18h18" />
                        <path d="m19 9-5 5-4-4-3 3" />
                    </svg>
                    Lihat Histori Kehadiran & Kelas
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">





                    {{-- TOMBOL RIWAYAT --}}


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Nama Lengkap</p>
                            <p class="text-lg font-medium">{{ $siswa->nama_lengkap }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Nama Panggilan</p>
                            <p class="text-lg font-medium">{{ $siswa->nama_panggilan ?? '-' }}</p>
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

                        {{-- STATUS SISWA (Warna disesuaikan otomatis) --}}
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Status</p>
                            <p
                                class="text-lg font-bold uppercase 
                                @if ($siswa->status == 'aktif') text-green-600 
                                @elseif($siswa->status == 'tidak aktif') text-red-600 
                                @else text-blue-600 @endif">
                                {{ $siswa->status }}
                            </p>
                        </div>

                        {{-- TAMBAHAN: ALASAN TIDAK AKTIF (Hanya muncul jika status "tidak aktif") --}}
                        @if ($siswa->status === 'tidak aktif')
                            <div class="md:col-span-2 bg-red-50 p-4 rounded-lg border border-red-200 mt-2">
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2 mt-0.5"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm text-red-700 font-bold uppercase tracking-wider mb-1">Alasan
                                            Berhenti / Tidak Aktif</p>
                                        <p class="text-base text-red-900 font-medium">
                                            {{ $siswa->alasan_tidak_aktif ?? 'Tidak ada keterangan tambahan yang dicatat.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- INFORMASI ANGKATAN & KELUAR --}}
                        <div class="col-span-1 md:col-span-2 mt-2 flex flex-col sm:flex-row gap-4">

                            {{-- TAHUN AJARAN PERTAMA KALI DIBUAT (ANGKATAN) --}}
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

                            {{-- INFO KAPAN TIDAK AKTIF / LULUS --}}
                            @if ($siswa->status != 'aktif')
                                <div
                                    class="inline-flex items-center px-3 py-1.5 rounded-md bg-red-50 border border-red-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="text-red-600 mr-2">
                                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0" />
                                        <line x1="12" y1="2" x2="12" y2="12" />
                                    </svg>
                                    <span class="text-sm font-semibold text-red-800">
                                        Berhenti/Lulus Sejak Tahun Ajaran:
                                        @php
                                            // Mencari histori terakhir siswa tersebut
                                            $historiTerakhir = $siswa
                                                ->riwayatHistori()
                                                ->with('tahunAjaran')
                                                ->orderBy('id', 'desc')
                                                ->first();

                                            if ($historiTerakhir && $historiTerakhir->tahunAjaran) {
                                                // Siswa berhenti DI TAHUN AJARAN SETELAH histori terakhirnya
                                                // Jadi kita cari TA yang ID-nya lebih besar dari ID TA terakhirnya
                                                $taBerhenti = \App\Models\TahunAjaran::where(
                                                    'id',
                                                    '>',
                                                    $historiTerakhir->tahun_ajaran_id,
                                                )
                                                    ->orderBy('id', 'asc')
                                                    ->first();

                                                // Jika tidak ada TA baru (berhenti di TA paling ujung saat ini)
                                                echo $taBerhenti
                                                    ? $taBerhenti->tahun_ajaran
                                                    : $historiTerakhir->tahunAjaran->tahun_ajaran;
                                            } else {
                                                echo 'Tidak diketahui';
                                            }
                                        @endphp
                                    </span>
                                </div>
                            @endif

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
                            <p class="text-sm text-gray-500 font-semibold">Email</p>
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
                        <div class="flex items-start sm:items-center space-x-6">

                            {{-- TAMPILAN GAMBAR QR --}}
                            <div
                                class="p-2 bg-white inline-block rounded border border-gray-300 shadow-sm text-center shrink-0">
                                {!! QrCode::size(100)->generate('SMB-' . $siswa->id) !!}
                                <p class="text-[10px] font-bold mt-1 text-gray-800 tracking-widest">
                                    SMB-{{ $siswa->id }}</p>
                            </div>

                            {{-- DERETAN TOMBOL CETAK --}}
                            <div class="flex flex-col gap-3 w-full max-w-xs">

                                {{-- TOMBOL BARU: CETAK BARCODE SAJA --}}
                                <a href="{{ route('siswa.cetakBarcode', $siswa->id) }}" target="_blank"
                                    class="inline-flex justify-center items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 transition shadow">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                        <rect x="3" y="3" width="18" height="18" rx="2"
                                            ry="2"></rect>
                                        <line x1="8" y1="8" x2="8" y2="16"></line>
                                        <line x1="12" y1="8" x2="12" y2="16"></line>
                                        <line x1="16" y1="8" x2="16" y2="16"></line>
                                    </svg>
                                    Cetak Barcode (Stiker)
                                </a>

                                {{-- TOMBOL LAMA: CETAK KARTU ID --}}
                                <a href="{{ route('siswa.cetakKartu', $siswa->id) }}" target="_blank"
                                    class="inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 transition shadow">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="mr-2" viewBox="0 0 16 16">
                                        <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z" />
                                        <path
                                            d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" />
                                    </svg>
                                    Cetak Kartu Pelajar (ID)
                                </a>

                                <p class="text-xs text-gray-500 mt-1">Pilih <strong>Stiker Barcode</strong> untuk
                                    ukuran kecil (di buku), atau <strong>Kartu ID</strong> untuk identitas lengkap
                                    (dikalungkan).</p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
