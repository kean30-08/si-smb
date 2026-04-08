<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Refleksi Siswa') }}
            </h2>
            <a href="{{ route('refleksi.index', $refleksi->tanggal) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow transition">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg sm:rounded-xl overflow-hidden">

                {{-- Header Kartu Detail --}}
                <div class="bg-indigo-50 border-b border-indigo-100 p-6 sm:px-10">
                    <div class="flex justify-between items-start flex-wrap gap-4">
                        <div>
                            <h1 class="text-2xl font-extrabold text-indigo-900">{{ $refleksi->nama_siswa }}</h1>
                            <p class="text-sm font-medium text-indigo-700 mt-1">NIS: {{ $refleksi->nis }}</p>
                        </div>
                        <div
                            class="text-left sm:text-right bg-white py-2 px-4 rounded-lg shadow-sm border border-indigo-100">
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">Tanggal Kegiatan
                            </p>
                            <p class="text-sm font-bold text-gray-800">
                                {{ \Carbon\Carbon::parse($refleksi->tanggal)->translatedFormat('l, d F Y') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-indigo-200/60 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 font-medium">Nama Orang Tua:</span>
                            <span class="font-bold text-gray-800 ml-1">{{ $refleksi->nama_orang_tua }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-medium">Email:</span>
                            @if (empty($refleksi->email_orang_tua))
                                <span class="italic text-gray-400 ml-1">Tidak dilampirkan</span>
                            @else
                                <a href="mailto:{{ $refleksi->email_orang_tua }}"
                                    class="font-bold text-blue-600 hover:underline ml-1">{{ $refleksi->email_orang_tua }}</a>
                            @endif
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-gray-500 font-medium">Waktu Pengisian:</span>
                            <span class="text-gray-800 ml-1">{{ $refleksi->created_at->translatedFormat('d F Y, H:i') }}
                                WIB ({{ $refleksi->created_at->diffForHumans() }})</span>
                        </div>
                    </div>
                </div>

                {{-- Body Konten Ulasan --}}
                {{-- Body Konten Ulasan --}}
                <div class="p-6 sm:p-10 space-y-8">

                    {{-- Rangkuman --}}
                    <div>
                        <h3 class="text-lg font-bold text-indigo-800 flex items-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="mr-2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                                <path d="M16 13H8" />
                                <path d="M16 17H8" />
                                <path d="M10 9H8" />
                            </svg>
                            Rangkuman Kegiatan
                        </h3>
                        <div
                            class="bg-gray-50 rounded-lg p-5 border border-gray-200 text-gray-700 leading-relaxed text-justify">
                            {!! nl2br(e($refleksi->rangkuman)) !!}
                        </div>
                    </div>

                    {{-- Bagian yang Disukai --}}
                    <div>
                        <h3 class="text-lg font-bold text-green-700 flex items-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="mr-2">
                                <path
                                    d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3" />
                            </svg>
                            Bagian yang Disukai
                        </h3>
                        <div
                            class="bg-green-50 rounded-lg p-5 border border-green-200 text-gray-800 leading-relaxed text-justify">
                            {!! nl2br(e($refleksi->bagian_disukai)) !!}
                        </div>
                    </div>

                    {{-- Bagian yang Kurang Disukai --}}
                    <div>
                        <h3 class="text-lg font-bold text-red-700 flex items-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="mr-2">
                                <path
                                    d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17" />
                            </svg>
                            Bagian yang Kurang Disukai
                        </h3>
                        <div
                            class="bg-red-50 rounded-lg p-5 border border-red-200 text-gray-800 leading-relaxed text-justify">
                            {!! nl2br(e($refleksi->bagian_kurang_disukai)) !!}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
