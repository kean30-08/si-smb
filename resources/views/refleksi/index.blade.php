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
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
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
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-white p-3 rounded shadow-sm border-l-4 border-indigo-400">
                                        <p class="text-xs font-bold text-gray-500 mb-1">Rangkuman:</p>
                                        <p class="text-sm text-gray-800">{{ $ref->rangkuman }}</p>
                                    </div>
                                    <div class="bg-white p-3 rounded shadow-sm border-l-4 border-green-400">
                                        <p class="text-xs font-bold text-gray-500 mb-1">Hal Disukai:</p>
                                        <p class="text-sm text-gray-800">{{ $ref->bagian_disukai }}</p>
                                    </div>
                                    <div class="bg-white p-3 rounded shadow-sm border-l-4 border-red-400">
                                        <p class="text-xs font-bold text-gray-500 mb-1">Kurang Disukai:</p>
                                        <p class="text-sm text-gray-800">{{ $ref->bagian_kurang_disukai }}</p>
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
