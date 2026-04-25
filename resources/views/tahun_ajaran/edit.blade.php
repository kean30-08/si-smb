<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Tahun Ajaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                            <div class="flex items-center text-red-800 font-bold mb-2">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Terjadi Kesalahan!
                            </div>
                            <ul class="list-disc list-inside text-sm text-red-700 ml-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- PEMECAHAN STRING DARI DATABASE --}}
                    @php
                        // Memecah "2026/2027 Ganjil" -> ["2026/2027", "Ganjil"]
                        $parts = explode(' ', $tahun_ajaran->tahun_ajaran);
                        $years = explode('/', $parts[0]); // Memecah "2026/2027" -> ["2026", "2027"]

                        $tahun_awal_db = $years[0] ?? '';
                        $semester_db = $parts[1] ?? 'Ganjil';
                    @endphp

                    <form action="{{ route('tahun_ajaran.update', $tahun_ajaran->id) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="mb-6">
                            <label class="block font-bold text-sm text-gray-700 mb-2">Format Tahun Ajaran &
                                Semester</label>

                            <div class="flex items-center gap-3" x-data="{ tahunAwal: '{{ old('tahun_awal', $tahun_awal_db) }}' }">

                                <div class="w-1/3">
                                    <input type="number" name="tahun_awal" x-model="tahunAwal" min="2000"
                                        max="2099"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-center"
                                        required>
                                </div>

                                <span class="text-2xl font-black text-gray-400">/</span>

                                <div class="w-1/3">
                                    <input type="number" disabled :value="tahunAwal ? parseInt(tahunAwal) + 1 : ''"
                                        class="w-full border-gray-200 bg-gray-100 text-gray-500 rounded-md shadow-sm text-center font-bold cursor-not-allowed">
                                </div>

                                <div class="w-1/3">
                                    <select name="semester"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold text-indigo-700 bg-indigo-50 cursor-pointer"
                                        required>
                                        <option value="Ganjil"
                                            {{ old('semester', $semester_db) == 'Ganjil' ? 'selected' : '' }}>Ganjil
                                        </option>
                                        <option value="Genap"
                                            {{ old('semester', $semester_db) == 'Genap' ? 'selected' : '' }}>Genap
                                        </option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                            <a href="{{ route('tahun_ajaran.index') }}"
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
