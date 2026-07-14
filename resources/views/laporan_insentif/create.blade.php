<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Buat Laporan Insentif Baru') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Sistem akan otomatis merangkum absensi siswa & guru pada bulan yang
                    Anda pilih.</p>
            </div>
            <a href="{{ route('laporan_insentif.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow-sm text-sm transition">
                &larr; Kembali ke Arsip
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('laporan_insentif.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">1. Pilih Bulan Laporan *</label>
                            <input type="month" name="bulan" value="{{ date('Y-m') }}" required
                                class="w-full md:w-1/2 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-2">Pilih bulan dan tahun kegiatan yang ingin direkap.</p>
                        </div>

                        <div class="mb-8">
                            <label class="block text-sm font-bold text-gray-700 mb-2">2. Upload Foto Dokumentasi
                                *</label>
                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-file"
                                    class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 border-indigo-300 hover:bg-indigo-50 transition">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-10 h-10 mb-3 text-indigo-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                            </path>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span
                                                class="font-semibold text-indigo-600">Klik untuk upload</span> atau drag
                                            and drop</p>
                                        <p class="text-xs text-gray-500">Bisa memilih banyak foto sekaligus (PNG, JPG,
                                            JPEG)</p>
                                    </div>
                                    <input id="dropzone-file" type="file" name="dokumentasi[]" multiple
                                        accept="image/*" class="hidden" required />
                                </label>
                            </div>
                            <div id="file-list" class="mt-3 text-sm text-gray-600 font-medium space-y-1"></div>
                        </div>

                        <div class="flex justify-end border-t pt-5">
                            <button type="submit"
                                onclick="this.innerHTML='Sedang Memproses PDF (Harap Tunggu)...'; this.classList.add('opacity-50');"
                                class="bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-3 px-6 rounded-lg shadow transition flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="7 10 12 15 17 10" />
                                    <line x1="12" y1="15" x2="12" y2="3" />
                                </svg>
                                Proses & Hasilkan Laporan PDF
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('dropzone-file').addEventListener('change', function(e) {
            const fileList = document.getElementById('file-list');
            fileList.innerHTML = '';
            for (let i = 0; i < this.files.length; i++) {
                fileList.innerHTML +=
                    `<div class="flex items-center text-emerald-600"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> ${this.files[i].name}</div>`;
            }
        });
    </script>
</x-app-layout>
