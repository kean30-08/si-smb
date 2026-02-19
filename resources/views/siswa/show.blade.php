<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Siswa') }}: {{ $siswa->nama_lengkap }}
            </h2>
            <a href="{{ route('siswa.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Nama Lengkap</p>
                            <p class="text-lg font-medium">{{ $siswa->nama_lengkap }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">NIS / ID Vihara</p>
                            <p class="text-lg font-medium">{{ $siswa->nis }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Jenis Kelamin</p>
                            <p class="text-lg font-medium">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
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
                            <p class="text-sm text-gray-500 font-semibold">Total Poin Keaktifan</p>
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
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500 font-semibold">Alamat Lengkap</p>
                            <p class="text-lg font-medium">{{ $siswa->alamat ?? '-' }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>