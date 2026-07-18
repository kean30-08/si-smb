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



                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="hidden md:table-header-group text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="py-3 px-4">Nama Pendaftar</th>
                                    <th class="py-3 px-4">Ortu / Kontak</th>
                                    <th class="py-3 px-4">Kelas Tujuan</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="block md:table-row-group">
                                @forelse($pendaftarans as $p)
                                    {{-- BARIS BISA DIKLIK --}}
                                    <tr onclick="window.location='{{ route('kelola_pendaftaran.show', $p->id) }}'"
                                        class="block md:table-row bg-white md:bg-transparent border border-gray-200 md:border-0 border-b-gray-200 hover:bg-gray-50 cursor-pointer transition mb-4 md:mb-0 p-3 md:p-0 rounded-lg md:rounded-none shadow-sm md:shadow-none">

                                        <td class="block md:table-cell py-2 md:py-3 px-3 md:px-4 border-b border-dashed border-gray-100 md:border-none">
                                            <div class="md:hidden text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nama Pendaftar</div>
                                            <p class="font-bold text-gray-800 text-base md:text-sm">{{ $p->nama_lengkap }}
                                                <span class="font-normal text-gray-600">({{ $p->nama_panggilan }})</span>
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">Umur: {{ \Carbon\Carbon::parse($p->tanggal_lahir)->age }} Tahun</p>
                                        </td>

                                        <td class="block md:table-cell py-2 md:py-3 px-3 md:px-4 border-b border-dashed border-gray-100 md:border-none">
                                            <div class="md:hidden text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Orang Tua / Kontak</div>
                                            <p class="font-bold text-gray-700">{{ $p->nama_orang_tua }}</p>
                                            <p class="text-xs text-blue-600 font-medium mt-0.5">{{ $p->nomor_hp_orang_tua }}</p>
                                        </td>

                                        <td class="block md:table-cell py-2 md:py-3 px-3 md:px-4 border-b border-dashed border-gray-100 md:border-none">
                                            <div class="md:hidden text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Kelas Tujuan</div>
                                            <span class="inline-block font-bold text-indigo-700 bg-indigo-50 px-2 py-1 rounded md:bg-transparent md:px-0 md:py-0 text-sm md:text-base">
                                                {{ $p->kelas->nama_kelas ?? '-' }}
                                            </span>
                                        </td>

                                        <td class="block md:table-cell py-3 md:py-3 px-3 md:px-4 text-center mt-1 md:mt-0">
                                            <div class="md:hidden text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 text-left">Aksi (Terima / Tolak)</div>
                                            <div class="flex flex-row justify-start md:justify-center items-center gap-2">

                                                {{-- Tombol Terima --}}
                                                <form action="{{ route('kelola_pendaftaran.terima', $p->id) }}" method="POST" class="flex-1 md:flex-none">
                                                    @csrf
                                                    <button type="submit"
                                                        onclick="event.stopPropagation(); return confirm('Terima siswa ini? Data akan otomatis masuk ke tabel master Siswa.')"
                                                        class="w-full md:w-auto bg-green-500 hover:bg-green-600 text-white px-3 py-2 md:py-1.5 rounded-lg md:rounded text-xs font-bold transition shadow-sm flex justify-center items-center gap-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:hidden" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                        Terima
                                                    </button>
                                                </form>

                                                {{-- Tombol Tolak --}}
                                                <form action="{{ route('kelola_pendaftaran.tolak', $p->id) }}" method="POST" class="flex-1 md:flex-none">
                                                    @csrf
                                                    <button type="submit"
                                                        onclick="event.stopPropagation(); return confirm('Yakin menolak pendaftaran ini?')"
                                                        class="w-full md:w-auto bg-red-500 hover:bg-red-600 text-white px-3 py-2 md:py-1.5 rounded-lg md:rounded text-xs font-bold transition shadow-sm flex justify-center items-center gap-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:hidden" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                        Tolak
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="block md:table-row">
                                        <td colspan="4" class="block md:table-cell text-center py-8 px-4 text-gray-500 font-medium bg-gray-50 md:bg-transparent rounded-lg">
                                            Tidak ada pendaftar baru yang menunggu.
                                        </td>
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
