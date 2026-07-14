<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('PENDATAAN SISWA AGAMA BUDDHA SMB VIHARA DHARMA CATTRA TABANAN PG/TK-SD') }}
            </h2>
            {{-- MENGAMBIL TAHUN AJARAN AKTIF --}}
            @php
                $tahunAktif = \App\Models\TahunAjaran::where('status', 'aktif')->first();
            @endphp

            <p class="text-sm text-gray-600 max-w-4xl leading-relaxed">
                Pendataan Siswa Agama Buddha SMB Vihara Dharma Cattra Tabanan jenjang PG/TK-SD Tahun Ajaran <span
                    class="font-bold text-indigo-600">{{ $tahunAktif ? $tahunAktif->tahun_ajaran : 'Belum Ada TA Aktif' }}</span>
                untuk kebutuhan administrasi dan arsip SMB.
            </p>
        </div>
    </x-slot>



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('pendaftaran.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- IDENTITAS UTAMA --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic @error('nama_lengkap') border-red-500 @enderror"
                                    value="{{ old('nama_lengkap') }}" placeholder="Masukkan Nama Lengkap ..." required>
                                @error('nama_lengkap')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nama Panggilan *</label>
                                <input type="text" name="nama_panggilan"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm @error('nama_panggilan') border-red-500 @enderror"
                                    value="{{ old('nama_panggilan') }}" placeholder="Contoh: Ryan, Jessica..." required>
                                @error('nama_panggilan')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">NIK</label>
                                <input type="text" name="nis"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic @error('nis') border-red-500 @enderror"
                                    value="{{ old('nis') }}" placeholder="Masukkan NIK dari KK ..." required>
                                @error('nis')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Jenis Kelamin *</label>
                                <select name="jenis_kelamin"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>
                                        Laki-laki
                                    </option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>
                                        Perempuan</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Kelas Tujuan *</label>
                                <select name="kelas_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    <option value="" disabled selected>-- Pilih Kelas --</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}"
                                            {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- BIODATA KELAHIRAN --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Tempat Lahir *</label>
                                <input type="text" name="tempat_lahir"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    value="{{ old('tempat_lahir') }}" placeholder="Masukkan Tempat Lahir ..." required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Tanggal Lahir *</label>
                                <input type="date" name="tanggal_lahir"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    value="{{ old('tanggal_lahir') }}" required>
                            </div>

                            {{-- PENDIDIKAN & KONTAK PRIBADI --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Asal Sekolah</label>
                                <input type="text" name="asal_sekolah"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    value="{{ old('asal_sekolah') }}" placeholder="Contoh: SDN 1 Gianyar...">
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nomor HP / WA Pribadi
                                    (Siswa)</label>
                                <input type="text" name="nomor_hp_siswa"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    value="{{ old('nomor_hp_siswa') }}"
                                    placeholder="Opsional (Kosongkan Bila Tidak Ada)...">
                            </div>

                            {{-- KONTAK & ORANG TUA --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Nama Orang Tua/Wali *</label>
                                <input type="text" name="nama_orang_tua"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    value="{{ old('nama_orang_tua') }}" placeholder="Masukkan Nama Orang Tua/Wali ..."
                                    required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Email</label>
                                <input type="email" name="email_orang_tua"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    value="{{ old('email_orang_tua') }}" placeholder="(Opsional) Masukkan Email ...">
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">No HP / WA Orang Tua/Wali
                                    *</label>
                                <input type="tel" name="nomor_hp_orang_tua"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic @error('nomor_hp_orang_tua') border-red-500 @enderror"
                                    value="{{ old('nomor_hp_orang_tua') }}"
                                    placeholder="Masukkan No HP / WA yang aktif ..." required>
                                @error('nomor_hp_orang_tua')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- ALAMAT --}}
                            <div class="md:col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Alamat Lengkap *</label>
                                <textarea name="alamat" rows="3"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    placeholder="Masukkan Alamat Lengkap ..." required>{{ old('alamat') }}</textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            {{-- DIUBAH: Batal mengarah ke halaman agenda publik --}}
                            <a href="{{ route('agenda.index') }}"
                                class="bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-800 font-bold py-2.5 px-6 mr-3 rounded-lg shadow-sm transition">Batal</a>
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition">
                                Kirim Pendaftaran
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
