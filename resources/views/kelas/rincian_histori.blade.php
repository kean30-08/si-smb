<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Rincian Histori Kelas') }}
                </h2>
                <p class="text-sm text-indigo-600 font-bold mt-1">
                    {{ $kelas->nama_kelas }} &mdash; TA: {{ $selectedTa->tahun_ajaran }}
                </p>
            </div>
            <a href="{{ route('kelas.histori', ['tahun_ajaran_id' => $selectedTa->id]) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm transition shadow-sm">
                &larr; Kembali ke Histori Kelas
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">

                    <div class="mb-4 bg-blue-50 border-l-4 border-blue-400 p-4">
                        <p class="text-sm text-blue-700">
                            <strong>Catatan:</strong> Ini adalah Data <em>Historis</em> daftar siswa pada Tahun Ajaran
                            {{ $selectedTa->tahun_ajaran }}
                            yang berada dikelas {{ $kelas->nama_kelas }}, segala <strong>Status Keaktifan</strong> siswa
                            yang tampil disini dapat berbeda di Tahun Ajaran Lainnya.
                        </p>
                    </div>

                    <div class="w-full md:shadow-md md:rounded-lg overflow-hidden border border-gray-200">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="hidden md:table-header-group text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th scope="col" class="py-3 px-6 text-center">No</th>
                                    <th scope="col" class="py-3 px-6">Nama Lengkap</th>
                                    <th scope="col" class="py-3 px-6 text-center">NIK</th>
                                    <th scope="col" class="py-3 px-6 text-center">Status (Pada TA Ini)</th>
                                    <th scope="col" class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="block md:table-row-group">
                                @forelse ($historis as $index => $item)
                                    <tr class="block md:table-row bg-white border-b border-gray-200 md:border-b hover:bg-gray-50 transition p-4 md:p-0">
                                        
                                        {{-- Sembunyikan Nomor Urut murni di mobile, kita gabung di nama --}}
                                        <td class="hidden md:table-cell py-4 px-6 text-center font-medium">{{ $index + 1 }}</td>
                                        
                                        <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6 font-bold text-gray-900 border-b md:border-none border-dashed border-gray-200 mb-2 md:mb-0 pb-3 md:pb-4">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="md:hidden text-gray-400 text-xs font-normal">#{{ $index + 1 }}</span>
                                                {{ $item->siswa->nama_lengkap }}

                                                {{-- Lencana Biru Murid Baru --}}
                                                @if ($item->is_murid_baru)
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] uppercase rounded-full border border-blue-200 flex-shrink-0" title="Murid Baru / Tambahan di Tahun Ajaran Ini">
                                                        Baru
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        <td class="block md:table-cell py-1.5 md:py-4 px-2 md:px-6 md:text-center text-gray-700">
                                            <span class="md:hidden text-xs font-bold text-gray-400 uppercase w-16 inline-block">NIK:</span>
                                            {{ $item->siswa->nis }}
                                        </td>
                                        
                                        <td class="block md:table-cell py-1.5 md:py-4 px-2 md:px-6 md:text-center">
                                            <span class="md:hidden text-xs font-bold text-gray-400 uppercase w-16 inline-block">Status:</span>
                                            <span class="px-3 py-1 font-semibold rounded-full text-xs {{ $item->status_class }}">
                                                {{ $item->dynamic_status }}
                                            </span>
                                        </td>
                                        
                                        <td class="block md:table-cell py-3 md:py-4 px-2 md:px-6 md:text-center mt-3 md:mt-0 border-t md:border-none border-gray-100 pt-3 md:pt-4">
                                            <a href="{{ route('siswa.show', $item->siswa->id) }}"
                                                class="block md:inline-block text-center bg-indigo-50 hover:bg-indigo-100 md:bg-transparent text-indigo-600 hover:text-indigo-900 font-semibold text-sm transition rounded py-2 md:py-0 w-full md:w-auto">
                                                Lihat Profil
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="block md:table-row">
                                        <td colspan="5" class="block md:table-cell py-8 px-6 text-center text-gray-500">
                                            Tidak ada data siswa yang terdaftar di kelas ini pada Tahun Ajaran {{ $selectedTa->tahun_ajaran }}.
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
