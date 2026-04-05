<div class="w-full md:shadow-md md:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500">
        {{-- HEADER TABEL (Sembunyi di HP) --}}
        <thead class="hidden md:table-header-group text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="py-3 px-6">Tanggal Kegiatan</th>
                <th class="py-3 px-6">Total Rangkaian Acara</th>
                <th class="py-3 px-6">Penanggung Jawab</th> {{-- TAMBAHAN KOLOM --}}
                @if ($isAdmin)
                    <th class="py-3 px-6 text-center">Aksi</th>
                @endif
            </tr>
        </thead>

        {{-- BODY TABEL --}}
        <tbody class="block md:table-row-group">
            @forelse ($agendasGrouped as $group)
                {{-- BARIS TABEL: Berubah jadi Kartu di HP --}}
                <tr onclick="window.location='{{ route('agenda.showDate', $group->tanggal) }}'"
                    class="block md:table-row bg-white border border-gray-200 md:border-0 md:border-b hover:bg-gray-100 cursor-pointer transition mb-4 md:mb-0 rounded-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0">

                    {{-- TANGGAL --}}
                    <td
                        class="block md:table-cell py-2 md:py-4 px-2 md:px-6 border-b md:border-none border-dashed border-gray-200 mb-3 md:mb-0 pb-3 md:pb-4 font-bold text-gray-900 text-base md:text-sm">
                        {{ \Carbon\Carbon::parse($group->tanggal)->translatedFormat('l, d F Y') }}
                    </td>

                    {{-- TOTAL KEGIATAN --}}
                    <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6 mb-2 md:mb-0">
                        <div class="flex items-center justify-between md:justify-start">
                            <span class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Total
                                Acara</span>
                            <span
                                class="bg-blue-100 text-blue-800 font-semibold px-2.5 py-0.5 rounded border border-blue-400">
                                {{ $group->total_kegiatan }} Kegiatan
                            </span>
                        </div>
                    </td>

                    {{-- PENANGGUNG JAWAB (PIC) --}}
                    <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6">
                        <div class="flex items-center justify-between md:justify-start">
                            <span class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">PIC
                                Absensi</span>
                            @if ($group->penanggungJawab)
                                <div class="flex items-center text-sm font-medium text-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-indigo-500">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                    {{ $group->penanggungJawab->nama_lengkap }}
                                </div>
                            @else
                                <span class="text-sm italic text-gray-400">Belum Ditentukan</span>
                            @endif
                        </div>
                    </td>

                    {{-- AKSI --}}
                    @if ($isAdmin)
                        <td class="block md:table-cell py-3 md:py-4 px-2 md:px-6 text-right md:text-center text-blue-600 font-medium mt-2 md:mt-0 border-t md:border-none border-gray-50 pt-3 md:pt-4"
                            onclick="event.stopPropagation();">

                            <div class="flex items-center justify-end md:justify-center gap-2">
                                <form action="{{ route('agenda.destroyDate', $group->tanggal) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus SEMUA agenda pada tanggal ini?');"
                                    class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="event.stopPropagation();"
                                        class="text-red-600 hover:text-red-800 transition ml-2"
                                        title="Hapus semua agenda pada tanggal ini">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18" />
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                            <line x1="10" x2="10" y1="11" y2="17" />
                                            <line x1="14" x2="14" y1="11" y2="17" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr class="block md:table-row bg-white border border-gray-200 rounded-lg p-4">
                    {{-- colspan diubah jadi 4 karena ada tambahan kolom Penanggung Jawab --}}
                    <td colspan="4" class="block md:table-cell py-4 px-6 text-center text-gray-500">
                        Belum ada jadwal kegiatan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination Container --}}
<div class="mt-4 pagination-container">
    {{ $agendasGrouped->links() }}
</div>
