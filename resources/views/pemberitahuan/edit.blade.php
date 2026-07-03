<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Pemberitahuan') }}
            </h2>
            <a href="{{ route('pemberitahuan.index') }}"
                class="w-full sm:w-auto text-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow transition">
                Batal & Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:p-8">

                <form action="{{ route('pemberitahuan.update', $pemberitahuan->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Judul --}}
                    <div class="mb-5">
                        <label for="judul" class="block text-sm font-bold text-gray-700 mb-1">Judul Pemberitahuan
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" id="judul"
                            value="{{ old('judul', $pemberitahuan->judul) }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-5">
                        <label for="deskripsi" class="block text-sm font-bold text-gray-700 mb-1">Isi Pemberitahuan
                            <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi" id="deskripsi" rows="8" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('deskripsi', $pemberitahuan->deskripsi) }}</textarea>
                    </div>

                    {{-- Gambar --}}
                    <div class="mb-5 p-5 bg-gray-50 border border-gray-200 rounded-lg">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Poster / Gambar Pendukung</label>

                        @if ($pemberitahuan->gambar)
                            <div class="mb-4 bg-white p-3 border border-gray-200 rounded-md inline-block">
                                <p class="text-xs text-gray-500 font-semibold mb-2">Gambar Saat Ini:</p>
                                <img src="{{ asset($pemberitahuan->gambar) }}" alt="Gambar"
                                    class="max-w-full h-auto rounded shadow-sm object-contain">
                            </div>
                    </div>
                    @endif

                    <div class="mt-2">
                        <label for="gambar" class="block text-xs font-semibold text-gray-700 mb-1">Ganti Gambar
                            Baru (Opsional)</label>
                        <input type="file" name="gambar" id="gambar" accept="image/*"
                            class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-yellow-100 file:text-yellow-800 hover:file:bg-yellow-200 cursor-pointer">
                        <p class="text-xs text-gray-500 mt-2">Biarkan kosong jika tidak ingin mengubah gambar.</p>
                    </div>
            </div>

            {{-- Status --}}
            <div class="mb-8">
                <label for="status" class="block text-sm font-bold text-gray-700 mb-1">Status Publikasi <span
                        class="text-red-500">*</span></label>
                <select name="status" id="status" required
                    class="w-full sm:w-1/3 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-medium">
                    <option value="aktif" {{ old('status', $pemberitahuan->status) == 'aktif' ? 'selected' : '' }}>
                        Aktif
                        (Tampilkan)</option>
                    <option value="arsip" {{ old('status', $pemberitahuan->status) == 'arsip' ? 'selected' : '' }}>
                        Arsip
                        (Sembunyikan)</option>
                </select>
            </div>

            {{-- Tombol Submit --}}
            <div class="flex items-center gap-4 pt-5 border-t border-gray-200">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2.5 px-6 rounded shadow-md transition flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Perbarui Data
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
                title: 'Oops... Gagal Memperbarui',
                html: '<ul class="text-left text-sm text-red-600 mt-2">@foreach ($errors->all() as $error)<li>&bull; {{ $error }}</li>@endforeach</ul>',
                confirmButtonColor: '#4f46e5'
            });
        @endif
    </script>
</x-app-layout>
