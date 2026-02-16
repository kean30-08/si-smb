<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Siswa Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Form Mulai Disini --}}
                    <form action="{{ route('siswa.store') }}" method="POST">
                        @csrf {{-- Wajib ada untuk keamanan Laravel --}}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Nama Lengkap --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>

                            {{-- NIS --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">NIS / ID Vihara</label>
                                <input type="text" name="nis" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            </div>

                            {{-- Jenis Kelamin --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>

                            {{-- Pilih Kelas --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Kelas</label>
                                <select name="kelas_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kelas ?? 'Kelas ID: '.$k->id }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-red-500 mt-1">*Jika kosong, buat data Kelas dulu di menu Kelas</p>
                            </div>

                            {{-- Nama Orang Tua --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nama Orang Tua</label>
                                <input type="text" name="nama_orang_tua" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            {{-- Email Orang Tua (Penting untuk Notifikasi) --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Email Orang Tua</label>
                                <input type="email" name="email_orang_tua" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                             {{-- No HP Orang Tua --}}
                             <div>
                                <label class="block font-medium text-sm text-gray-700">No HP / WA Orang Tua</label>
                                <input type="text" name="nomor_hp_orang_tua" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                        </div>

                        {{-- Tombol Simpan --}}
                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="ml-4 bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Data
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>