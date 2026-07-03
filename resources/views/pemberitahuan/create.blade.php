<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Buat Pemberitahuan Baru') }}
            </h2>
            <a href="{{ route('pemberitahuan.index') }}"
                class="w-full sm:w-auto text-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow transition">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-8">

                <form action="{{ route('pemberitahuan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Judul --}}
                    <div class="mb-5">
                        <label for="judul" class="block text-sm font-bold text-gray-700 mb-1">Judul Pemberitahuan
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" id="judul" value="{{ old('judul') }}" required
                            placeholder="Contoh: Jadwal Libur Semester Ganjil"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-5">
                        <label for="deskripsi" class="block text-sm font-bold text-gray-700 mb-1">Isi Pemberitahuan
                            <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi" id="deskripsi" rows="8" required placeholder="Ketikkan detail informasi di sini..."
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('deskripsi') }}</textarea>
                    </div>

                    {{-- Gambar --}}
                    <div class="mb-5 p-5 bg-gray-50 border border-gray-200 rounded-lg border-dashed">
                        <label for="gambar" class="block text-sm font-bold text-gray-700 mb-2">Unggah Poster/Gambar
                            (Opsional)</label>
                        <input type="file" name="gambar" id="gambar" accept="image/*"
                            class="block w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 cursor-pointer">
                        <p class="text-xs text-gray-500 mt-2 font-medium">Maksimal ukuran file: 2MB. Format: JPG, PNG,
                            JPEG.</p>
                    </div>

                    {{-- Status --}}
                    <div class="mb-8">
                        <label for="status" class="block text-sm font-bold text-gray-700 mb-1">Status Publikasi <span
                                class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                            class="w-full sm:w-1/3 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-medium">
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif (Tampilkan)
                            </option>
                            <option value="arsip" {{ old('status') == 'arsip' ? 'selected' : '' }}>Arsip (Sembunyikan)
                            </option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Jika memilih Arsip, pemberitahuan tidak akan terlihat oleh
                            siswa.</p>
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="flex items-center gap-4 pt-5 border-t border-gray-200">
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2.5 px-6 rounded shadow-md transition flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan & Publikasikan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- SweetAlert untuk Error Validasi --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Oops... Gagal Menyimpan',
                html: '<ul class="text-left text-sm text-red-600 mt-2">@foreach ($errors->all() as $error)<li>&bull; {{ $error }}</li>@endforeach</ul>',
                confirmButtonColor: '#4f46e5'
            });
        @endif
    </script>
</x-app-layout>
