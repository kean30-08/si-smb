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

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('agenda.store') }}" method="POST">
                        @csrf

                        {{-- Input Tanggal (Hanya 1 untuk seluruh rangkaian acara) --}}
                        <div class="mb-6 border-b pb-6">
                            <label class="block font-bold text-lg text-gray-800 mb-2">Tanggal Pelaksanaan *</label>
                            <input type="date" name="tanggal" value="{{ old('tanggal') }}" class="block w-full md:w-1/3 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>

                        {{-- Wadah untuk menampung baris form rundown kegiatan --}}
                        <div id="rundown-container">
                            {{-- Baris Form Pertama (Default) --}}
                            <div class="rundown-item bg-gray-50 p-4 rounded-md border border-gray-200 mb-4 relative">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block font-medium text-sm text-gray-700">Nama Kegiatan *</label>
                                        <input type="text" name="nama_kegiatan[]" placeholder="Contoh: Puja Bakti, Meditasi, dll" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
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
                                        <input type="text" name="deskripsi_rundown[]" placeholder="Opsional..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol untuk menambah baris form dengan Javascript --}}
                        <button type="button" onclick="addRundown()" class="mt-2 mb-6 text-sm font-semibold text-blue-600 hover:text-blue-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                            Tambah Acara ke Jadwal Ini
                        </button>

                        <div class="flex items-center justify-end mt-6 border-t pt-4">
                            <a href="{{ route('agenda.index') }}" class="text-gray-600 underline mr-4">Batal</a>
                            <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition">
                                Simpan Seluruh Rangkaian
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk Duplikasi Baris Form --}}
    <script>
        function addRundown() {
            const container = document.getElementById('rundown-container');
            const rowHtml = `
                <div class="rundown-item bg-gray-50 p-4 rounded-md border border-gray-200 mb-4 relative mt-4">
                    <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-sm font-bold flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
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
            // Sisipkan form baru di bagian bawah container
            container.insertAdjacentHTML('beforeend', rowHtml);
        }
    </script>
</x-app-layout>