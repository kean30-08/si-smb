<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Rangkaian Jadwal Kegiatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('agenda.store') }}" method="POST">
                        @csrf

                        {{-- PERBAIKAN LAYOUT: Grid 2 Kolom Kiri-Kanan untuk Desktop --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6 border-b pb-6">

                            {{-- KOLOM KIRI: Tanggal --}}
                            <div>
                                <label class="block font-bold text-lg text-gray-800 mb-2">Tanggal Pelaksanaan *</label>

                                {{-- PEMBERITAHUAN TAHUN AJARAN AKTIF --}}
                                @php
                                    $tahunAktif = \App\Models\TahunAjaran::where('status', 'aktif')->first();
                                @endphp
                                <p
                                    class="text-xs font-bold text-indigo-700 bg-indigo-50 p-2 rounded border border-indigo-200 mb-3 w-full md:w-3/4">
                                    Info: Jadwal ini akan otomatis dimasukkan ke dalam <br>
                                    Tahun Ajaran:
                                    {{ $tahunAktif ? $tahunAktif->tahun_ajaran : 'BELUM ADA TAHUN AJARAN AKTIF' }}
                                </p>

                                <input type="date" name="tanggal" value="{{ old('tanggal') }}"
                                    class="block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                <p class="text-xs text-gray-500 mt-2">Pilih satu tanggal untuk seluruh rangkaian acara
                                    di bawah.</p>
                            </div>

                            {{-- KOLOM KANAN: Penanggung Jawab Dinamis (Mirip halaman Show) --}}
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block font-bold text-lg text-gray-800">Penanggung Jawab
                                        Kehadiran</label>
                                    <button type="button" onclick="tambahPic()"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-md text-xs font-bold border border-blue-300 shadow-sm transition flex items-center shrink-0">
                                        + Tambah PIC
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 mb-3">Pilih pengajar yang bertugas mengabsensi pada
                                    hari ini (bisa lebih dari satu).</p>

                                {{-- Kontainer Daftar PIC --}}
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

                        {{-- Wadah Rundown Kegiatan --}}
                        <div id="rundown-container">
                            <div class="rundown-item bg-gray-50 p-4 rounded-md border border-gray-200 mb-4 relative">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700">Nama Kegiatan *</label>
                                        <input type="text" name="nama_kegiatan[]"
                                            placeholder="Contoh: Puja Bakti, Meditasi, dll"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm placeholder:italic"
                                            required>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700">Waktu Mulai *</label>
                                        <input type="time" name="waktu_mulai[]"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-sm text-gray-700">Waktu Selesai *</label>
                                        <input type="time" name="waktu_selesai[]"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700">Catatan /
                                            Deskripsi</label>
                                        <input type="text" name="deskripsi_rundown[]" placeholder="Opsional..."
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm placeholder:italic">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="addRundown()"
                            class="mt-2 mb-6 bg-blue-100 border border-blue-400 text-blue-600 hover:bg-blue-200 hover:text-blue-800 rounded-md py-2 px-4 text-sm font-semibold flex items-center transition shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="mr-1">
                                <line x1="12" x2="12" y1="5" y2="19" />
                                <line x1="5" x2="19" y1="12" y2="12" />
                            </svg>
                            Tambah Acara ke Jadwal Ini
                        </button>

                        <div class="flex items-center justify-end mt-6 border-t border-gray-200 pt-6">
                            <a href="{{ route('agenda.index') }}"
                                class="bg-gray-100 border border-gray-300 hover:bg-gray-200 text-gray-800 font-bold py-2.5 px-6 mr-3 rounded-lg shadow-sm transition">Batal</a>
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition">
                                Simpan Seluruh Rangkaian
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- Kumpulan Script Dinamis (Rundown & PIC) --}}
    <script>
        // Logika Menambah Rundown
        function addRundown() {
            const container = document.getElementById('rundown-container');
            const rowHtml = `
                <div class="rundown-item bg-gray-50 p-4 rounded-md border border-gray-200 mb-4 relative mt-4">
                    <button type="button" onclick="this.closest('.rundown-item').remove()" 
                        class="absolute top-3 right-3 bg-red-100 border border-red-400 text-red-600 hover:bg-red-200 hover:text-red-800 text-sm font-bold py-1 px-3 rounded flex items-center transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        Hapus
                    </button>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        <div class="md:col-span-2">
                            <label class="block font-medium text-sm text-gray-700">Nama Kegiatan *</label>
                            <input type="text" name="nama_kegiatan[]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Waktu Mulai *</label>
                            <input type="time" name="waktu_mulai[]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Waktu Selesai *</label>
                            <input type="time" name="waktu_selesai[]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block font-medium text-sm text-gray-700">Catatan / Deskripsi</label>
                            <input type="text" name="deskripsi_rundown[]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', rowHtml);
        }

        // Logika Menambah & Menghapus PIC secara dinamis dengan Select2 Re-init
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

            // Sisipkan HTML
            container.insertAdjacentHTML('beforeend', rowHtml);

            // Re-inisialisasi fungsi Searchable Select2 pada elemen yang baru ditambahkan
            $('.select2-pic').last().select2({
                width: '100%'
            });
        }

        function hapusPic(btn) {
            const rows = document.getElementsByClassName('pic-row');
            if (rows.length > 1) {
                // Hancurkan instance Select2 sebelum menghapus elemen HTML agar memori browser tidak bocor
                $(btn).closest('.pic-row').find('.select2-pic').select2('destroy');
                btn.closest('.pic-row').remove();
            } else {
                // Jika hanya tersisa 1 baris, cukup reset pilihannya
                $(btn).closest('.pic-row').find('.select2-pic').val('').trigger('change');
            }
        }
    </script>

    {{-- LIBRARY JQUERY & SELECT2 (WAJIB DITARUH DI BAWAH) --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi awal saat halaman baru dibuka
            $('.select2-pic').select2({
                width: '100%'
            });
        });
    </script>
    <style>
        /* Modifikasi Kotak Select2 agar mirip dengan tinggi input Tailwind */
        .select2-container .select2-selection--single {
            height: 42px !important;
            border-color: #d1d5db !important;
            /* gray-300 */
            display: flex;
            align-items: center;
            border-radius: 0.375rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
    </style>
</x-app-layout>
