<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail & Penanggung Jawab Absensi') }} <br class="xl:hidden">
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

            @if ($isAdmin)
                <form action="{{ route('agenda.updatePic', $tanggal) }}" method="POST" class="m-0">
                    @csrf @method('PUT')

                    {{-- ========================================== --}}
                    {{-- KARTU 1: INFORMASI DETAIL AGENDA --}}
                    {{-- ========================================== --}}
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
                            {{-- FORM EDIT NAMA KEGIATAN --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Agenda Kegiatan
                                    *</label>
                                <input type="text" name="nama_kegiatan"
                                    value="{{ old('nama_kegiatan', $agendas->first()->nama_kegiatan ?? '') }}"
                                    maxlength="100"
                                    class="w-full text-sm font-medium border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-gray-800 py-2.5"
                                    placeholder="Contoh: Sekolah Minggu Pekan ke-1 (Maksimal 100 Huruf)" required>
                                @error('nama_kegiatan')
                                    <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                                @enderror
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

                    {{-- ========================================== --}}
                    {{-- KARTU 2: DAFTAR PENANGGUNG JAWAB (PIC) --}}
                    {{-- ========================================== --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-t-4 border-blue-500">
                        <div
                            class="bg-blue-50/50 px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="text-blue-600 mr-2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                                <div>
                                    <h3 class="text-lg font-bold text-blue-900">Penanggung Jawab Absensi</h3>
                                    <p class="text-xs text-blue-700">Pengajar yang bertugas memindai kehadiran siswa.
                                    </p>
                                </div>
                            </div>

                            <button type="button" onclick="tambahPic()"
                                class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1.5 rounded-md text-xs font-bold border border-blue-200 shadow-sm transition flex items-center shrink-0">
                                + Tambah Penanggung Jawab
                            </button>
                        </div>

                        <div class="p-6 text-gray-900">
                            <div id="pic-container" class="space-y-3">
                                @if (empty($penanggungJawabIds))
                                    <div class="pic-row flex items-center gap-2">
                                        <select name="penanggung_jawab_id[]"
                                            class="flex-1 text-sm font-medium border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-gray-800 py-2.5">
                                            <option value="" disabled selected>-- Pilih Pengajar --</option>
                                            @foreach ($pengajars as $pengajar)
                                                <option value="{{ $pengajar->id }}">{{ $pengajar->nama_lengkap }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" onclick="hapusPic(this)"
                                            class="p-2.5 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition border border-red-200"
                                            title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M18 6 6 18" />
                                                <path d="m6 6 12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    @foreach ($penanggungJawabIds as $idPic)
                                        <div class="pic-row flex items-center gap-2">
                                            <select name="penanggung_jawab_id[]"
                                                class="flex-1 text-sm font-medium border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-gray-800 py-2.5">
                                                <option value="" disabled>-- Pilih Pengajar --</option>
                                                @foreach ($pengajars as $pengajar)
                                                    <option value="{{ $pengajar->id }}"
                                                        {{ $idPic == $pengajar->id ? 'selected' : '' }}>
                                                        {{ $pengajar->nama_lengkap }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button" onclick="hapusPic(this)"
                                                class="p-2.5 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition border border-red-200"
                                                title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M18 6 6 18" />
                                                    <path d="m6 6 12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
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
            @else
                {{-- ========================================== --}}
                {{-- TAMPILAN READ-ONLY UNTUK PENGAJAR --}}
                {{-- ========================================== --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-gray-400">
                    <div class="p-6 text-gray-900">
                        <div
                            class="mb-6 pb-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-4 justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Deskripsi
                                    Kegiatan</h4>
                                <p class="text-base font-bold text-gray-900">
                                    "{{ $agendas->first()->nama_kegiatan ?? '-' }}"
                                </p>
                            </div>

                            @if ($agendas->first()->is_libur)
                                <div
                                    class="px-4 py-1.5 bg-red-100 text-red-800 border border-red-200 rounded-full text-xs font-bold shadow-sm flex items-center">
                                    <span class="mr-1.5">📌</span> HARI LIBUR
                                </div>
                            @endif
                        </div>

                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Daftar Penanggung
                                Jawab Absensi</h4>
                            <div class="flex flex-wrap gap-2">
                                @if (empty($penanggungJawabIds))
                                    <span class="text-sm font-bold text-gray-400 italic">Belum ada PIC yang ditentukan
                                        untuk hari ini.</span>
                                @else
                                    @foreach ($penanggungJawabIds as $idPic)
                                        @php $namaPic = $pengajars->firstWhere('id', $idPic); @endphp
                                        @if ($namaPic)
                                            <span
                                                class="bg-blue-50 text-blue-800 border border-blue-200 px-4 py-2 rounded-full text-sm font-bold flex items-center shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="mr-2 text-blue-500">
                                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                                    <circle cx="12" cy="7" r="4" />
                                                </svg>
                                                {{ $namaPic->nama_lengkap }}
                                            </span>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT DINAMIS --}}
    <script>
        function tambahPic() {
            const container = document.getElementById('pic-container');
            const html = `
                    <div class="pic-row flex items-center gap-2 mt-3">
                        <select name="penanggung_jawab_id[]" class="flex-1 text-sm font-medium border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-gray-800 py-2.5">
                            <option value="" disabled selected>-- Pilih Pengajar --</option>
                            @foreach ($pengajars as $pengajar)
                                <option value="{{ $pengajar->id }}">{{ addslashes($pengajar->nama_lengkap) }}</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="hapusPic(this)" class="p-2.5 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition border border-red-200" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>
                `;
            container.insertAdjacentHTML('beforeend', html);
        }

        function hapusPic(btn) {
            const rows = document.getElementsByClassName('pic-row');
            if (rows.length > 1) {
                btn.closest('.pic-row').remove();
            } else {
                btn.closest('.pic-row').querySelector('select').value = "";
            }
        }
    </script>
</x-app-layout>
