<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Profil Pengajar') }}: {{ $pengajar->nama_lengkap }}
            </h2>
            @php
                $isAdmin = auth()->user()->isAdmin();
            @endphp
            <div class="w-full lg:w-auto flex flex-col sm:flex-row flex-wrap gap-2">
                @if ($isAdmin)
                    <a href="{{ route('pengajar.edit', $pengajar->id) }}"
                        class="w-full sm:w-auto justify-center bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 flex items-center rounded">
                        Edit Data
                    </a>
                @endif
                <a href="{{ route('pengajar.index') }}"
                    class="w-full sm:w-auto justify-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 flex items-center rounded">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Informasi Akun Login</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Email Sistem</p>
                            <p class="text-lg font-medium text-blue-600">
                                {{ $pengajar->user->email ?? 'Tidak ada akun terhubung' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Hak Akses</p>
                            <p class="text-lg font-medium">Pengajar</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Biodata Diri</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Nama Lengkap</p>
                            <p class="text-lg font-medium">{{ $pengajar->nama_lengkap }}</p>
                        </div>
                        {{-- <div>
                            <p class="text-sm text-gray-500 font-semibold">NIP / ID</p>
                            <p class="text-lg font-medium">{{ $pengajar->nip ?? '-' }}</p>
                        </div> --}}
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Jabatan</p>
                            <p class="text-lg font-medium">{{ $pengajar->jabatan->nama_jabatan ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Jenis Kelamin</p>
                            <p class="text-lg font-medium">
                                {{ $pengajar->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Nomor HP / WA</p>
                            <p class="text-lg font-medium">{{ $pengajar->nomor_hp ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500 font-semibold">Alamat Lengkap</p>
                            <p class="text-lg font-medium">{{ $pengajar->alamat ?? '-' }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
