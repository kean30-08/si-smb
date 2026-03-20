<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Pengajar Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('pengajar.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Akun Login (User) --}}
                            <div class="md:col-span-2 border-b pb-4 mb-2">
                                <h3 class="text-lg font-bold text-gray-800">1. Data Akun Login</h3>
                                <p class="text-sm text-gray-500">Gunakan email aktif dan password minimal 6 karakter.
                                </p>
                            </div>

                            {{-- Email dibuat memanjang (col-span-2) agar rapi --}}
                            <div class="md:col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Email Login </label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:italic @error('email') border-red-500 @enderror"
                                    required placeholder="Masukkan Email Aktif Pengajar">
                                @error('email')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror

                            </div>

                            {{-- Password --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Password Login </label>
                                <input type="password" name="password"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:italic @error('password') border-red-500 @enderror"
                                    required minlength="6" placeholder="Masukkan Minimal 6 Katakter">
                                @error('password')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror

                            </div>

                            {{-- Konfirmasi Password (Baru) --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:italic"
                                    required minlength="6" placeholder="Ketikan Ulang Password">
                            </div>

                            {{-- Biodata Pengajar --}}
                            <div class="md:col-span-2 border-b pb-4 mt-4 mb-2">
                                <h3 class="text-lg font-bold text-gray-800">2. Biodata Pengajar</h3>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:italic"
                                    required placeholder="Masukkan Nama Lengkap Pengajar">
                            </div>

                            {{-- NIP --}}
                            {{-- <div>
                                <label class="block font-medium text-sm text-gray-700">NIP / ID Pengajar</label>
                                <input type="text" name="nip" value="{{ old('nip') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div> --}}

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Jenis Kelamin</label>
                                <select name="jenis_kelamin"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki
                                    </option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Jabatan</label>
                                <select name="jabatan_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>

                                    @if ($jabatans->isEmpty())
                                        <option value="">Tidak Ada Jabatan Tersedia</option>
                                    @else
                                        @foreach ($jabatans as $jabatan)
                                            <option value="{{ $jabatan->id }}"
                                                {{ old('jabatan_id') == $jabatan->id ? 'selected' : '' }}>
                                                {{ $jabatan->nama_jabatan }}
                                            </option>
                                        @endforeach
                                    @endif

                                </select>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nomor HP / WA</label>
                                <input type="text" name="nomor_hp" value="{{ old('nomor_hp') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:italic class:@error('nomor_hp') border-red-500 @enderror"
                                    placeholder="Masukkan No HP/WA Aktif" required>
                                @error('nomor_hp')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror

                            </div>

                            <div class="md:col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Alamat Lengkap</label>
                                <textarea name="alamat" rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:italic"
                                    placeholder="Masukkan Alamat Lengkap Pengajar" required>{{ old('alamat') }}</textarea>
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pengajar.index') }}"
                                class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 mr-2 rounded transition">Batal</a>
                            <button type="submit"
                                class="bg-blue-800 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                Simpan Data
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal!',
                    // Mengambil error pertama saja agar pop-up tidak terlalu panjang
                    text: '{{ $errors->first() }}',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Mengerti'
                });
            });
        </script>
    @endif
</x-app-layout>
