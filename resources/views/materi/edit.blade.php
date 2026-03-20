<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Materi Pembelajaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('materi.update', $materi->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Judul Materi</label>
                                <input type="text" name="judul" value="{{ old('judul', $materi->judul) }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Untuk Kelas</label>
                                <select name="kelas_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    required>
                                    @if ($kelas->isEmpty())
                                        <div>
                                            <p class="text-sm text-red-600">Belum ada kelas yang tersedia. Silakan buat
                                                kelas terlebih dahulu.</p>
                                        </div>
                                    @else
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}"
                                                {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama_kelas }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Deskripsi / Rangkuman
                                    Singkat (Opsional)</label>
                                <textarea name="deskripsi" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
                            </div>

                            <div class="p-4 border border-dashed border-gray-300 rounded-md bg-gray-50">
                                <label class="block font-bold text-sm text-gray-800 mb-2">Update File Materi
                                    (Opsional)</label>

                                {{-- Info File Lama --}}
                                @if ($materi->file_materi)
                                    <div
                                        class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded text-sm text-blue-800 flex items-center justify-between">
                                        <span>File saat ini sudah tersedia.</span>
                                        <a href="{{ asset('storage/' . $materi->file_materi) }}" target="_blank"
                                            class="font-bold underline">Lihat File Lama</a>
                                    </div>
                                    <p class="text-xs text-orange-600 mb-3">*Abaikan input di bawah jika tidak ingin
                                        mengganti file lama.</p>
                                @endif

                                <input type="file" name="file_materi"
                                    accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 cursor-pointer">
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('materi.index') }}"
                                class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 mr-2 rounded transition">Batal</a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                Update Materi
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
