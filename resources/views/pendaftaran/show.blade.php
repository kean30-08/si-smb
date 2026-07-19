<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Detail Pendaftaran') }}: {{ $pendaftaran->nama_panggilan }}
    </h2>
    
    <a href="{{ route('kelola_pendaftaran.index') }}"
        class="w-full sm:w-auto justify-center text-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition shadow-sm">
        &larr; Kembali
    </a>
</div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- INFORMASI PENDAFTAR --}}
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-indigo-700 mb-4 uppercase tracking-wider">Identitas Calon
                            Siswa</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase">Nama Lengkap</p>
                                <p class="text-base font-medium">{{ $pendaftaran->nama_lengkap }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase">Nama Panggilan</p>
                                <p class="text-base font-medium">{{ $pendaftaran->nama_panggilan }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase">NIK</p>
                                <p class="text-base font-medium">{{ $pendaftaran->nis ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase">Jenis Kelamin</p>
                                <p class="text-base font-medium">
                                    {{ $pendaftaran->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase">Tempat, Tanggal Lahir</p>
                                <p class="text-base font-medium">
                                    {{ $pendaftaran->tempat_lahir }},
                                    {{ \Carbon\Carbon::parse($pendaftaran->tanggal_lahir)->translatedFormat('d F Y') }}
                                    <span
                                        class="text-xs font-bold text-indigo-600">({{ \Carbon\Carbon::parse($pendaftaran->tanggal_lahir)->age }}
                                        Thn)</span>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase">Kelas Tujuan</p>
                                <p class="text-base font-bold text-indigo-700">
                                    {{ $pendaftaran->kelas->nama_kelas ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- PENDIDIKAN & KONTAK SISWA --}}
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase">Asal Sekolah</p>
                                <p class="text-base font-medium">{{ $pendaftaran->asal_sekolah ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase">Nomor HP/WA Siswa</p>
                                <p class="text-base font-medium">{{ $pendaftaran->nomor_hp_siswa ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- INFO ORANG TUA --}}
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-indigo-700 mb-4 uppercase tracking-wider">Informasi Orang Tua
                            / Wali</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6">
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase">Nama Orang Tua</p>
                                <p class="text-base font-medium">{{ $pendaftaran->nama_orang_tua }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase">Email Orang Tua</p>
                                <p class="text-base font-medium">{{ $pendaftaran->email_orang_tua ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase">Nomor HP/WA Orang Tua</p>
                                <p class="text-base font-medium text-blue-600 font-bold">
                                    {{ $pendaftaran->nomor_hp_orang_tua }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-xs text-gray-500 font-semibold uppercase">Alamat Lengkap</p>
                                <p class="text-base font-medium">{{ $pendaftaran->alamat }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- TOMBOL AKSI --}}
                    <div class="flex justify-end gap-3 mt-8">
                        <form action="{{ route('kelola_pendaftaran.tolak', $pendaftaran->id) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Yakin menolak pendaftaran ini?')"
                                class="bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 px-6 rounded-lg transition shadow-sm">
                                Tolak Pendaftaran
                            </button>
                        </form>

                        <form action="{{ route('kelola_pendaftaran.terima', $pendaftaran->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Terima siswa ini? Data akan otomatis masuk ke tabel master Siswa dan Kelas saat ini.')"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-lg transition shadow-sm">
                                Terima & Masukkan ke Siswa ke Sistem
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
