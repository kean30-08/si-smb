<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Kelas Baru') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">



                    <form action="{{ route('kelas.store') }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <label class="block font-bold text-sm text-gray-700 mb-2">Format Penamaan Kelas</label>

                            {{-- INISIALISASI ALPINE.JS --}}
                            <div class="flex items-center gap-3" x-data="{ jenjang: '{{ old('jenjang', '') }}' }">

                                {{-- 1. Teks KELAS (Paten/Mati) --}}
                                <div class="flex-shrink-0 w-24">
                                    <input type="text" disabled value="Kelas"
                                        class="w-full border-gray-200 bg-gray-100 text-gray-500 rounded-md shadow-sm text-center font-bold cursor-not-allowed">
                                </div>

                                {{-- 2. Input Nomor/Tingkat (Hanya muncul jika SD/SMP/SMA) --}}
                                <div class="flex-1" x-show="['SD', 'SMP', 'SMA'].includes(jenjang)"
                                    style="display: none;">
                                    <input type="number" name="tingkat" value="{{ old('tingkat') }}"
                                        placeholder="Nomor"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold"
                                        x-bind:min="1" x-bind:max="jenjang === 'SD' ? 6 : 3"
                                        x-bind:required="['SD', 'SMP', 'SMA'].includes(jenjang)">
                                </div>

                                {{-- 3. Dropdown Jenjang --}}
                                <div class="flex-1">
                                    <select name="jenjang" x-model="jenjang"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-indigo-700 bg-indigo-50 cursor-pointer"
                                        required>
                                        <option value="" disabled selected>-- Pilih Jenjang --</option>
                                        <option value="TK">TK</option>
                                        <option value="PAUD">PAUD</option>
                                        <option value="SD">SD</option>
                                        <option value="SMP">SMP</option>
                                        <option value="SMA">SMA</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Petunjuk Dinamis --}}
                            <div class="mt-2 text-xs font-bold text-gray-500" x-show="jenjang !== ''">
                                <p x-show="jenjang === 'TK' || jenjang === 'PAUD'" class="text-blue-600">Info: Jenjang
                                    ini tidak memerlukan nomor kelas.</p>
                                <p x-show="jenjang === 'SD'" class="text-amber-600">Info: Limit nomor kelas untuk SD
                                    adalah 1 sampai 6.</p>
                                <p x-show="jenjang === 'SMP' || jenjang === 'SMA'" class="text-amber-600">Info: Limit
                                    nomor kelas untuk SMP/SMA adalah 1 sampai 3.</p>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                            <a href="{{ route('kelas.index') }}"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded transition">Batal</a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">Simpan
                                Kelas</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
