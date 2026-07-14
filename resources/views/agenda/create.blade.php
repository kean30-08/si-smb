<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Jadwal Absensi Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('agenda.store') }}" method="POST">
                        @csrf

                        <div class="space-y-6 mb-6">
                            {{-- KOLOM NAMA KEGIATAN --}}
                            <div>
                                <label class="block font-bold text-lg text-gray-800 mb-2">Deskripsi Agenda Kegiatan
                                    *</label>
                                <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan') }}"
                                    maxlength="100"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Contoh: Sekolah Minggu Pekan ke-1 (Maksimal 100 Huruf)" required>
                                @error('nama_kegiatan')
                                    <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p>
                                @enderror
                            </div>
                            {{-- KOLOM TANGGAL --}}
                            <div>
                                <label class="block font-bold text-lg text-gray-800 mb-2">Tanggal Pelaksanaan *</label>

                                @php
                                    $tahunAktif = \App\Models\TahunAjaran::where('status', 'aktif')->first();
                                @endphp
                                <p
                                    class="text-xs font-bold text-indigo-700 bg-indigo-50 p-2 rounded border border-indigo-200 mb-3 w-full">
                                    Info: Jadwal ini akan otomatis dimasukkan ke dalam Tahun Ajaran:
                                    {{ $tahunAktif ? $tahunAktif->tahun_ajaran : 'BELUM ADA TAHUN AJARAN AKTIF' }}
                                </p>

                                <input type="date" name="tanggal" value="{{ old('tanggal') }}"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>

                                @error('tanggal')
                                    <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- KOLOM PUBLIKASI --}}
                            <div>
                                <label class="block font-bold text-lg text-gray-800 mb-2">Status Publikasi *</label>
                                <select name="is_public" required
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1" {{ old('is_public') == '1' ? 'selected' : '' }}>Publik
                                        (Ditampilkan untuk Umum)</option>
                                    <option value="0" {{ old('is_public') == '0' ? 'selected' : '' }}>Internal
                                        (Hanya Pengajar & Admin)</option>
                                </select>
                                @error('is_public')
                                    <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- KOLOM STATUS LIBUR --}}
                            <div>
                                <label class="block font-bold text-lg text-gray-800 mb-2">Status Kegiatan *</label>
                                <select name="is_libur" required
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="0" {{ old('is_libur') == '0' ? 'selected' : '' }}>Kegiatan
                                        Normal (Wajib Absen)</option>
                                    <option value="1" {{ old('is_libur') == '1' ? 'selected' : '' }}>Hari Libur
                                        (Tidak Ada Absensi)</option>
                                </select>
                                @error('is_libur')
                                    <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- KOLOM PENANGGUNG JAWAB --}}
                            <div class="border-t border-gray-200 pt-6">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block font-bold text-lg text-gray-800">Penanggung Jawab
                                        Absensi</label>
                                    <button type="button" onclick="tambahPic()"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-md text-xs font-bold border border-blue-300 shadow-sm transition flex items-center shrink-0">
                                        + Tambah Penanggung Jawab
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 mb-3">Pilih pengajar yang bertugas mengabsensi pada
                                    hari ini (opsional, bisa lebih dari satu).</p>

                                <div id="pic-container" class="space-y-3">
                                    <div class="pic-row flex items-center gap-2">
                                        <select name="penanggung_jawab_id[]"
                                            class="select2-pic flex-1 text-sm font-medium border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 py-2">
                                            <option value="" disabled selected>-- Cari dan Pilih Pengajar --
                                            </option>
                                            @foreach ($pengajars as $pengajar)
                                                <option value="{{ $pengajar->id }}">
                                                    {{ $pengajar->nama_lengkap }}
                                                    ({{ $pengajar->jabatan->nama_jabatan ?? 'Pengajar' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" onclick="hapusPic(this)"
                                            class="p-2.5 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition border border-red-200"
                                            title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M18 6 6 18" />
                                                <path d="m6 6 12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 border-t border-gray-200 pt-6">
                            <a href="{{ route('agenda.index') }}"
                                class="bg-gray-100 border border-gray-300 hover:bg-gray-200 text-gray-800 font-bold py-2.5 px-6 mr-3 rounded-lg shadow-sm transition">Batal</a>
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition">
                                Simpan Jadwal
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- Script Menambah & Menghapus PIC --}}
    <script>
        function tambahPic() {
            const container = document.getElementById('pic-container');
            const rowHtml = `
                <div class="pic-row flex items-center gap-2 mt-3">
                    <select name="penanggung_jawab_id[]" class="select2-pic flex-1 text-sm font-medium border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 py-2">
                        <option value="" disabled selected>-- Cari dan Pilih Pengajar --</option>
                        @foreach ($pengajars as $pengajar)
                            <option value="{{ $pengajar->id }}">{{ addslashes($pengajar->nama_lengkap) }} ({{ addslashes($pengajar->jabatan->nama_jabatan ?? 'Pengajar') }})</option>
                        @endforeach
                    </select>
                    <button type="button" onclick="hapusPic(this)" class="p-2.5 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition border border-red-200" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', rowHtml);
            $('.select2-pic').last().select2({
                width: '100%'
            });
        }

        function hapusPic(btn) {
            const rows = document.getElementsByClassName('pic-row');
            if (rows.length > 1) {
                $(btn).closest('.pic-row').find('.select2-pic').select2('destroy');
                btn.closest('.pic-row').remove();
            } else {
                $(btn).closest('.pic-row').find('.select2-pic').val('').trigger('change');
            }
        }
    </script>

    {{-- LIBRARY JQUERY & SELECT2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-pic').select2({
                width: '100%'
            });
        });
    </script>
    <style>
        .select2-container .select2-selection--single {
            height: 42px !important;
            border-color: #d1d5db !important;
            display: flex;
            align-items: center;
            border-radius: 0.375rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
    </style>
</x-app-layout>
