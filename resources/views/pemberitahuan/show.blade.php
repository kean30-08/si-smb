<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Pemberitahuan') }}
            </h2>
            <a href="{{ route('pemberitahuan.index') }}"
                class="w-full sm:w-auto text-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow transition">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">

                {{-- Banner Gambar --}}
                @if ($pemberitahuan->gambar)
                    <div class="w-full bg-gray-100 flex justify-center items-center">
                        <img src="{{ asset($pemberitahuan->gambar) }}" alt="{{ $pemberitahuan->judul }}"
                            class="w-full h-auto max-h-[500px] object-contain"> {{-- h-auto dan max-h agar gambar proporsional --}}
                    </div>
                @endif

                <div class="p-6 sm:p-10">
                    <div class="mb-6 pb-6 border-b border-gray-100">
                        <p class="text-sm text-indigo-600 font-bold mb-2 uppercase tracking-widest">
                            Dipublikasikan: {{ $pemberitahuan->created_at->translatedFormat('l, d F Y - H:i') }}
                        </p>
                        <h1 class="text-3xl font-extrabold text-gray-900 leading-tight">
                            {{ $pemberitahuan->judul }}
                        </h1>
                    </div>

                    <div class="prose max-w-none text-gray-800 leading-relaxed">
                        {!! nl2br(e($pemberitahuan->deskripsi)) !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
