<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Informasi & Pemberitahuan') }}
            </h2>
            @if ($isAdmin)
                <a href="{{ route('pemberitahuan.create') }}"
                    class="w-full sm:w-auto justify-center bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded shadow transition flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Buat Pemberitahuan Baru
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($pemberitahuans->isEmpty())
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 text-center text-gray-500 flex flex-col items-center">
                    <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15" />
                    </svg>
                    <p class="text-lg font-medium">Belum ada informasi atau pemberitahuan saat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($pemberitahuans as $pemberitahuan)
                        @if (!$isAdmin && $pemberitahuan->status == 'arsip')
                            @continue
                        @endif

                        <div
                            class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 hover:shadow-lg transition flex flex-col h-full">
                            {{-- Container Gambar --}}
                            @if ($pemberitahuan->gambar)
                                <div class="w-full h-48 bg-gray-100 flex-shrink-0 overflow-hidden relative">
                                    <img src="{{ asset('storage/' . $pemberitahuan->gambar) }}"
                                        alt="{{ $pemberitahuan->judul }}" class="w-full h-full object-cover">
                                    @if ($isAdmin)
                                        <div class="absolute top-2 right-2">
                                            <span
                                                class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full shadow-sm {{ $pemberitahuan->status == 'aktif' ? 'bg-green-500 text-white' : 'bg-gray-600 text-white' }}">
                                                {{ $pemberitahuan->status }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div
                                    class="w-full h-48 bg-gradient-to-br from-indigo-50 to-indigo-100 flex items-center justify-center flex-shrink-0 relative">
                                    <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                                        </path>
                                    </svg>
                                    @if ($isAdmin)
                                        <div class="absolute top-2 right-2">
                                            <span
                                                class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full shadow-sm {{ $pemberitahuan->status == 'aktif' ? 'bg-green-500 text-white' : 'bg-gray-600 text-white' }}">
                                                {{ $pemberitahuan->status }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Konten Text --}}
                            <div class="p-5 flex flex-col flex-grow">
                                <p class="text-xs text-indigo-600 font-bold mb-2 uppercase tracking-wider">
                                    {{ $pemberitahuan->created_at->translatedFormat('d M Y') }}
                                </p>
                                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 leading-tight">
                                    <a href="{{ route('pemberitahuan.show', $pemberitahuan->id) }}"
                                        class="hover:text-indigo-600 transition">{{ $pemberitahuan->judul }}</a>
                                </h3>
                                <p class="text-sm text-gray-600 mb-4 line-clamp-3 flex-grow">
                                    {{ Str::limit(strip_tags($pemberitahuan->deskripsi), 120) }}
                                </p>

                                <div class="mt-auto pt-4 border-t border-gray-100 flex justify-between items-center">
                                    <a href="{{ route('pemberitahuan.show', $pemberitahuan->id) }}"
                                        class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">Baca
                                        detail &rarr;</a>

                                    @if ($isAdmin)
                                        <div class="flex gap-2">
                                            <a href="{{ route('pemberitahuan.edit', $pemberitahuan->id) }}"
                                                class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded transition"
                                                title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('pemberitahuan.destroy', $pemberitahuan->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete(this)"
                                                    class="p-1.5 text-red-600 hover:bg-red-50 rounded transition"
                                                    title="Hapus">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $pemberitahuans->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- SweetAlert Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#4f46e5',
                timer: 3000
            });
        @endif

        function confirmDelete(button) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        }
    </script>
</x-app-layout>
