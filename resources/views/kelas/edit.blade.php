<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Kelas') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">



                    {{-- LOGIKA MEMBONGKAR NAMA KELAS DARI DATABASE --}}
                    @php
                        $nama = $kelas->nama_kelas;
                        $jenjang_db = '';
                        $tingkat_db = '';

                        if (str_contains($nama, 'SD')) {
                            $jenjang_db = 'SD';
                            $tingkat_db = filter_var($nama, FILTER_SANITIZE_NUMBER_INT);
                        } elseif (str_contains($nama, 'SMP')) {
                            $jenjang_db = 'SMP';
                            $tingkat_db = filter_var($nama, FILTER_SANITIZE_NUMBER_INT);
                        } elseif (str_contains($nama, 'SMA')) {
                            $jenjang_db = 'SMA';
                            $tingkat_db = filter_var($nama, FILTER_SANITIZE_NUMBER_INT);
                        } elseif (str_contains($nama, 'TK')) {
                            $jenjang_db = 'TK';
                        } elseif (str_contains($nama, 'PAUD')) {
                            $jenjang_db = 'PAUD';
                        }
                    @endphp

                    <form action="{{ route('kelas.update', $kelas->id) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="mb-6">
                            <label class="block font-bold text-sm text-gray-700 mb-2">Format Penamaan Kelas</label>

                            {{-- INISIALISASI ALPINE DENGAN DATA DATABASE --}}
                            <div class="flex items-center gap-3" x-data="{ jenjang: '{{ old('jenjang', $jenjang_db) }}' }">

                                <div class="flex-shrink-0 w-24">
                                    <input type="text" disabled value="Kelas"
                                        class="w-full border-gray-200 bg-gray-100 text-gray-500 rounded-md shadow-sm text-center font-bold cursor-not-allowed">
                                </div>

                                <div class="flex-1" x-show="['SD', 'SMP', 'SMA'].includes(jenjang)"
                                    style="display: none;">
                                    <input type="number" name="tingkat" value="{{ old('tingkat', $tingkat_db) }}"
                                        placeholder="Nomor"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold"
                                        x-bind:min="1" x-bind:max="jenjang === 'SD' ? 6 : 3"
                                        x-bind:required="['SD', 'SMP', 'SMA'].includes(jenjang)">
                                </div>

                                <div class="flex-1">
                                    <select name="jenjang" x-model="jenjang"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-indigo-700 bg-indigo-50 cursor-pointer"
                                        required>
                                        <option value="" disabled {{ $jenjang_db == '' ? 'selected' : '' }}>--
                                            Pilih Jenjang --</option>
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
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">Update
                                Perubahan</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
