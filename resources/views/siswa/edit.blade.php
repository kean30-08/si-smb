<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- IDENTITAS UTAMA --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap"
                                    value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">NIS / ID Vihara *</label>
                                <input type="text" name="nis" value="{{ old('nis', $siswa->nis) }}"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Jenis Kelamin *</label>
                                <select name="jenis_kelamin"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    <option value="L"
                                        {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>
                                        Laki-laki</option>
                                    <option value="P"
                                        {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>
                                        Perempuan</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Kelas</label>
                                <select name="kelas_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
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
                                    value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir"
                                    value="{{ old('tanggal_lahir', $siswa->tanggal_lahir) }}"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            {{-- KONTAK & ORANG TUA --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nama Orang Tua</label>
                                <input type="text" name="nama_orang_tua"
                                    value="{{ old('nama_orang_tua', $siswa->nama_orang_tua) }}"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Email Orang Tua</label>
                                <input type="email" name="email_orang_tua"
                                    value="{{ old('email_orang_tua', $siswa->email_orang_tua) }}"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">No HP / WA Orang Tua</label>
                                <input type="text" name="nomor_hp_orang_tua"
                                    value="{{ old('nomor_hp_orang_tua', $siswa->nomor_hp_orang_tua) }}"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            {{-- STATUS & GAMIFIKASI --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Status Siswa</label>
                                <select name="status"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="aktif"
                                        {{ old('status', $siswa->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="tidak aktif"
                                        {{ old('status', $siswa->status) == 'tidak aktif' ? 'selected' : '' }}>Tidak
                                        Aktif</option>
                                    <option value="lulus"
                                        {{ old('status', $siswa->status) == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Total Poin</label>
                                <input type="number" name="total_poin"
                                    value="{{ old('total_poin', $siswa->total_poin) }}"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            {{-- ALAMAT (Memakan 2 kolom supaya lebar) --}}
                            <div class="md:col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Alamat Lengkap</label>
                                <textarea name="alamat" rows="3"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alamat', $siswa->alamat) }}</textarea>
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('siswa.index') }}"
                                class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 mr-2 rounded transition">Batal</a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Data
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
