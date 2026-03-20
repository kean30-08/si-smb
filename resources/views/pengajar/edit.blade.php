<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Pengajar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('pengajar.update', $pengajar->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- TOGGLE EDIT AKUN LOGIN --}}
                            <div
                                class="md:col-span-2 border-b pb-4 mb-2 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                                <h3 class="text-lg font-bold text-gray-800">1. Data Akun Login</h3>
                                <label
                                    class="inline-flex items-center cursor-pointer mt-2 sm:mt-0 bg-yellow-100 px-3 py-1.5 rounded-md border border-yellow-300">
                                    <input type="checkbox" name="ubah_kredensial" id="toggleKredensial"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        {{ old('ubah_kredensial') ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-yellow-800 font-bold">Ubah Email & Password
                                        Login</span>
                                </label>
                            </div>

                            {{-- KONTANER EMAIL & PASSWORD (Sembunyi by default) --}}
                            <div id="kredensialContainer"
                                class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 {{ old('ubah_kredensial') ? '' : 'hidden' }} bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                                <div class="md:col-span-2">
                                    <label class="block font-medium text-sm text-gray-700">Email Login</label>
                                    <input type="email" name="email" id="emailInput"
                                        value="{{ old('email', $pengajar->user->email ?? '') }}"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('email') border-red-500 @enderror"
                                        required placeholder="Masukkan Email Aktif Pengajar">
                                    @error('email')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block font-medium text-sm text-gray-700">Password Login Baru</label>
                                    <input type="password" name="password"
                                        placeholder="Kosongkan jika tidak ingin diubah"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm placeholder:italic @error('password') border-red-500 @enderror">
                                    @error('password')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    {{-- WAJIB ADA INI AGAR VALIDASI 'CONFIRMED' BEKERJA --}}
                                    <label class="block font-medium text-sm text-gray-700">Konfirmasi Password
                                        Baru</label>
                                    <input type="password" name="password_confirmation"
                                        placeholder="Ketik ulang password baru"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm placeholder:italic">
                                </div>
                            </div>


                            {{-- BIODATA PENGAJAR --}}
                            <div class="md:col-span-2 border-b pb-4 mt-2 mb-2">
                                <h3 class="text-lg font-bold text-gray-800">2. Biodata Pengajar</h3>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap"
                                    value="{{ old('nama_lengkap', $pengajar->nama_lengkap) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('nama_lengkap') border-red-500 @enderror"
                                    required>
                                @error('nama_lengkap')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Jenis Kelamin</label>
                                <select name="jenis_kelamin"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="L"
                                        {{ old('jenis_kelamin', $pengajar->jenis_kelamin) == 'L' ? 'selected' : '' }}>
                                        Laki-laki</option>
                                    <option value="P"
                                        {{ old('jenis_kelamin', $pengajar->jenis_kelamin) == 'P' ? 'selected' : '' }}>
                                        Perempuan</option>
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
                                                {{ old('jabatan_id', $pengajar->jabatan_id) == $jabatan->id ? 'selected' : '' }}>
                                                {{ $jabatan->nama_jabatan }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nomor HP / WA</label>
                                {{-- Ubah jadi type="tel" --}}
                                <input type="tel" name="nomor_hp"
                                    value="{{ old('nomor_hp', $pengajar->nomor_hp) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('nomor_hp') border-red-500 @enderror"
                                    required>
                                @error('nomor_hp')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Alamat Lengkap</label>
                                <textarea name="alamat" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('alamat', $pengajar->alamat) }}</textarea>
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pengajar.index') }}"
                                class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 mr-2 rounded transition">Batal</a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                Update Data
                            </button>
                        </div>

                    </form>



                </div>
            </div>
        </div>
    </div>
    {{-- SCRIPT UNTUK TOGGLE EMAIL & PASSWORD --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleKredensial');
            const container = document.getElementById('kredensialContainer');
            const emailInput = document.getElementById('emailInput');

            toggleBtn.addEventListener('change', function() {
                if (this.checked) {
                    // Munculkan container
                    container.classList.remove('hidden');
                    // Jadikan email wajib diisi
                    emailInput.setAttribute('required', 'required');
                } else {
                    // Sembunyikan container
                    container.classList.add('hidden');
                    // Cabut status wajib diisi agar form bisa di-submit
                    emailInput.removeAttribute('required');
                }
            });
        });
    </script>
</x-app-layout>
