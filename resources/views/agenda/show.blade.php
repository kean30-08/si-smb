<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Rundown Kegiatan: ') }} {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
            </h2>
            <div class="space-x-2 flex">
                
                {{-- Tombol Broadcast PDF --}}
                <form action="{{ route('agenda.broadcast', $tanggal) }}" method="POST" onsubmit="return confirm('Kirim jadwal rundown tanggal ini ke SEMUA email orang tua yang terdaftar?');">
                    @csrf
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded flex items-center shadow transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M21.2 8.4c.5.3.8.8.8 1.4v10.2a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9.8c0-.6.3-1.1.8-1.4l8-4.8c.7-.4 1.7-.4 2.4 0l8 4.8Z"/><path d="m22 10-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 10"/></svg>
                        Broadcast PDF Acara Ini
                    </button>
                </form>

                {{-- Tombol Tambah Acara Baru --}}
                <a href="{{ route('agenda.createDetail', $tanggal) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center shadow transition">
                    + Tambah Acara
                </a>

                {{-- Tombol Kembali --}}
                <a href="{{ route('agenda.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow transition">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="py-3 px-6">Waktu</th>
                                    <th class="py-3 px-6">Nama Kegiatan</th>
                                    <th class="py-3 px-6">Status</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($agendas as $agenda)
                                <tr class="bg-white border-b hover:bg-gray-50 transition">
                                    <td class="py-4 px-6 font-bold text-gray-800">
                                        {{ \Carbon\Carbon::parse($agenda->waktu_mulai)->format('H:i') }} - 
                                        {{ $agenda->waktu_selesai ? \Carbon\Carbon::parse($agenda->waktu_selesai)->format('H:i') : 'Selesai' }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="text-base font-bold text-gray-900">{{ $agenda->nama_kegiatan }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $agenda->deskripsi_rundown ?? '-' }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if ($agenda->status == 'akan datang')
                                            <span class="px-2 py-1 font-semibold text-blue-700 bg-blue-100 rounded-full text-xs">Akan Datang</span>
                                        @elseif ($agenda->status == 'sedang berlangsung')
                                            <span class="px-2 py-1 font-semibold text-yellow-700 bg-yellow-100 rounded-full text-xs">Sedang Berlangsung</span>
                                        @elseif ($agenda->status == 'selesai')
                                            <span class="px-2 py-1 font-semibold text-green-700 bg-green-100 rounded-full text-xs">Selesai</span>
                                        @elseif ($agenda->status == 'batal')
                                            <span class="px-2 py-1 font-semibold text-red-700 bg-red-100 rounded-full text-xs">Batal</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 flex justify-center space-x-4">
                                        <a href="{{ route('agenda.edit', $agenda->id) }}" class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/></svg>
                                        </a>
                                        <form action="{{ route('agenda.destroy', $agenda->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus agenda ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>