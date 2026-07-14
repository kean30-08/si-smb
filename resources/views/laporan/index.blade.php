<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pusat Cetak Laporan & Rekapitulasi') }}
            </h2>
            @php $tahunAktif = \App\Models\TahunAjaran::where('status', 'aktif')->first(); @endphp
            <p class="text-sm text-indigo-600 font-bold mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                TA Aktif Saat Ini: {{ $tahunAktif ? $tahunAktif->tahun_ajaran : 'Belum Ada TA Aktif' }}
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">

                {{-- ============================================== --}}
                {{-- KARTU 1: LAPORAN ABSENSI SISWA --}}
                {{-- ============================================== --}}
                <div class="bg-white shadow-sm sm:rounded-lg border-t-4 border-indigo-500 flex flex-col">
                    <div class="p-6 text-gray-900 flex flex-col flex-1">
                        <h3 class="text-lg font-bold mb-2 text-indigo-700">Rekap Keaktifan Siswa</h3>
                        <p class="text-xs text-gray-500 mb-4">Cetak Laporan kehadiran siswa dalam format tabel dinamis.
                        </p>

                        <form action="{{ route('laporan.cetakKehadiranSiswa') }}" method="POST" target="_blank"
                            class="flex flex-col flex-1" id="form_siswa">
                            @csrf

                            {{-- 1. RADIO MODE WAKTU --}}
                            <div class="mb-4 bg-indigo-50 p-3 rounded-lg border border-indigo-100">
                                <label class="block text-xs font-bold text-indigo-800 mb-2">Pilih Mode Rentang
                                    Waktu</label>
                                <div class="flex items-center space-x-4">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="mode_siswa" value="bulan"
                                            class="text-indigo-600 form-radio focus:ring-indigo-500" checked
                                            onchange="toggleMode('siswa')">
                                        <span class="ml-2 text-xs font-semibold text-gray-700">Bulan & Tahun</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="mode_siswa" value="custom"
                                            class="text-indigo-600 form-radio focus:ring-indigo-500"
                                            onchange="toggleMode('siswa')">
                                        <span class="ml-2 text-xs font-semibold text-gray-700">Custom Waktu</span>
                                    </label>
                                </div>
                            </div>

                            {{-- 2. INPUT: SPESIFIK BULAN & TAHUN --}}
                            <div id="wrap_bulan_siswa" class="mb-4 grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Bulan</label>
                                    <select id="input_bulan_siswa"
                                        class="w-full text-sm border-gray-300 rounded focus:border-indigo-500">
                                        <option value="01">Januari</option>
                                        <option value="02">Februari</option>
                                        <option value="03">Maret</option>
                                        <option value="04">April</option>
                                        <option value="05">Mei</option>
                                        <option value="06">Juni</option>
                                        <option value="07">Juli</option>
                                        <option value="08">Agustus</option>
                                        <option value="09">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                        <option value="semua" class="font-bold text-indigo-600">Semua Bulan (1 Tahun)
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Ketik Tahun</label>
                                    <input type="number" id="input_tahun_siswa" value="{{ date('Y') }}"
                                        class="w-full text-sm border-gray-300 rounded focus:border-indigo-500"
                                        placeholder="Misal: 2026" required>
                                </div>
                            </div>

                            {{-- 3. INPUT: RENTANG CUSTOM --}}
                            <div id="wrap_custom_siswa" class="hidden mb-4 space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Mulai Tanggal</label>
                                    <input type="date" name="tanggal_mulai" id="start_siswa"
                                        value="{{ date('Y-m-01') }}"
                                        class="w-full text-sm border-gray-300 rounded focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Sampai Tanggal</label>
                                    <input type="date" name="tanggal_selesai" id="end_siswa"
                                        value="{{ date('Y-m-t') }}"
                                        class="w-full text-sm border-gray-300 rounded focus:border-indigo-500">
                                </div>
                            </div>

                            {{-- 4. FILTER KELAS --}}
                            <div class="mb-5 border-t border-gray-200 pt-4">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Penyaringan Kelas</label>
                                <select name="kelas_id"
                                    class="w-full text-sm border-gray-300 rounded focus:border-indigo-500" required>
                                    <option value="semua">Semua Kelas</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="button" onclick="submitForm('siswa')"
                                class="w-full mt-auto bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded text-sm transition shadow-sm">Cetak
                                PDF</button>
                        </form>
                    </div>
                </div>

                {{-- ============================================== --}}
                {{-- KARTU 2: LAPORAN STATISTIK AGENDA --}}
                {{-- ============================================== --}}
                <div class="bg-white shadow-sm sm:rounded-lg border-t-4 border-green-500 flex flex-col">
                    <div class="p-6 text-gray-900 flex flex-col flex-1">
                        <h3 class="text-lg font-bold mb-2 text-green-700">Agenda Kegiatan</h3>
                        <p class="text-xs text-gray-500 mb-4">Mencetak rekapitulasi riwayat kegiatan beserta statistik
                            kehadiran jumlah siswa.</p>

                        <form action="{{ route('laporan.cetakAgenda') }}" method="POST" target="_blank"
                            class="flex flex-col flex-1" id="form_agenda">
                            @csrf

                            {{-- 1. RADIO MODE WAKTU --}}
                            <div class="mb-4 bg-green-50 p-3 rounded-lg border border-green-100">
                                <label class="block text-xs font-bold text-green-800 mb-2">Pilih Mode Rentang
                                    Waktu</label>
                                <div class="flex items-center space-x-4">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="mode_agenda" value="bulan"
                                            class="text-green-600 form-radio focus:ring-green-500" checked
                                            onchange="toggleMode('agenda')">
                                        <span class="ml-2 text-xs font-semibold text-gray-700">Bulan & Tahun</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="mode_agenda" value="custom"
                                            class="text-green-600 form-radio focus:ring-green-500"
                                            onchange="toggleMode('agenda')">
                                        <span class="ml-2 text-xs font-semibold text-gray-700">Custom Waktu</span>
                                    </label>
                                </div>
                            </div>

                            {{-- 2. INPUT: SPESIFIK BULAN & TAHUN --}}
                            <div id="wrap_bulan_agenda" class="mb-4 grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Bulan</label>
                                    <select id="input_bulan_agenda"
                                        class="w-full text-sm border-gray-300 rounded focus:border-green-500">
                                        <option value="01">Januari</option>
                                        <option value="02">Februari</option>
                                        <option value="03">Maret</option>
                                        <option value="04">April</option>
                                        <option value="05">Mei</option>
                                        <option value="06">Juni</option>
                                        <option value="07">Juli</option>
                                        <option value="08">Agustus</option>
                                        <option value="09">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                        <option value="semua" class="font-bold text-green-600">Semua Bulan (1 Tahun)
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Ketik Tahun</label>
                                    <input type="number" id="input_tahun_agenda" value="{{ date('Y') }}"
                                        class="w-full text-sm border-gray-300 rounded focus:border-green-500"
                                        placeholder="Misal: 2026" required>
                                </div>
                            </div>

                            {{-- 3. INPUT: RENTANG CUSTOM --}}
                            <div id="wrap_custom_agenda" class="hidden mb-4 space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Mulai Tanggal</label>
                                    <input type="date" name="tanggal_mulai" id="start_agenda"
                                        value="{{ date('Y-m-01') }}"
                                        class="w-full text-sm border-gray-300 rounded focus:border-green-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Sampai Tanggal</label>
                                    <input type="date" name="tanggal_selesai" id="end_agenda"
                                        value="{{ date('Y-m-t') }}"
                                        class="w-full text-sm border-gray-300 rounded focus:border-green-500">
                                </div>
                            </div>

                            <button type="button" onclick="submitForm('agenda')"
                                class="w-full mt-auto bg-green-600 hover:bg-green-800 text-white font-bold py-2 px-4 rounded text-sm transition shadow-sm">Cetak
                                PDF</button>
                        </form>
                    </div>
                </div>

                {{-- ============================================== --}}
                {{-- KARTU 3: LAPORAN PENGAJAR --}}
                {{-- ============================================== --}}
                <div class="bg-white shadow-sm sm:rounded-lg border-t-4 border-amber-500 flex flex-col">
                    <div class="p-6 text-gray-900 flex flex-col flex-1">
                        <h3 class="text-lg font-bold mb-2 text-amber-700">Data Pengurus Vihara</h3>
                        <p class="text-xs text-gray-500 mb-4">Mencetak daftar rekapitulasi kehadiran pengurus dan
                            pengajar Sekolah Minggu.</p>

                        <form action="{{ route('laporan.cetakPengajar') }}" method="POST" target="_blank"
                            class="flex flex-col flex-1" id="form_pengajar">
                            @csrf

                            {{-- 1. RADIO MODE WAKTU --}}
                            <div class="mb-4 bg-amber-50 p-3 rounded-lg border border-amber-100">
                                <label class="block text-xs font-bold text-amber-800 mb-2">Pilih Mode Rentang
                                    Waktu</label>
                                <div class="flex items-center space-x-4">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="mode_pengajar" value="bulan"
                                            class="text-amber-500 form-radio focus:ring-amber-500" checked
                                            onchange="toggleMode('pengajar')">
                                        <span class="ml-2 text-xs font-semibold text-gray-700">Bulan & Tahun</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="mode_pengajar" value="custom"
                                            class="text-amber-500 form-radio focus:ring-amber-500"
                                            onchange="toggleMode('pengajar')">
                                        <span class="ml-2 text-xs font-semibold text-gray-700">Custom Waktu</span>
                                    </label>
                                </div>
                            </div>

                            {{-- 2. INPUT: SPESIFIK BULAN & TAHUN --}}
                            <div id="wrap_bulan_pengajar" class="mb-4 grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Bulan</label>
                                    <select id="input_bulan_pengajar"
                                        class="w-full text-sm border-gray-300 rounded focus:border-amber-500">
                                        <option value="01">Januari</option>
                                        <option value="02">Februari</option>
                                        <option value="03">Maret</option>
                                        <option value="04">April</option>
                                        <option value="05">Mei</option>
                                        <option value="06">Juni</option>
                                        <option value="07">Juli</option>
                                        <option value="08">Agustus</option>
                                        <option value="09">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                        <option value="semua" class="font-bold text-amber-500">Semua Bulan (1 Tahun)
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Ketik Tahun</label>
                                    <input type="number" id="input_tahun_pengajar" value="{{ date('Y') }}"
                                        class="w-full text-sm border-gray-300 rounded focus:border-amber-500"
                                        placeholder="Misal: 2026" required>
                                </div>
                            </div>

                            {{-- 3. INPUT: RENTANG CUSTOM --}}
                            <div id="wrap_custom_pengajar" class="hidden mb-4 space-y-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Mulai Tanggal</label>
                                    <input type="date" name="tanggal_mulai" id="start_pengajar"
                                        value="{{ date('Y-m-01') }}"
                                        class="w-full text-sm border-gray-300 rounded focus:border-amber-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Sampai Tanggal</label>
                                    <input type="date" name="tanggal_selesai" id="end_pengajar"
                                        value="{{ date('Y-m-t') }}"
                                        class="w-full text-sm border-gray-300 rounded focus:border-amber-500">
                                </div>
                            </div>

                            <button type="button" onclick="submitForm('pengajar')"
                                class="w-full mt-auto bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded text-sm transition shadow-sm">Cetak
                                PDF</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- SCRIPT SEDERHANA & KUAT UNTUK PENGATURAN TANGGAL --}}
    <script>
        const currentMonth = "{{ date('m') }}";

        // Set default pilihan bulan agar sesuai dengan bulan saat ini
        document.addEventListener('DOMContentLoaded', () => {
            ['siswa', 'agenda', 'pengajar'].forEach(type => {
                const monthSelect = document.getElementById(`input_bulan_${type}`);
                if (monthSelect) {
                    monthSelect.value = currentMonth;
                }
                toggleMode(type);
            });
        });

        // 1. Fungsi Toggle Menampilkan Box Input Sesuai Pilihan Radio Button
        function toggleMode(type) {
            const mode = document.querySelector(`input[name="mode_${type}"]:checked`).value;
            const wrapBulan = document.getElementById(`wrap_bulan_${type}`);
            const wrapCustom = document.getElementById(`wrap_custom_${type}`);

            if (mode === 'bulan') {
                wrapBulan.classList.remove('hidden');
                wrapCustom.classList.add('hidden');
            } else {
                wrapBulan.classList.add('hidden');
                wrapCustom.classList.remove('hidden');
            }
        }

        // 2. Fungsi Validasi & Sinkronisasi Form sebelum dikirim ke Server
        function submitForm(type) {
            const mode = document.querySelector(`input[name="mode_${type}"]:checked`).value;
            const startInput = document.getElementById(`start_${type}`);
            const endInput = document.getElementById(`end_${type}`);

            // Jika mode Bulan yang menyala, kalkulasi tanggalnya menggunakan Javascript
            // lalu isian form tanggal di belakang layar akan terisi otomatis.
            if (mode === 'bulan') {
                const bulanVal = document.getElementById(`input_bulan_${type}`).value;
                const tahunVal = document.getElementById(`input_tahun_${type}`).value;

                if (!tahunVal || tahunVal.length < 4) {
                    alert("Harap ketik kotak Tahun dengan benar (Contoh: 2026).");
                    return;
                }

                if (bulanVal === 'semua') {
                    // Cetak Full 1 Tahun masehi penuh
                    startInput.value = `${tahunVal}-01-01`;
                    endInput.value = `${tahunVal}-12-31`;
                } else {
                    // Cetak hanya 1 bulan spesifik
                    const firstDay = `${tahunVal}-${bulanVal}-01`;
                    // Mendapatkan tanggal terakhir di bulan tsb (28/29/30/31)
                    const lastDayObj = new Date(tahunVal, parseInt(bulanVal), 0);
                    const lastDay = `${tahunVal}-${bulanVal}-${String(lastDayObj.getDate()).padStart(2, '0')}`;

                    startInput.value = firstDay;
                    endInput.value = lastDay;
                }
            }

            // Keamanan Terakhir: Cek jika input tanggal masih kosong
            if (!startInput.value || !endInput.value) {
                alert('Terdapat kesalahan rentang waktu. Harap periksa input tanggal Anda.');
                return;
            }

            document.getElementById(`form_${type}`).submit();
        }
    </script>
</x-app-layout>
