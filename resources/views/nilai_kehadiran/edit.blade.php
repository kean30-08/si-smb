<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Manual Nilai Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6 bg-gray-100 p-4 rounded-lg">
                        <p><strong>Nama Siswa:</strong> {{ $nilai_kehadiran->siswa->nama_lengkap ?? '-' }}</p>
                        <p><strong>NIS:</strong> {{ $nilai_kehadiran->siswa->nis ?? '-' }}</p>
                    </div>

                    <form action="{{ route('nilai_kehadiran.update', $nilai_kehadiran->id) }}" method="POST">
                        @csrf @method('PUT')

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Ubah Kelas</label>
                            <select name="kelas_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                required>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}"
                                        {{ $nilai_kehadiran->kelas_id == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Total Poin Saat Ini</label>
                            <input type="number" name="total_poin"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                value="{{ old('total_poin', $nilai_kehadiran->total_poin) }}" min="0" required>
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('nilai_kehadiran.index') }}"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded mr-2">Batal</a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Simpan
                                Perubahan</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
