<div class="w-full md:shadow-md md:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500">
        {{-- HEADER TABEL (Sembunyi di HP) --}}
        <thead class="hidden md:table-header-group text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="py-3 px-6">Tanggal Kegiatan</th>
                <th class="py-3 px-6">Total Rangkaian Acara</th>
                <th class="py-3 px-6 text-center">Aksi</th>
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
                    <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6">
                        <div class="flex items-center justify-between md:justify-start">
                            <span class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Total
                                Acara</span>
                            <span
                                class="bg-blue-100 text-blue-800 font-semibold px-2.5 py-0.5 rounded border border-blue-400">
                                {{ $group->total_kegiatan }} Kegiatan
                            </span>
                        </div>
                    </td>

                    {{-- AKSI --}}
                    <td
                        class="block md:table-cell py-3 md:py-4 px-2 md:px-6 text-right md:text-center text-blue-600 font-medium mt-2 md:mt-0 border-t md:border-none border-gray-50 pt-3 md:pt-4">
                        <span class="inline-flex items-center text-sm md:text-base">
                            Lihat Detail Rundown &rarr;
                        </span>
                    </td>
                </tr>
            @empty
                <tr class="block md:table-row bg-white border border-gray-200 rounded-lg p-4">
                    <td colspan="3" class="block md:table-cell py-4 px-6 text-center text-gray-500">
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
