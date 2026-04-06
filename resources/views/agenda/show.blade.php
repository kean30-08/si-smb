<x-app-layout>
    <x-slot name="header">
        {{-- PERBAIKAN HEADER: Flex-col agar teks dan tombol tidak tabrakan di HP --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Rundown Kegiatan: ') }} <br class="lg:hidden">
                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
            </h2>



            {{-- PERBAIKAN TOMBOL: Menggunakan gap-2 flex-col untuk HP, flex-row untuk Layar Besar --}}
            <div class="w-full lg:w-auto flex flex-col sm:flex-row flex-wrap gap-2">

                @php
                    $isAdmin = auth()->user()->isAdmin();
                @endphp

                @if ($isAdmin)
                    {{-- Tombol Broadcast PDF --}}
                    <form action="{{ route('agenda.broadcast', $tanggal) }}" method="POST"
                        onsubmit="return confirm('Kirim jadwal rundown tanggal ini ke SEMUA email orang tua yang terdaftar?');"
                        class="w-full sm:w-auto m-0">
                        @csrf
                        <button type="submit"
                            class="w-full sm:w-auto justify-center bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded flex items-center shadow transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="mr-2">
                                <path
                                    d="M21.2 8.4c.5.3.8.8.8 1.4v10.2a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9.8c0-.6.3-1.1.8-1.4l8-4.8c.7-.4 1.7-.4 2.4 0l8 4.8Z" />
                                <path d="m22 10-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 10" />
                            </svg>
                            Bagikan via Email
                        </button>
                    </form>
                @endif

                {{-- Tombol Delete Agenda Satu Tanggal (hapus semua agenda pada tanggal tersebut) --}}
                <form action="{{ route('agenda.destroyDate', $tanggal) }}" method="POST"
                    onsubmit="return confirm('Yakin ingin menghapus SEMUA agenda pada tanggal ini?');"
                    class="w-full sm:w-auto m-0">
                    @csrf
                    @method('DELETE')
                    @if ($isAdmin)
                        <button type="submit"
                            class="w-full sm:w-auto justify-center bg-red-600 hover:bg-red-800 text-white font-bold py-2 px-4 rounded flex items-center shadow transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="mr-2">
                                <path d="M3 6h18" />
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                <line x1="10" x2="10" y1="11" y2="17" />
                                <line x1="14" x2="14" y1="11" y2="17" />
                            </svg>
                            Hapus Semua Tanggal Ini
                        </button>
                    @endif
                </form>

                {{-- Tombol Download PDF --}}
                <a href="{{ route('agenda.download', $tanggal) }}"
                    class="w-full sm:w-auto justify-center bg-teal-600 hover:bg-teal-800 text-white font-bold py-2 px-4 rounded flex items-center shadow transition"
                    title="Download PDF untuk dibagikan via WhatsApp/Manual">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="mr-2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="7 10 12 15 17 10" />
                        <line x1="12" x2="12" y1="15" y2="3" />
                    </svg>
                    Download PDF
                </a>
                @if ($isAdmin)
                    {{-- Tombol Tambah Acara Baru --}}
                    <a href="{{ route('agenda.createDetail', $tanggal) }}"
                        class="w-full sm:w-auto justify-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center shadow transition">
                        + Tambah Acara
                    </a>
                @endif

                {{-- Tombol Kembali --}}
                <a href="{{ route('agenda.index') }}"
                    class="w-full sm:w-auto justify-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex items-center shadow transition">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 bg-gray-50 md:bg-white">
                    {{-- Form Ganti PIC (Hanya Admin) --}}
                    <div
                        class="mb-6 bg-indigo-50 border border-indigo-200 rounded-lg p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">

                        {{-- Info Teks Kiri --}}
                        <div class="flex items-center">
                            <div class="p-2 bg-indigo-100 rounded-full text-indigo-600 mr-3 hidden sm:block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <polyline points="16 11 18 13 22 9" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-indigo-900">Penanggung Jawab Absensi</h3>
                                <p class="text-xs text-indigo-700 mt-0.5">Pilih pengajar yang bertugas memindai
                                    kehadiran siswa pada hari ini.</p>
                            </div>
                        </div>

                        {{-- Form Ganti PIC (Hanya Admin) --}}
                        @if ($isAdmin)
                            <form action="{{ route('agenda.updatePic', $tanggal) }}" method="POST"
                                class="m-0 w-full md:w-auto flex items-center gap-2">
                                @csrf
                                @method('PUT')

                                <select name="penanggung_jawab_id"
                                    class="w-full md:w-72 text-sm font-medium border-indigo-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-gray-700 py-2">
                                    @if ($pengajars->isEmpty())
                                        <option value="" disabled selected class="text-gray-400 italic">Belum ada
                                            pengajar terdaftar</option>
                                    @else
                                        @foreach ($pengajars as $pengajar)
                                            <option value="{{ $pengajar->id }}"
                                                {{ $penanggungJawabId == $pengajar->id || (is_null($penanggungJawabId) && $loop->first) ? 'selected' : '' }}>
                                                {{ $pengajar->nama_lengkap }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>

                                <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-800 text-white px-4 py-2 rounded-md text-sm font-bold shadow transition duration-150 flex-shrink-0">
                                    Simpan
                                </button>
                            </form>
                        @else
                            {{-- Tampilan Jika Bukan Admin (Hanya Baca) --}}
                            <div class="bg-white px-4 py-2 border border-gray-200 rounded-md shadow-sm">
                                <span class="text-sm font-bold text-gray-800">
                                    @php
                                        $namaPic = $pengajars->firstWhere('id', $penanggungJawabId);
                                        echo $namaPic
                                            ? $namaPic->nama_lengkap
                                            : '<span class="text-gray-400 italic">Belum Ditentukan</span>';
                                    @endphp
                                </span>
                            </div>
                        @endif

                    </div>


                    {{-- PERBAIKAN TABEL: Menghapus overflow-x-auto, menambah wrapper responsif --}}
                    <div class="w-full md:shadow-md md:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500">

                            {{-- HEADER TABEL (Sembunyi di HP) --}}
                            <thead class="hidden md:table-header-group text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="py-3 px-6">Waktu</th>
                                    <th class="py-3 px-6">Nama Kegiatan</th>
                                    <th class="py-3 px-6">Status</th>
                                    @if ($isAdmin)
                                        <th class="py-3 px-6 text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>

                            {{-- BODY TABEL (Berubah jadi susunan kartu) --}}
                            {{-- BODY TABEL (Berubah jadi susunan kartu) --}}
                            <tbody class="block md:table-row-group">
                                @foreach ($agendas as $agenda)
                                    {{-- BARIS TABEL: Bentuk Kartu di Mobile --}}
                                    <tr onclick="window.location='{{ route('absensi.index', ['tanggal' => $agenda->tanggal, 'agenda_id' => $agenda->id, 'type' => 'siswa']) }}'"
                                        class="block md:table-row bg-white border border-gray-200 md:border-0 md:border-b hover:bg-gray-100 cursor-pointer transition mb-4 md:mb-0 rounded-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0">

                                        {{-- WAKTU --}}
                                        <td
                                            class="block md:table-cell py-2 md:py-4 px-2 md:px-6 border-b md:border-none border-dashed border-gray-200 mb-3 md:mb-0 pb-3 md:pb-4 font-bold text-gray-800">
                                            <span
                                                class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">Pukul</span>
                                            {{ \Carbon\Carbon::parse($agenda->waktu_mulai)->format('H:i') }} -
                                            {{ $agenda->waktu_selesai ? \Carbon\Carbon::parse($agenda->waktu_selesai)->format('H:i') : 'Selesai' }}
                                        </td>

                                        {{-- NAMA KEGIATAN --}}
                                        <td
                                            class="block md:table-cell py-2 md:py-4 px-2 md:px-6 border-b md:border-none border-dashed border-gray-200 mb-3 md:mb-0 pb-3 md:pb-4">
                                            <p class="text-base font-bold text-gray-900">{{ $agenda->nama_kegiatan }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $agenda->deskripsi_rundown ?? '-' }}</p>
                                        </td>

                                        {{-- STATUS --}}
                                        <td class="block md:table-cell py-2 md:py-4 px-2 md:px-6 mb-2 md:mb-0">
                                            <div class="flex items-center justify-between md:justify-start">
                                                <span
                                                    class="md:hidden text-xs font-bold text-gray-500 uppercase tracking-wider">Status
                                                    Acara</span>

                                                @if ($agenda->status == 'akan datang')
                                                    <span
                                                        class="px-2 py-1 font-semibold text-blue-700 bg-blue-100 rounded-full text-[10px] md:text-xs">Akan
                                                        Datang</span>
                                                @elseif ($agenda->status == 'sedang berlangsung')
                                                    <span
                                                        class="px-2 py-1 font-semibold text-yellow-700 bg-yellow-100 rounded-full text-[10px] md:text-xs">Berlangsung</span>
                                                @elseif ($agenda->status == 'selesai')
                                                    <span
                                                        class="px-2 py-1 font-semibold text-green-700 bg-green-100 rounded-full text-[10px] md:text-xs">Selesai</span>
                                                @elseif ($agenda->status == 'batal')
                                                    <span
                                                        class="px-2 py-1 font-semibold text-red-700 bg-red-100 rounded-full text-[10px] md:text-xs">Batal</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- AKSI --}}
                                        @if ($isAdmin)
                                            {{-- TAMBAHAN: onclick event.stopPropagation() agar saat menekan tombol Edit/Hapus, barisnya tidak ikut terklik --}}
                                            <td onclick="event.stopPropagation();"
                                                class="block md:table-cell py-3 md:py-4 px-2 md:px-6 text-right md:text-center mt-2 md:mt-0 border-t md:border-none border-gray-50 pt-3 md:pt-4">
                                                <div class="flex justify-end md:justify-center space-x-5">
                                                    <a href="{{ route('agenda.edit', $agenda->id) }}"
                                                        class="text-blue-500 hover:text-blue-700 transition"
                                                        title="Edit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                            height="20" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path
                                                                d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                            <path
                                                                d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z" />
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('agenda.destroy', $agenda->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus agenda ini?');"
                                                        class="m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-500 hover:text-red-700 transition"
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
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
