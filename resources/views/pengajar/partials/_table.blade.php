<div class="w-full md:shadow-md md:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500">

        {{-- HEADER TABEL (Sembunyi di HP) --}}
        <thead class="hidden md:table-header-group text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="py-3 px-6">No</th>
                <th class="py-3 px-6">Nama Lengkap</th>
                <th class="py-3 px-6">No HP</th>
                {{-- <th class="py-3 px-6">NIP</th> --}}
                <th class="py-3 px-6">Jabatan</th>
                <th class="py-3 px-6">Email Login</th>
                @if ($isAdmin)
                    <th class="py-3 px-6 text-center">Aksi</th>
                @endif
            </tr>
        </thead>

        {{-- BODY TABEL (Berubah jadi susunan kartu di HP) --}}
        <tbody class="block md:table-row-group">
            @forelse ($pengajars as $index => $pengajar)
                {{-- BARIS TABEL --}}
                <tr onclick="window.location='{{ route('pengajar.show', $pengajar->id) }}'"
                    class="block md:table-row bg-white border border-gray-200 md:border-0 md:border-b hover:bg-gray-100 cursor-pointer transition mb-4 md:mb-0 rounded-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0">

                    {{-- Kolom No (Disembunyikan di HP untuk menghemat ruang) --}}
                    <td class="hidden md:table-cell py-4 px-6">
                        {{ $pengajars->firstItem() + $index }}</td>

                    {{-- Kolom Nama (Di HP, NIP ditumpuk di bawah Nama) --}}
                    <td
                        class="block md:table-cell py-2 md:py-4 px-2 md:px-6 border-b md:border-none border-dashed border-gray-200 mb-3 md:mb-0 pb-3 md:pb-4">
                        <div class="font-bold text-gray-900 text-base md:text-sm whitespace-nowrap">
                            {{ $pengajar->nama_lengkap }}</div>
                        <div class="text-xs text-gray-500 md:hidden mt-1 font-medium">No HP:
                            {{ $pengajar->nomor_hp ?? '-' }}</div>
                    </td>

                    {{-- Kolom NIP (Hanya muncul terpisah di Desktop)
                <td class="hidden md:table-cell py-4 px-6">{{ $pengajar->nip ?? '-' }}</td> --}}
                    {{-- Kolom NIP (Hanya muncul terpisah di Desktop) --}}
                    <td class="hidden md:table-cell py-4 px-6">{{ $pengajar->nomor_hp ?? '-' }}</td>

                    {{-- Kolom Jabatan --}}
                    <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6 mb-2 md:mb-0">
                        <div class="flex items-center justify-between md:justify-start">
                            <span
                                class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Jabatan</span>
                            <span class="text-sm text-gray-800">{{ $pengajar->jabatan }}</span>
                        </div>
                    </td>

                    {{-- Kolom Email --}}
                    <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6 mb-2 md:mb-0">
                        <div class="flex items-center justify-between md:justify-start">
                            <span
                                class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Email</span>
                            <span class="text-sm text-gray-600">{{ $pengajar->user->email ?? '-' }}</span>
                        </div>
                    </td>

                    {{-- Kolom Aksi --}}
                    @if ($isAdmin)
                        <td class="block md:table-cell py-3 md:py-4 px-2 md:px-6 text-right md:text-center mt-2 md:mt-0 border-t md:border-none border-gray-50 pt-3 md:pt-4"
                            onclick="event.stopPropagation();">
                            <div class="flex justify-end md:justify-center space-x-5">
                                {{-- Edit --}}
                                <a href="{{ route('pengajar.edit', $pengajar->id) }}"
                                    class="text-blue-500 hover:text-blue-700 transition p-1" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                        <path
                                            d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z" />
                                    </svg>
                                </a>
                                {{-- Hapus --}}
                                <form action="{{ route('pengajar.destroy', $pengajar->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus pengajar ini beserta akun loginnya?');"
                                    class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition p-1"
                                        title="Hapus">
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
                    <td colspan="6" class="block md:table-cell py-4 px-6 text-center text-gray-500">Data
                        pengajar tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $pengajars->links() }}
</div>
