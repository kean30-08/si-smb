<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Tahun Ajaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">



                    <form action="{{ route('tahun_ajaran.store') }}" method="POST">
                        @csrf

                        <div class="mb-6">
                            <label class="block font-bold text-sm text-gray-700 mb-2">Format Tahun Ajaran &
                                Semester</label>

                            {{-- Alpine.js untuk menghitung tahun_akhir secara otomatis --}}
                            <div class="flex items-center gap-3" x-data="{ tahunAwal: '{{ old('tahun_awal', date('Y')) }}' }">

                                {{-- Input Tahun Awal --}}
                                <div class="w-1/3">
                                    <input type="number" name="tahun_awal" x-model="tahunAwal" min="2000"
                                        max="2099"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-center"
                                        placeholder="2026" required>
                                </div>

                                <span class="text-2xl font-black text-gray-400">/</span>

                                {{-- Input Tahun Akhir (Readonly, dihitung otomatis) --}}
                                <div class="w-1/3">
                                    <input type="number" disabled :value="tahunAwal ? parseInt(tahunAwal) + 1 : ''"
                                        class="w-full border-gray-200 bg-gray-100 text-gray-500 rounded-md shadow-sm text-center font-bold cursor-not-allowed">
                                </div>

                                {{-- Dropdown Semester --}}
                                <div class="w-1/3">
                                    <select name="semester"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-indigo-700 bg-indigo-50 cursor-pointer"
                                        required>
                                        <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>
                                            Ganjil</option>
                                        <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap
                                        </option>
                                    </select>
                                </div>

                            </div>
                            <p class="mt-2 text-xs text-gray-500">Tahun kedua akan terisi otomatis berdasarkan tahun
                                pertama yang Anda ketik.</p>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                            <a href="{{ route('tahun_ajaran.index') }}"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded transition">Batal</a>
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
