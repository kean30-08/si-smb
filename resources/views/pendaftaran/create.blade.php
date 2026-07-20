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
                                    value="{{ old('nama_lengkap') }}" placeholder="Masukkan Nama Lengkap..." required>
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
                                    value="{{ old('nis') }}" placeholder="Masukkan NIK dari KK..." required>
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
                                    value="{{ old('tempat_lahir') }}"
                                    placeholder="Masukkan Tempat Lahir (Contoh: Tabanan)..." required>
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
                                    value="{{ old('asal_sekolah') }}" placeholder="Contoh: SDN 1 Gianyar..." required>
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
                                    value="{{ old('nama_orang_tua') }}" placeholder="Masukkan Nama Orang Tua/Wali..."
                                    required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Email</label>
                                <input type="email" name="email_orang_tua"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    value="{{ old('email_orang_tua') }}"
                                    placeholder="Opsional (Kosongkan Bila Tidak Ada)...">
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">No HP/WA Aktif Orang Tua/Wali
                                    *</label>
                                <input type="tel" name="nomor_hp_orang_tua"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic @error('nomor_hp_orang_tua') border-red-500 @enderror"
                                    value="{{ old('nomor_hp_orang_tua') }}" placeholder="Contoh: 08123456789..."
                                    required>
                                @error('nomor_hp_orang_tua')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- ALAMAT --}}
                            <div class="md:col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Alamat Lengkap *</label>
                                <textarea name="alamat" rows="3"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:italic"
                                    placeholder="Contoh: Jl. Melati No.18, Delod Peken, Kec. Tabanan..." required>{{ old('alamat') }}</textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
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

    {{-- ======================================================== --}}
    {{-- POP-UP NOTIFIKASI SUKSES & JOIN GRUP WHATSAPP            --}}
    {{-- ======================================================== --}}
    @if (session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-10" 
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-10"
             style="display: none;"
             class="fixed bottom-6 left-6 z-50 w-80 sm:w-96 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
            
            {{-- Bagian Header Hijau --}}
            <div class="bg-green-500 px-4 py-3 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-white font-bold text-sm sm:text-base">Pendaftaran Terkirim!</h3>
            </div>

            {{-- Bagian Konten --}}
            <div class="p-4 bg-white">
                <p class="text-sm text-gray-600 mb-5 leading-relaxed">
                    {{ session('success') }}
                    <br><br>
                    Untuk informasi lebih lanjut, silakan langsung bergabung ke dalam Grup WhatsApp Orang Tua & Siswa SMB melalui tombol di bawah ini.
                </p>

                {{-- Bagian Tombol Aksi (Kanan Bawah) --}}
                <div class="flex justify-end items-center gap-3">
                    <button @click="show = false" type="button" class="px-4 py-2 text-xs font-semibold text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        Tutup
                    </button>
                    <a href="https://chat.whatsapp.com/Hat4nkZ3HS4EkRnF3iY1LQ" target="_blank" @click="show = false" class="inline-flex items-center gap-1 px-4 py-2 text-xs font-bold text-white bg-green-500 hover:bg-green-600 shadow-md rounded-lg transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        Join Grup WA
                    </a>
                </div>
            </div>
        </div>
    @endif
    {{-- ======================================================== --}}

</x-app-layout>