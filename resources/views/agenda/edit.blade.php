<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit / Update Status Agenda') }}
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

                    <form action="{{ route('agenda.update', $agenda->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Pilih Status (Ditampilkan paling atas agar mudah diupdate) --}}
                            <div class="md:col-span-2 bg-blue-50 p-4 rounded-md border border-blue-200">
                                <label class="block font-bold text-sm text-blue-800 mb-2">Update Status Kegiatan</label>
                                <select name="status" class="block w-full md:w-1/2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    <option value="akan datang" {{ old('status', $agenda->status) == 'akan datang' ? 'selected' : '' }}>Akan Datang</option>
                                    <option value="sedang berlangsung" {{ old('status', $agenda->status) == 'sedang berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                                    <option value="selesai" {{ old('status', $agenda->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="batal" {{ old('status', $agenda->status) == 'batal' ? 'selected' : '' }}>Batal</option>
                                </select>
                            </div>

                            <div class="md:col-span-2 mt-2">
                                <label class="block font-medium text-sm text-gray-700">Nama Kegiatan *</label>
                                <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan', $agenda->nama_kegiatan) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Tanggal *</label>
                                <input type="date" name="tanggal" value="{{ old('tanggal', $agenda->tanggal) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-medium text-sm text-gray-700">Waktu Mulai *</label>
                                    {{-- Menggunakan \Carbon\Carbon agar format waktu jam:menit sesuai standar form input HTML --}}
                                    <input type="time" name="waktu_mulai" value="{{ old('waktu_mulai', \Carbon\Carbon::parse($agenda->waktu_mulai)->format('H:i')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                </div>
                                <div>
                                    <label class="block font-medium text-sm text-gray-700">Waktu Selesai</label>
                                    <input type="time" name="waktu_selesai" value="{{ old('waktu_selesai', $agenda->waktu_selesai ? \Carbon\Carbon::parse($agenda->waktu_selesai)->format('H:i') : '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block font-medium text-sm text-gray-700">Deskripsi / Rundown Kegiatan</label>
                                <textarea name="deskripsi_rundown" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('deskripsi_rundown', $agenda->deskripsi_rundown) }}</textarea>
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('agenda.index') }}" class="text-gray-600 underline mr-4 hover:text-gray-900">Batal</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                Update Agenda
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>