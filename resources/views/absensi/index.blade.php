<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Data Kehadiran') }}
            </h2>
            @if($type == 'siswa')
            <a href="{{ route('absensi.scanner') }}" class="bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded shadow transition flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h.01"/><path d="M17 7h.01"/><path d="M7 17h.01"/><path d="M17 17h.01"/><path d="M12 7v10"/><path d="M7 12h10"/></svg>
                Buka Kamera Scanner (Siswa)
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                
                {{-- NAVIGASI TAB MENU --}}
                <div class="border-b border-gray-200 bg-gray-50 pt-2 px-4">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                        <li class="mr-2">
                            <a href="{{ route('absensi.index', ['type' => 'siswa', 'tanggal' => $tanggal]) }}" class="inline-block p-4 border-b-2 rounded-t-lg transition {{ $type == 'siswa' ? 'border-indigo-600 text-indigo-600 font-bold bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Absensi Siswa (Scan & Manual)
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="{{ route('absensi.index', ['type' => 'pengajar', 'tanggal' => $tanggal]) }}" class="inline-block p-4 border-b-2 rounded-t-lg transition {{ $type == 'pengajar' ? 'border-amber-500 text-amber-600 font-bold bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Absensi Pengajar / Pengurus
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="p-6 text-gray-900">

                    {{-- Form Filter Tanggal (Berbagi untuk kedua tab) --}}
                    <form action="{{ route('absensi.index') }}" method="GET" class="mb-6 flex flex-col md:flex-row gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $tanggal }}" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                        </div>
                        
                        @if($type == 'siswa')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filter Kelas</label>
                            <select name="kelas_id" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                                <option value="">Semua Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ $kelas_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </form>

                    {{-- Info Jika Tidak Ada Jadwal --}}
                    @if($agendas->isEmpty())
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-sm">
                            <p class="font-bold">Perhatian</p>
                            <p>Tidak ada jadwal kegiatan yang terdaftar pada tanggal <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</strong>.</p>
                        </div>
                    @else
                        
                        {{-- ============================== --}}
                        {{-- TAB 1: KONTEN ABSENSI SISWA    --}}
                        {{-- ============================== --}}
                        @if($type == 'siswa')
                            <div class="overflow-x-auto relative shadow-md sm:rounded-lg border-t-2 border-indigo-500">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-gray-700 uppercase bg-indigo-50">
                                        <tr>
                                            <th class="py-3 px-6">No</th>
                                            <th class="py-3 px-6">Nama Siswa</th>
                                            <th class="py-3 px-6">Kelas</th>
                                            <th class="py-3 px-6 text-center">Status Kehadiran</th>
                                            <th class="py-3 px-6 text-center">Waktu Masuk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($siswas as $index => $siswa)
                                            @php $absenSiswa = $absensis->where('siswa_id', $siswa->id)->first(); @endphp
                                            <tr class="bg-white border-b hover:bg-gray-50 transition">
                                                <td class="py-4 px-6">{{ $siswas->firstItem() + $index }}</td>
                                                <td class="py-4 px-6 font-bold text-gray-900">{{ $siswa->nama_lengkap }} <br><span class="text-xs text-gray-400 font-normal">ID: SMB-{{ $siswa->id }}</span></td>
                                                <td class="py-4 px-6">{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                                                <td class="py-4 px-6 text-center">
                                                    <form action="{{ route('absensi.manual') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                                                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                                        @php $statusSaatIni = $absenSiswa ? $absenSiswa->status_kehadiran : 'alpa'; @endphp
                                                        <select name="status" onchange="this.form.submit()" class="text-xs font-bold rounded-full border-gray-300 shadow-sm cursor-pointer focus:ring-0
                                                            @if($statusSaatIni == 'alpa') bg-red-100 text-red-800 border-red-200
                                                            @elseif($statusSaatIni == 'hadir') bg-green-100 text-green-800 border-green-200
                                                            @elseif($statusSaatIni == 'izin') bg-blue-100 text-blue-800 border-blue-200
                                                            @elseif($statusSaatIni == 'sakit') bg-yellow-100 text-yellow-800 border-yellow-200 @endif
                                                        ">
                                                            <option value="alpa" class="bg-white text-black" {{ $statusSaatIni == 'alpa' ? 'selected' : '' }}>Alpa</option>
                                                            <option value="hadir" class="bg-white text-black" {{ $statusSaatIni == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                            <option value="izin" class="bg-white text-black" {{ $statusSaatIni == 'izin' ? 'selected' : '' }}>Izin</option>
                                                            <option value="sakit" class="bg-white text-black" {{ $statusSaatIni == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td class="py-4 px-6 text-center text-xs text-gray-600">
                                                    @if($absenSiswa && $absenSiswa->waktu_hadir) {{ \Carbon\Carbon::parse($absenSiswa->waktu_hadir)->format('H:i:s') }} <br><span class="text-[10px] text-gray-400">({{ ucfirst($absenSiswa->metode_absen) }})</span>
                                                    @else - @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">{{ $siswas->links() }}</div>
                        @endif

                        {{-- ============================== --}}
                        {{-- TAB 2: KONTEN ABSENSI PENGAJAR --}}
                        {{-- ============================== --}}
                        @if($type == 'pengajar')
                            <div class="overflow-x-auto relative shadow-md sm:rounded-lg border-t-2 border-amber-500">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-gray-700 uppercase bg-amber-50">
                                        <tr>
                                            <th class="py-3 px-6">No</th>
                                            <th class="py-3 px-6">Nama Pengajar / Pengurus</th>
                                            <th class="py-3 px-6">Jabatan</th>
                                            <th class="py-3 px-6 text-center">Status Kehadiran</th>
                                            <th class="py-3 px-6 text-center">Waktu Masuk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pengajars as $index => $pengajar)
                                            @php $absenPengajar = $absensiPengajars->where('pengajar_id', $pengajar->id)->first(); @endphp
                                            <tr class="bg-white border-b hover:bg-gray-50 transition">
                                                <td class="py-4 px-6">{{ $pengajars->firstItem() + $index }}</td>
                                                <td class="py-4 px-6 font-bold text-gray-900">{{ $pengajar->nama_lengkap }}</td>
                                                <td class="py-4 px-6">{{ $pengajar->jabatan ?? '-' }}</td>
                                                
                                                <td class="py-4 px-6 text-center">
                                                    <form action="{{ route('absensi.manualPengajar') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="pengajar_id" value="{{ $pengajar->id }}">
                                                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                                        @php $statusSaatIni = $absenPengajar ? $absenPengajar->status_kehadiran : 'alpa'; @endphp
                                                        <select name="status" onchange="this.form.submit()" class="text-xs font-bold rounded-full border-gray-300 shadow-sm cursor-pointer focus:ring-0
                                                            @if($statusSaatIni == 'alpa') bg-red-100 text-red-800 border-red-200
                                                            @elseif($statusSaatIni == 'hadir') bg-amber-100 text-amber-800 border-amber-200
                                                            @elseif($statusSaatIni == 'izin') bg-blue-100 text-blue-800 border-blue-200
                                                            @elseif($statusSaatIni == 'sakit') bg-yellow-100 text-yellow-800 border-yellow-200 @endif
                                                        ">
                                                            <option value="alpa" class="bg-white text-black" {{ $statusSaatIni == 'alpa' ? 'selected' : '' }}>Alpa</option>
                                                            <option value="hadir" class="bg-white text-black" {{ $statusSaatIni == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                            <option value="izin" class="bg-white text-black" {{ $statusSaatIni == 'izin' ? 'selected' : '' }}>Izin</option>
                                                            <option value="sakit" class="bg-white text-black" {{ $statusSaatIni == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                        </select>
                                                    </form>
                                                </td>

                                                <td class="py-4 px-6 text-center text-xs text-gray-600">
                                                    @if($absenPengajar && $absenPengajar->waktu_hadir) {{ \Carbon\Carbon::parse($absenPengajar->waktu_hadir)->format('H:i:s') }}
                                                    @else - @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">{{ $pengajars->links() }}</div>
                        @endif

                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>