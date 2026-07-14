<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Histori Laporan Insentif Pengajar') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Arsip dokumen laporan insentif yang siap disetor ke dinas.</p>
            </div>

            <div class="w-full sm:w-auto flex">
                <a href="{{ route('laporan_insentif.create') }}"
                    class="w-full sm:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                    + Tambah Laporan Insentif
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-0 overflow-x-auto">

                    @php
                        $namaBulanArray = [
                            '01' => 'Januari',
                            '02' => 'Februari',
                            '03' => 'Maret',
                            '04' => 'April',
                            '05' => 'Mei',
                            '06' => 'Juni',
                            '07' => 'Juli',
                            '08' => 'Agustus',
                            '09' => 'September',
                            '10' => 'Oktober',
                            '11' => 'November',
                            '12' => 'Desember',
                        ];
                    @endphp

                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                            <tr>
                                <th class="py-4 px-6 w-16">No</th>
                                <th class="py-4 px-6">Nama Pengajar</th>
                                <th class="py-4 px-6 text-center">Periode Laporan</th>
                                <th class="py-4 px-6 text-center">Tanggal Dibuat</th>
                                <th class="py-4 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporans as $index => $lap)
                                <tr class="bg-white border-b hover:bg-gray-50 transition">
                                    <td class="py-4 px-6 font-medium text-gray-900">
                                        {{ $laporans->firstItem() + $index }}</td>
                                    <td class="py-4 px-6 font-bold text-gray-800">
                                        {{ $lap->pengajar->nama_lengkap ?? 'Pengajar Dihapus' }}
                                    </td>
                                    <td class="py-4 px-6 text-center font-semibold text-indigo-600">
                                        {{ $namaBulanArray[$lap->bulan] ?? $lap->bulan }} {{ $lap->tahun }}
                                    </td>
                                    <td class="py-4 px-6 text-center text-gray-500">
                                        {{ $lap->created_at->translatedFormat('d M Y, H:i') }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="flex justify-center items-center gap-2">
                                            <a href="{{ route('laporan_insentif.download', $lap->id) }}"
                                                class="p-2 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 rounded-lg transition"
                                                title="Download PDF">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                    <polyline points="7 10 12 15 17 10" />
                                                    <line x1="12" y1="15" x2="12" y2="3" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('laporan_insentif.destroy', $lap->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Hapus histori dokumen ini secara permanen?');"
                                                class="m-0">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 bg-red-100 text-red-600 hover:bg-red-200 rounded-lg transition"
                                                    title="Hapus">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18"
                                                        height="18" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M3 6h18" />
                                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Belum ada riwayat cetak laporan insentif yang tersimpan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $laporans->links() }}</div>
        </div>
    </div>
</x-app-layout>
