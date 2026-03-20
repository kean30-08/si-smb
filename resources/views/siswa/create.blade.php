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
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- IDENTITAS UTAMA --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    value="{{ old('nama_lengkap') }}" placeholder="Masukkan Nama Lengkap ...">
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">NIS</label>
                                <input type="text" name="nis"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    value="{{ old('nis') }}" placeholder="Masukkan Nomor Induk Siswa ..." required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Jenis Kelamin</label>
                                <select name="jenis_kelamin"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    value="{{ old('jenis_kelamin') }}">
                                    <option value="L" {{ old('jenis_kelamin', 'L') == 'L' ? 'selected' : '' }}>
                                        Laki-laki
                                    </option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Kelas</label>
                                <select name="kelas_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">

                                    @if ($kelas->isEmpty())
                                        <option value="">Tidak Ada Kelas Tersedia</option>
                                    @else
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}"
                                                {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama_kelas }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            {{-- BIODATA KELAHIRAN --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    value="{{ old('tempat_lahir') }}" placeholder="Masukkan Tempat Lahir ..." required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    value="{{ old('tanggal_lahir') }}" required>
                            </div>

                            {{-- KONTAK & ORANG TUA --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nama Orang Tua</label>
                                <input type="text" name="nama_orang_tua"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    value="{{ old('nama_orang_tua') }}" placeholder="Masukkan Nama Orang Tua ..."
                                    required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Email Orang Tua</label>
                                <input type="email" name="email_orang_tua"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    value="{{ old('email_orang_tua') }}" placeholder="Masukkan Email Orang Tua ..."
                                    required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">No HP / WA Orang Tua</label>
                                <input type="tel" name="nomor_hp_orang_tua"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    value="{{ old('nomor_hp_orang_tua') }}"
                                    placeholder="Masukkan No HP / WA Orang Tua ..." required>
                            </div>

                            {{-- STATUS & GAMIFIKASI --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Status Siswa</label>
                                <select name="status"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>
                                        Aktif
                                    </option>

                                    <option value="tidak aktif" {{ old('status') == 'tidak aktif' ? 'selected' : '' }}>
                                        Tidak Aktif
                                    </option>

                                    <option value="lulus" {{ old('status') == 'lulus' ? 'selected' : '' }}>
                                        Lulus
                                    </option>
                                </select>
                            </div>

                            {{-- ALAMAT --}}
                            <div class="md:col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Alamat Lengkap</label>
                                <textarea name="alamat" rows="3"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    placeholder="Masukkan Alamat Lengkap ..." required>{{ old('alamat') }}</textarea>
                            </div>
                        </div>

                        {{-- Tombol Simpan --}}
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('siswa.index') }}"
                                class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 mr-2 rounded transition">Batal</a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Simpan Data
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
