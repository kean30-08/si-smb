<div class="w-full md:shadow-md md:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="hidden md:table-header-group text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="py-3 px-6">No</th>
                <th class="py-3 px-6">Nama Lengkap</th>
                <th class="py-3 px-6">No HP</th>
                <th class="py-3 px-6">Jabatan</th>
                <th class="py-3 px-6">Email Login</th>
                <th class="py-3 px-6 text-center">Status</th>
                @if ($isAdmin)
                    <th class="py-3 px-6 text-center">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody class="block md:table-row-group">
            @forelse ($pengajars as $index => $pengajar)
                <tr onclick="window.location='{{ route('pengajar.show', $pengajar->id) }}'"
                    class="block md:table-row bg-white border border-gray-200 md:border-0 md:border-b hover:bg-gray-100 cursor-pointer transition mb-4 md:mb-0 rounded-lg md:rounded-none p-4 md:p-0">

                    <td class="hidden md:table-cell py-4 px-6">{{ $pengajars->firstItem() + $index }}</td>

                    <td
                        class="block md:table-cell py-2 md:py-4 px-2 md:px-6 border-b md:border-none border-dashed border-gray-200 mb-3 md:mb-0 pb-3 md:pb-4 font-bold text-gray-900 text-base md:text-sm">
                        {{ $pengajar->nama_lengkap }}
                    </td>

                    <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6 mb-2 md:mb-0">
                        <div class="flex items-center justify-between md:justify-start">
                            <span class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">No
                                HP</span>
                            {{ $pengajar->nomor_hp ?? '-' }}
                        </div>
                    </td>

                    <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6 mb-2 md:mb-0">
                        <div class="flex items-center justify-between md:justify-start">
                            <span
                                class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Jabatan</span>
                            {{ $pengajar->jabatan->nama_jabatan ?? '-' }}
                        </div>
                    </td>

                    <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6 mb-2 md:mb-0">
                        <div
                            class="flex items-center justify-between md:justify-start text-xs md:text-sm text-gray-400 md:text-gray-500">
                            <span
                                class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Email</span>
                            {{ $pengajar->user->email ?? 'Tidak ada akun' }}
                        </div>
                    </td>

                    {{-- KOLOM STATUS --}}
                    <td
                        class="block md:table-cell py-2 md:py-4 px-2 md:px-6 md:text-center mb-2 md:mb-0 border-b md:border-none border-dashed border-gray-200 pb-3 md:pb-4">
                        <div class="flex items-center justify-between md:justify-center">
                            <span
                                class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Status</span>
                            @if ($pengajar->status == 'aktif')
                                <span
                                    class="px-3 py-1 font-semibold text-green-700 bg-green-100 rounded-full text-xs">Aktif</span>
                            @else
                                <span class="px-3 py-1 font-semibold text-red-700 bg-red-100 rounded-full text-xs">Tidak
                                    Aktif</span>
                            @endif
                        </div>
                    </td>

                    @if ($isAdmin)
                        <td class="block md:table-cell py-3 md:py-4 px-2 md:px-6 text-right md:text-center text-blue-600 font-medium mt-2 md:mt-0 pt-3 md:pt-4"
                            onclick="event.stopPropagation();">

                            <div class="flex items-center justify-end md:justify-center gap-4">
                                {{-- PERBAIKAN: Tombol Edit disamakan dengan tabel Agenda --}}
                                <a href="{{ route('pengajar.edit', $pengajar->id) }}"
                                    class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                        <path
                                            d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z" />
                                    </svg>
                                </a>

                                {{-- LOGIKA PROTEKSI DELETE --}}
                                @if (
                                    !(
                                        $pengajar->user_id == 1 ||
                                        $pengajar->jabatan_id == 2 ||
                                        (auth()->user()->pengajar && $pengajar->id == auth()->user()->pengajar->id)
                                    ))
                                    <form action="{{ route('pengajar.destroy', $pengajar->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pengajar ini beserta akun loginnya?');"
                                        class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="event.stopPropagation();"
                                            class="text-red-500 hover:text-red-700 transition" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 6h18" />
                                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                                <line x1="10" x2="10" y1="11" y2="17" />
                                                <line x1="14" x2="14" y1="11" y2="17" />
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    {{-- Gembok sebagai indikator tidak bisa dihapus --}}
                                    <div class="text-gray-300" title="Akun ini tidak dapat dihapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="11" x="3" y="11" rx="2"
                                                ry="2" />
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr class="block md:table-row bg-white border border-gray-200 rounded-lg p-4">
                    <td colspan="{{ $isAdmin ? '7' : '6' }}"
                        class="block md:table-cell py-4 px-6 text-center text-gray-500">
                        Tidak ada data pengajar yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination Container --}}
<div class="mt-4 pagination-container">
    {{ $pengajars->links() }}
</div>
