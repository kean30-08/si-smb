<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Agenda Kegiatan') }} <br class="xl:hidden">
                <span class="text-indigo-600">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</span>
            </h2>

            <div class="w-full xl:w-auto flex flex-col sm:flex-row flex-wrap gap-2">
                <a href="{{ route('agenda.index') }}"
                    class="w-full sm:w-auto justify-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg flex items-center shadow-sm transition text-sm">
                    &larr; Kembali ke Jadwal
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- FORM SEKARANG TERBUKA UNTUK SEMUA USER (ADMIN & PENGAJAR) --}}
            <form action="{{ route('agenda.updatePic', $tanggal) }}" method="POST" class="m-0">
                @csrf @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-t-4 border-indigo-500">
                    <div class="bg-indigo-50/50 px-6 py-4 border-b border-gray-100 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="text-indigo-600 mr-2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                            <polyline points="10 9 9 9 8 9" />
                        </svg>
                        <h3 class="text-lg font-bold text-indigo-900">Informasi Agenda Kegiatan</h3>
                    </div>

                    <div class="p-6 text-gray-900 space-y-5">
                        
                        {{-- FORM EDIT TANGGAL & NAMA KEGIATAN --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Kegiatan *</label>
                                <input type="date" name="tanggal_baru"
                                    value="{{ old('tanggal_baru', $tanggal) }}" required
                                    class="w-full text-sm font-medium border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-gray-800 py-2.5">
                                @error('tanggal_baru')
                                    <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Agenda Kegiatan *</label>
                                <input type="text" name="nama_kegiatan"
                                    value="{{ old('nama_kegiatan', $agendas->first()->nama_kegiatan ?? '') }}"
                                    maxlength="100"
                                    class="w-full text-sm font-medium border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-gray-800 py-2.5"
                                    placeholder="Contoh: Sekolah Minggu Pekan ke-1" required>
                                @error('nama_kegiatan')
                                    <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- FORM EDIT STATUS LIBUR --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Status Kegiatan *</label>
                                <select name="is_libur" required
                                    class="w-full text-sm font-medium border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-gray-800 py-2.5">
                                    <option value="0"
                                        {{ old('is_libur', $agendas->first()->is_libur ?? 0) == 0 ? 'selected' : '' }}>
                                        Kegiatan Normal (Wajib Absen)</option>
                                    <option value="1"
                                        {{ old('is_libur', $agendas->first()->is_libur ?? 0) == 1 ? 'selected' : '' }}>
                                        Hari Libur (Tidak Ada Absensi)</option>
                                </select>
                            </div>

                            {{-- FORM EDIT STATUS PUBLIKASI --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Status Publikasi *</label>
                                <select name="is_public" required
                                    class="w-full text-sm font-medium border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-gray-800 py-2.5">
                                    <option value="1"
                                        {{ old('is_public', $agendas->first()->is_public ?? 1) == 1 ? 'selected' : '' }}>
                                        Publik (Ditampilkan untuk Umum)</option>
                                    <option value="0"
                                        {{ old('is_public', $agendas->first()->is_public ?? 1) == 0 ? 'selected' : '' }}>
                                        Internal (Hanya Pengajar & Admin)</option>
                                </select>
                                @error('is_public')
                                    <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TOMBOL SUBMIT --}}
                <div class="flex justify-end mt-2">
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-800 text-white px-8 py-3 rounded-lg text-sm font-bold shadow-md transition duration-150 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                            <polyline points="17 21 17 13 7 13 7 21" />
                            <polyline points="7 3 7 8 15 8" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>