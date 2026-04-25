<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Tahun Ajaran') }}
        </h2>
    </x-slot>

    {{-- KITA BUNGKUS SELURUH KONTEN DENGAN ALPINE.JS STATE --}}
    <div class="py-12" x-data="{ showModal: false, activeFormId: '', confirmInput: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- KOTAK BACKGROUND PUTIH DITAMBAHKAN DI SINI --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-4 md:p-6 text-gray-900">

                    <div class="mb-6 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-800">Daftar Tahun Ajaran</h3>
                        <a href="{{ route('tahun_ajaran.create') }}"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm transition shadow-sm">
                            + Tambah Data
                        </a>
                    </div>

                    <div class="w-full">
                        <table class="w-full text-sm text-left text-gray-500">
                            {{-- HEADER TABEL (HANYA MUNCUL DI DESKTOP) --}}
                            <thead
                                class="hidden md:table-header-group text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th scope="col" class="py-3 px-6">No</th>
                                    <th scope="col" class="py-3 px-6">Tahun Ajaran / Semester</th>
                                    <th scope="col" class="py-3 px-6 text-center">Status</th>
                                    <th scope="col" class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>

                            {{-- BODY TABEL --}}
                            <tbody class="block md:table-row-group">
                                @forelse($tahun_ajarans as $index => $ta)
                                    <tr
                                        class="block md:table-row bg-white border border-gray-200 md:border-0 md:border-b hover:bg-gray-50 transition mb-4 md:mb-0 rounded-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0">

                                        <td class="hidden md:table-cell py-4 px-6">{{ $index + 1 }}</td>

                                        <td
                                            class="block md:table-cell py-2 md:py-4 px-2 md:px-6 border-b md:border-none border-dashed border-gray-200 mb-3 md:mb-0 pb-3 md:pb-4">
                                            <div class="font-bold text-gray-900 text-base md:text-sm whitespace-nowrap">
                                                {{ $ta->tahun_ajaran }}
                                            </div>
                                        </td>

                                        <td
                                            class="block md:table-cell py-2 md:py-4 px-2 md:px-6 md:text-center mb-2 md:mb-0">
                                            <div class="flex items-center justify-between md:justify-center">
                                                <span
                                                    class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Status</span>
                                                <div>
                                                    @if ($ta->status == 'aktif')
                                                        <span
                                                            class="px-2 py-1 font-semibold text-green-700 bg-green-100 rounded-full text-[10px] md:text-xs">Aktif
                                                            Sekarang</span>
                                                    @else
                                                        <span
                                                            class="px-2 py-1 font-semibold text-gray-600 bg-gray-100 rounded-full text-[10px] md:text-xs">Tidak
                                                            Aktif</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td
                                            class="block md:table-cell py-3 md:py-4 px-2 md:px-6 text-right md:text-center mt-2 md:mt-0 border-t md:border-none border-gray-50 pt-3 md:pt-4">
                                            <div class="flex justify-end md:justify-center space-x-4 items-center">

                                                @if ($ta->status != 'aktif')
                                                    {{-- FORM SET AKTIF --}}
                                                    <form id="form-aktif-{{ $ta->id }}"
                                                        action="{{ route('tahun_ajaran.aktifkan', $ta->id) }}"
                                                        method="POST" class="m-0">
                                                        @csrf @method('PATCH')
                                                        <button type="button"
                                                            @click="showModal = true; activeFormId = 'form-aktif-{{ $ta->id }}'; confirmInput = '';"
                                                            class="text-green-500 hover:text-green-700 transition font-bold text-xs md:text-sm mr-2 border border-green-500 hover:bg-green-50 px-2 py-1 rounded">
                                                            Set Aktif
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- TOMBOL EDIT (ICON PENCIL) --}}
                                                <a href="{{ route('tahun_ajaran.edit', $ta->id) }}"
                                                    class="text-blue-500 hover:text-blue-700 transition p-1"
                                                    title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                        height="20" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path
                                                            d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                        <path
                                                            d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z" />
                                                    </svg>
                                                </a>

                                                @if ($ta->status != 'aktif')
                                                    {{-- TOMBOL HAPUS (ICON TRASH) --}}
                                                    <form action="{{ route('tahun_ajaran.destroy', $ta->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus Tahun Ajaran ini?');"
                                                        class="m-0">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-500 hover:text-red-700 transition p-1"
                                                            title="Hapus">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                                height="20" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M3 6h18" />
                                                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                                                <line x1="10" x2="10" y1="11"
                                                                    y2="17" />
                                                                <line x1="14" x2="14" y1="11"
                                                                    y2="17" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="block md:table-row bg-white border border-gray-200 rounded-lg p-4">
                                        <td colspan="4"
                                            class="block md:table-cell py-4 px-6 text-center text-gray-500">
                                            Belum ada data.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL KONFIRMASI KETIK "Aktif" --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showModal = false"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Konfirmasi
                                    Pengaktifan</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Peringatan: Tahun ajaran yang sedang berjalan saat ini akan otomatis dimatikan.
                                        Untuk melanjutkan, silakan ketik tulisan <span
                                            class="font-bold text-red-600">Aktif</span> pada kotak di bawah ini.
                                    </p>
                                    <input type="text" x-model="confirmInput"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Ketik 'Aktif' di sini...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button"
                            @click="if(confirmInput === 'Aktif') { document.getElementById(activeFormId).submit(); }"
                            :disabled="confirmInput !== 'Aktif'"
                            :class="confirmInput === 'Aktif' ? 'bg-green-600 hover:bg-green-700 cursor-pointer' :
                                'bg-gray-300 text-gray-500 cursor-not-allowed'"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Aktifkan Sekarang
                        </button>
                        <button type="button" @click="showModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
