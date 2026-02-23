<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Data Kehadiran') }}
            </h2>
            <a href="{{ route('absensi.scanner') }}" class="bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded shadow transition flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h.01"/><path d="M17 7h.01"/><path d="M7 17h.01"/><path d="M17 17h.01"/><path d="M12 7v10"/><path d="M7 12h10"/></svg>
                Buka Kamera Scanner
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Form Filter Tanggal & Kelas --}}
                    <form action="{{ route('absensi.index') }}" method="GET" class="mb-6 flex flex-col md:flex-row gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $tanggal }}" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filter Kelas</label>
                            <select name="kelas_id" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ $kelas_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    {{-- Info Jika Tidak Ada Jadwal --}}
                    @if($agendas->isEmpty())
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-sm">
                            <p class="font-bold">Perhatian</p>
                            <p>Tidak ada jadwal kegiatan yang terdaftar pada tanggal <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</strong>.</p>
                        </div>
                    @else
                        {{-- Tabel Kesimpulan Kehadiran Harian --}}
                        <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-200">
                                    <tr>
                                        <th class="py-3 px-6">No</th>
                                        <th class="py-3 px-6">Nama Siswa</th>
                                        <th class="py-3 px-6">Kelas</th>
                                        <th class="py-3 px-6 text-center">Status Harian</th>
                                        <th class="py-3 px-6 text-center">Waktu Scan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siswas as $index => $siswa)
                                        @php
                                            // Cari apakah siswa ini punya absensi di tanggal yang dipilih
                                            // (Meskipun di DB ada 4 baris, kita ambil 1 saja untuk perwakilan hari ini)
                                            $absenSiswa = $absensis->where('siswa_id', $siswa->id)->first();
                                        @endphp
                                        <tr class="bg-white border-b hover:bg-gray-50 transition">
                                            <td class="py-4 px-6">{{ $siswas->firstItem() + $index }}</td>
                                            <td class="py-4 px-6 font-bold text-gray-900">{{ $siswa->nama_lengkap }} <br><span class="text-xs text-gray-400 font-normal">ID: SMB-{{ $siswa->id }}</span></td>
                                            <td class="py-4 px-6">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                                            
                                            {{-- Kolom Dropdown Status Harian --}}
                                            <td class="py-4 px-6 text-center">
                                                <form action="{{ route('absensi.manual') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                                                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                                    
                                                    @php
                                                        $statusSaatIni = $absenSiswa ? $absenSiswa->status_kehadiran : 'alpa';
                                                    @endphp

                                                    <select name="status" onchange="this.form.submit()" class="text-xs font-bold rounded-full border-gray-300 shadow-sm cursor-pointer focus:ring-0
                                                        @if($statusSaatIni == 'alpa') bg-red-100 text-red-800 border-red-200
                                                        @elseif($statusSaatIni == 'hadir') bg-green-100 text-green-800 border-green-200
                                                        @elseif($statusSaatIni == 'izin') bg-blue-100 text-blue-800 border-blue-200
                                                        @elseif($statusSaatIni == 'sakit') bg-yellow-100 text-yellow-800 border-yellow-200
                                                        @endif
                                                    ">
                                                        <option value="alpa" class="bg-white text-black" {{ $statusSaatIni == 'alpa' ? 'selected' : '' }}>Alpa</option>
                                                        <option value="hadir" class="bg-white text-black" {{ $statusSaatIni == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                        <option value="izin" class="bg-white text-black" {{ $statusSaatIni == 'izin' ? 'selected' : '' }}>Izin</option>
                                                        <option value="sakit" class="bg-white text-black" {{ $statusSaatIni == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                    </select>
                                                </form>
                                            </td>

                                            <td class="py-4 px-6 text-center text-xs text-gray-600">
                                                @if($absenSiswa && $absenSiswa->waktu_hadir)
                                                    {{ \Carbon\Carbon::parse($absenSiswa->waktu_hadir)->format('H:i:s') }}
                                                    <br><span class="text-[10px] text-gray-400">({{ ucfirst($absenSiswa->metode_absen) }})</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                         {{ $siswas->links() }}
                     </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
        });

        @if(session('success'))
            Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
        @endif
        @if(session('error'))
            Toast.fire({ icon: 'error', title: "{{ session('error') }}" });
        @endif
    </script>
</x-app-layout>