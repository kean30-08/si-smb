<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Konfirmasi Pendaftaran Siswa Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">



                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th class="py-3 px-4">Nama Pendaftar</th>
                                    <th class="py-3 px-4">Ortu / Kontak</th>
                                    <th class="py-3 px-4">Kelas Tujuan</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendaftarans as $p)
                                    {{-- BARIS BISA DIKLIK --}}
                                    <tr onclick="window.location='{{ route('kelola_pendaftaran.show', $p->id) }}'"
                                        class="border-b hover:bg-gray-100 cursor-pointer transition">

                                        <td class="py-3 px-4">
                                            <p class="font-bold text-gray-800">{{ $p->nama_lengkap }}
                                                ({{ $p->nama_panggilan }})
                                            </p>
                                            <p class="text-xs">{{ \Carbon\Carbon::parse($p->tanggal_lahir)->age }} Tahun
                                            </p>
                                        </td>

                                        <td class="py-3 px-4">
                                            <p class="font-bold">{{ $p->nama_orang_tua }}</p>
                                            <p class="text-xs text-blue-600">{{ $p->nomor_hp_orang_tua }}</p>
                                        </td>

                                        <td class="py-3 px-4 font-bold text-indigo-600">
                                            {{ $p->kelas->nama_kelas ?? '-' }}
                                        </td>

                                        <td class="py-3 px-4 text-center">
                                            <div class="flex justify-center space-x-2">

                                                {{-- Tombol Terima --}}
                                                <form action="{{ route('kelola_pendaftaran.terima', $p->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        onclick="event.stopPropagation(); return confirm('Terima siswa ini? Data akan otomatis masuk ke tabel master Siswa.')"
                                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs font-bold transition shadow-sm">
                                                        Terima
                                                    </button>
                                                </form>

                                                {{-- Tombol Tolak --}}
                                                <form action="{{ route('kelola_pendaftaran.tolak', $p->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        onclick="event.stopPropagation(); return confirm('Yakin menolak pendaftaran ini?')"
                                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-bold transition shadow-sm">
                                                        Tolak
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-6 text-gray-500 font-medium">Tidak ada
                                            pendaftar baru yang menunggu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
