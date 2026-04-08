<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Refleksi Siswa: ') }} {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y') }}
            </h2>
            <a href="{{ route('agenda.showDate', $tanggal) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                &larr; Kembali ke Rundown
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @if ($refleksis->isEmpty())
                    <p class="text-gray-500 text-center py-10">Belum ada siswa yang mengisi refleksi untuk tanggal ini.
                    </p>
                @else
                    <div class="space-y-6">
                        @foreach ($refleksis as $ref)
                            {{-- TAMBAHKAN ONCLICK DAN EFEK HOVER DI SINI --}}
                            <div onclick="window.location='{{ route('refleksi.show', $ref->id) }}'"
                                class="bg-gray-50 border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-100 hover:shadow-md transition duration-200">

                                <div class="flex justify-between border-b pb-2 mb-3">
                                    <div>
                                        <h3 class="font-bold text-lg text-indigo-700">
                                            {{ $ref->nama_siswa }}
                                            <span class="text-sm font-normal text-gray-500">(NIS:
                                                {{ $ref->nis }})</span>
                                        </h3>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Ortu: {{ $ref->nama_orang_tua }}
                                            | Email:
                                            @if (empty($ref->email_orang_tua))
                                                <span class="italic text-gray-400">Email tidak dilampirkan</span>
                                            @else
                                                <span class="text-blue-500">{{ $ref->email_orang_tua }}</span>
                                            @endif
                                            | Dikirim: {{ $ref->created_at->diffForHumans() }}
                                        </p>
                                    </div>

                                    {{-- Tambahkan Ikon Panah Kecil untuk indikasi bisa diklik --}}
                                    <div class="flex items-center text-gray-400">
                                        <span class="text-xs mr-1 hidden sm:block">Lihat Detail</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m9 18 6-6-6-6" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Potong teks agar tidak terlalu panjang di halaman index (Preview saja) --}}
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 opacity-80">
                                    <div class="bg-white p-3 rounded shadow-sm border-l-4 border-indigo-400">
                                        <p class="text-xs font-bold text-gray-500 mb-1">Rangkuman:</p>
                                        <p class="text-sm text-gray-800 truncate">{{ $ref->rangkuman }}</p>
                                    </div>
                                    <div class="bg-white p-3 rounded shadow-sm border-l-4 border-green-400">
                                        <p class="text-xs font-bold text-gray-500 mb-1">Hal Disukai:</p>
                                        <p class="text-sm text-gray-800 truncate">{{ $ref->bagian_disukai }}</p>
                                    </div>
                                    <div class="bg-white p-3 rounded shadow-sm border-l-4 border-red-400">
                                        <p class="text-xs font-bold text-gray-500 mb-1">Kurang Disukai:</p>
                                        <p class="text-sm text-gray-800 truncate">{{ $ref->bagian_kurang_disukai }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
