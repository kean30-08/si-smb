<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Materi Pembelajaran') }}
            </h2>
            <a href="{{ route('materi.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow transition">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 sm:p-8 text-gray-900">

                    {{-- Header Judul & Badge Kelas --}}
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $materi->judul }}</h1>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 self-start md:self-auto">
                                Kelas: {{ $materi->kelas->nama_kelas ?? 'Umum / Semua Kelas' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            Diunggah pada:
                            {{ \Carbon\Carbon::parse($materi->created_at)->translatedFormat('d F Y, H:i') }}
                        </p>
                    </div>

                    {{-- Bagian Deskripsi --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Deskripsi Materi</h3>
                        <div
                            class="bg-gray-50 rounded-lg p-5 border border-gray-100 text-gray-700 leading-relaxed text-justify">
                            @if ($materi->deskripsi)
                                {{-- nl2br digunakan agar enter/baris baru di database terbaca di HTML --}}
                                {!! nl2br(e($materi->deskripsi)) !!}
                            @else
                                <span class="italic text-gray-400">Tidak ada deskripsi untuk materi ini.</span>
                            @endif
                        </div>
                    </div>

                    {{-- Bagian Lampiran File --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">File Lampiran</h3>
                        @if ($materi->file_materi)
                            <div class="flex items-center p-4 border border-gray-200 rounded-lg bg-white shadow-sm">
                                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">Dokumen Materi Pembelajaran
                                    </p>
                                    <p class="text-xs text-gray-500">Klik tombol di samping untuk mengunduh</p>
                                </div>
                                <div>
                                    <a href="{{ asset('storage/' . $materi->file_materi) }}" target="_blank"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow">
                                        Download
                                    </a>
                                </div>
                            </div>
                        @else
                            <div
                                class="p-4 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-lg flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <span class="text-sm font-medium">Pengajar tidak menyertakan file lampiran pada materi
                                    ini.</span>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
