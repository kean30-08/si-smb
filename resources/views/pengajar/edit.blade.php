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

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pengajar.update', $pengajar->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Akun Login (User) --}}
                            <div class="md:col-span-2 border-b pb-4 mb-2">
                                <h3 class="text-lg font-bold text-gray-800">1. Data Akun Login</h3>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Email Login *</label>
                                <input type="email" name="email" value="{{ old('email', $pengajar->user->email ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Password Login Baru</label>
                                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <p class="text-xs text-orange-500 mt-1">*Hanya isi jika ingin mereset password pengajar</p>
                            </div>

                            {{-- Biodata Pengajar --}}
                            <div class="md:col-span-2 border-b pb-4 mt-4 mb-2">
                                <h3 class="text-lg font-bold text-gray-800">2. Biodata Pengajar</h3>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $pengajar->nama_lengkap) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">NIP / ID Pengajar</label>
                                <input type="text" name="nip" value="{{ old('nip', $pengajar->nip) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Jenis Kelamin *</label>
                                <select name="jenis_kelamin" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="L" {{ old('jenis_kelamin', $pengajar->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin', $pengajar->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Jabatan</label>
                                <input type="text" name="jabatan" value="{{ old('jabatan', $pengajar->jabatan) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nomor HP / WA</label>
                                <input type="text" name="nomor_hp" value="{{ old('nomor_hp', $pengajar->nomor_hp) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Alamat Lengkap</label>
                                <textarea name="alamat" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('alamat', $pengajar->alamat) }}</textarea>
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pengajar.index') }}" class="text-gray-600 underline mr-4">Batal</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                Update Data
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>