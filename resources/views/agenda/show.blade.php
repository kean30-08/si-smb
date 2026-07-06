<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Atur Penanggung Jawab Absensi: ') }} <br class="xl:hidden">
                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
            </h2>

            <div class="w-full xl:w-auto flex flex-col sm:flex-row flex-wrap gap-2">
                {{-- Tombol Kembali --}}
                <a href="{{ route('agenda.index') }}"
                    class="w-full sm:w-auto justify-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex items-center shadow transition text-sm">
                    &larr; Kembali ke Jadwal
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 bg-gray-50 md:bg-white">

                    {{-- TAMPILAN PIC --}}
                    @auth
                        <div class="mb-2 bg-indigo-50 border border-indigo-200 rounded-lg p-4 shadow-sm">
                            <div
                                class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4 border-b border-indigo-200 pb-3">
                                <div class="flex items-center">
                                    <div class="p-2 bg-indigo-100 rounded-full text-indigo-600 mr-3 hidden sm:block">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-indigo-900">Daftar Penanggung Jawab Absensi</h3>
                                        <p class="text-xs text-indigo-700 mt-0.5">Pengajar yang bertugas memindai kehadiran
                                            hari ini.</p>
                                    </div>
                                </div>

                                @if ($isAdmin)
                                    <button type="button" onclick="tambahPic()"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1.5 rounded-md text-xs font-bold border border-blue-300 shadow-sm transition flex items-center shrink-0">
                                        + Tambah Penanggung Jawab
                                    </button>
                                @endif
                            </div>

                            @if ($isAdmin)
                                <form action="{{ route('agenda.updatePic', $tanggal) }}" method="POST" class="m-0">
                                    @csrf @method('PUT')

                                    <div id="pic-container" class="space-y-3 mb-4">
                                        @if (empty($penanggungJawabIds))
                                            <div class="pic-row flex items-center gap-2">
                                                <select name="penanggung_jawab_id[]"
                                                    class="flex-1 md:w-72 text-sm font-medium border-indigo-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-gray-700 py-2">
                                                    <option value="" disabled selected>-- Pilih Pengajar --</option>
                                                    @foreach ($pengajars as $pengajar)
                                                        <option value="{{ $pengajar->id }}">{{ $pengajar->nama_lengkap }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="button" onclick="hapusPic(this)"
                                                    class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition"
                                                    title="Hapus">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M18 6 6 18" />
                                                        <path d="m6 6 12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            @foreach ($penanggungJawabIds as $idPic)
                                                <div class="pic-row flex items-center gap-2">
                                                    <select name="penanggung_jawab_id[]"
                                                        class="flex-1 md:w-72 text-sm font-medium border-indigo-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-gray-700 py-2">
                                                        <option value="" disabled>-- Pilih Pengajar --</option>
                                                        @foreach ($pengajars as $pengajar)
                                                            <option value="{{ $pengajar->id }}"
                                                                {{ $idPic == $pengajar->id ? 'selected' : '' }}>
                                                                {{ $pengajar->nama_lengkap }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button type="button" onclick="hapusPic(this)"
                                                        class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition"
                                                        title="Hapus">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18"
                                                            height="18" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path d="M18 6 6 18" />
                                                            <path d="m6 6 12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="flex justify-end mt-6">
                                        <button type="submit"
                                            class="bg-indigo-600 hover:bg-indigo-800 text-white px-6 py-2 rounded-md text-sm font-bold shadow transition duration-150">
                                            Simpan Penanggung Jawab
                                        </button>
                                    </div>
                                </form>
                            @else
                                {{-- Tampilan Read-Only Pengajar --}}
                                <div class="flex flex-wrap gap-2">
                                    @if (empty($penanggungJawabIds))
                                        <span class="text-sm font-bold text-gray-400 italic">Belum ada PIC yang
                                            ditentukan</span>
                                    @else
                                        @foreach ($penanggungJawabIds as $idPic)
                                            @php $namaPic = $pengajars->firstWhere('id', $idPic); @endphp
                                            @if ($namaPic)
                                                <span
                                                    class="bg-indigo-100 text-indigo-800 border border-indigo-200 px-3 py-1.5 rounded-full text-xs font-bold flex items-center shadow-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="mr-1.5">
                                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                                        <circle cx="12" cy="7" r="4" />
                                                    </svg>
                                                    {{ $namaPic->nama_lengkap }}
                                                </span>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endauth

                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT DINAMIS --}}
    @if ($isAdmin)
        <script>
            function tambahPic() {
                const container = document.getElementById('pic-container');
                const html = `
                    <div class="pic-row flex items-center gap-2">
                        <select name="penanggung_jawab_id[]" class="flex-1 md:w-72 text-sm font-medium border-indigo-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-gray-700 py-2">
                            <option value="" disabled selected>-- Pilih Pengajar --</option>
                            @foreach ($pengajars as $pengajar)
                                <option value="{{ $pengajar->id }}">{{ addslashes($pengajar->nama_lengkap) }}</option>
                            @endforeach
                        </select>
                        <button type="button" onclick="hapusPic(this)" class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
            }

            function hapusPic(btn) {
                const rows = document.getElementsByClassName('pic-row');
                if (rows.length > 1) {
                    btn.closest('.pic-row').remove();
                } else {
                    btn.closest('.pic-row').querySelector('select').value = "";
                }
            }
        </script>
    @endif
</x-app-layout>
