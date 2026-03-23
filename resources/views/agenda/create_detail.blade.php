<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Acara untuk Tanggal:') }} {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('agenda.storeDetail') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Tanggal dikunci (readonly) agar pasti masuk ke jadwal yang sedang dibuka --}}
                            <div class="md:col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Tanggal Pelaksanaan</label>
                                <input type="date" name="tanggal" value="{{ $tanggal }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 cursor-not-allowed"
                                    readonly>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Nama Kegiatan Tambahan *</label>
                                <input type="text" name="nama_kegiatan"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:italic"
                                    placeholder="Masukkan nama kegiatan" required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Waktu Mulai *</label>
                                <input type="time" name="waktu_mulai"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Waktu Selesai *</label>
                                <input type="time" name="waktu_selesai"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Catatan / Deskripsi</label>
                                <textarea name="deskripsi_rundown" rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:italic"
                                    placeholder="Masukkan catatan atau deskripsi kegiatan"></textarea>
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('agenda.showDate', $tanggal) }}"
                                class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 mr-2 rounded transition">Batal</a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                Simpan Acara
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
