<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Siswa') }}: {{ $siswa->nama_lengkap }}
            </h2>
            <div class="w-full sm:w-auto flex">
                <a href="{{ route('siswa.index') }}"
                    class="w-full sm:w-auto text-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        @php
            $isAdmin = !\App\Models\Pengajar::where('user_id', auth()->id())->exists();
        @endphp
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Nama Lengkap</p>
                            <p class="text-lg font-medium">{{ $siswa->nama_lengkap }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">NIS</p>
                            <p class="text-lg font-medium">{{ $siswa->nis }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Jenis Kelamin</p>
                            <p class="text-lg font-medium">
                                {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Kelas</p>
                            <p class="text-lg font-medium">{{ $siswa->kelas->nama_kelas ?? 'Belum memiliki kelas' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Tempat, Tanggal Lahir</p>
                            <p class="text-lg font-medium">
                                {{ $siswa->tempat_lahir ?? '-' }},
                                {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d M Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Status</p>
                            <p class="text-lg font-medium uppercase text-blue-600">{{ $siswa->status }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Total Poin</p>
                            <p class="text-lg font-bold text-orange-500">{{ $siswa->total_poin }} Poin</p>
                        </div>
                    </div>

                    <hr class="my-6">

                    <h3 class="text-lg font-bold mb-4">Informasi Orang Tua & Kontak</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Nama Orang Tua</p>
                            <p class="text-lg font-medium">{{ $siswa->nama_orang_tua ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Email Orang Tua</p>
                            <p class="text-lg font-medium">{{ $siswa->email_orang_tua ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Nomor HP / WA</p>
                            <p class="text-lg font-medium">{{ $siswa->nomor_hp_orang_tua ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Alamat Lengkap</p>
                            <p class="text-lg font-medium">{{ $siswa->alamat ?? '-' }}</p>
                        </div>
                        @if ($isAdmin)
                            <div class="mt-8 border-t pt-6">
                                <h4 class="text-sm font-bold text-gray-500 mb-3">QR Code Identitas Siswa</h4>
                                <div class="flex items-center space-x-6">
                                    <div
                                        class="p-2 bg-white inline-block rounded border border-gray-300 shadow-sm text-center">
                                        {{-- Memanggil Library QR Code --}}
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
