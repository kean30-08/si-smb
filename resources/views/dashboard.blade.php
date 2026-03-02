<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Statistik Utama') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- FILTER DATA DI ATAS --}}
            <div class="mb-6 flex justify-start ">
                <form action="{{ route('dashboard') }}" method="GET" class="flex items-center bg-white p-2 rounded-lg shadow-sm border-l-4 border-indigo-500">
                    <label class="mr-3 ml-2 text-sm font-bold text-gray-700">Filter Data:</label>
                    <select name="rentang" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 cursor-pointer bg-indigo-50 text-indigo-800 font-semibold border-none py-1.5 pl-3 pr-8">
                        <option value="1" {{ $rentang_bulan == 1 ? 'selected' : '' }}>1 Bulan Terakhir</option>
                        <option value="2" {{ $rentang_bulan == 2 ? 'selected' : '' }}>2 Bulan Terakhir</option>
                        <option value="3" {{ $rentang_bulan == 3 ? 'selected' : '' }}>3 Bulan Terakhir</option>
                        <option value="4" {{ $rentang_bulan == 4 ? 'selected' : '' }}>4 Bulan Terakhir</option>
                        <option value="5" {{ $rentang_bulan == 5 ? 'selected' : '' }}>5 Bulan Terakhir</option>
                        <option value="6" {{ $rentang_bulan == 6 ? 'selected' : '' }}>1 Semester (6 Bulan)</option>
                    </select>
                </form>
            </div>

            {{-- 1. KARTU RINGKASAN (KPI CARDS) - SEKARANG 3 KOLOM --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-semibold mb-1">Total Siswa Aktif</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $total_siswa }}</h3>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-amber-500 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-semibold mb-1">Total Pengajar</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $total_pengajar }}</h3>
                    </div>
                    <div class="p-3 bg-amber-100 rounded-full text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-semibold mb-1">Total Agenda Harian</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $total_agenda_harian }} <span class="text-xs font-normal text-gray-400">Jadwal</span></h3>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full text-purple-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    </div>
                </div>
            </div>

            {{-- 2. GRAFIK & LEADERBOARD --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col">
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-lg font-bold text-gray-800 mb-6">Grafik Kehadiran Siswa</h3>
                        
                        {{-- Area Grafik --}}
                        <div class="relative h-64 w-full mb-6">
                            <canvas id="attendanceChart"></canvas>
                        </div>

                        {{-- Rekapan Siswa Tidak Hadir di Bawah Grafik --}}
                        <div class="mt-auto grid grid-cols-3 gap-4 border-t border-gray-100 pt-5">
                            <div class="bg-yellow-50 rounded-lg p-3 text-center border border-yellow-100">
                                <p class="text-xs text-yellow-600 font-bold uppercase mb-1">Total Sakit</p>
                                <p class="text-xl font-black text-yellow-800">{{ $total_sakit_period }} <span class="text-xs font-normal">Siswa</span></p>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-3 text-center border border-blue-100">
                                <p class="text-xs text-blue-600 font-bold uppercase mb-1">Total Izin</p>
                                <p class="text-xl font-black text-blue-800">{{ $total_izin_period }} <span class="text-xs font-normal">Siswa</span></p>
                            </div>
                            <div class="bg-red-50 rounded-lg p-3 text-center border border-red-100">
                                <p class="text-xs text-red-600 font-bold uppercase mb-1">Total Alpa</p>
                                <p class="text-xl font-black text-red-800">{{ $total_alpa_period }} <span class="text-xs font-normal">Siswa</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 bg-indigo-50 h-full">
                        <div class="flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-600 mr-2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                            <h3 class="text-lg font-bold text-indigo-800">Top 5 Siswa Teladan</h3>
                        </div>
                        <p class="text-xs text-indigo-600 mb-4">Jika terdapat poin yang sama, peringkat akan ditentukan berdasarkan rata-rata kedatangan paling awal.</p>
                        
                        <ul class="divide-y divide-indigo-100">
                            @foreach($top_siswas as $index => $siswa)
                                <li class="py-3 flex justify-between items-center">
                                    <div class="flex items-center">
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mr-3 
                                            @if($index == 0) bg-yellow-400 text-yellow-900 
                                            @elseif($index == 1) bg-gray-300 text-gray-800 
                                            @elseif($index == 2) bg-orange-300 text-orange-900 
                                            @else bg-white border border-gray-300 text-gray-500 @endif
                                        ">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <p class="font-bold text-sm text-gray-800">{{ $siswa->nama_lengkap }}</p>
                                            <p class="text-xs text-gray-500">{{ $siswa->kelas->nama_kelas ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right flex flex-col items-end">
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full mb-1">
                                            {{ $siswa->persentase }}%
                                        </span>
                                        <span class="text-[10px] font-bold text-indigo-500 border border-indigo-200 bg-white px-1.5 rounded">
                                            {{ $siswa->poin_keaktifan }} Pts
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                            @if(count($top_siswas) == 0)
                                <p class="text-sm text-gray-500 italic text-center py-4">Belum ada data absensi.</p>
                            @endif
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- SCRIPT CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            
            const labels = {!! json_encode($label_grafik) !!};
            
            // Ambil seluruh data dari Controller
            const dataHadir = {!! json_encode($data_hadir) !!};
            const dataSakit = {!! json_encode($data_sakit) !!};
            const dataIzin = {!! json_encode($data_izin) !!};
            const dataAlpa = {!! json_encode($data_alpa) !!};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    
                    datasets: [
                        {
                            label: 'Hadir',
                            data: dataHadir,
                            backgroundColor: 'rgba(34, 197, 94, 0.9)', // Opacity dinaikkan jadi 0.9 agar lebih solid
                            borderWidth: 0, // Garis tepi dihilangkan
                            stack: 'stack1'
                        },
                        {
                            label: 'Sakit',
                            data: dataSakit,
                            backgroundColor: 'rgba(234, 179, 8, 0.9)',
                            borderWidth: 0,
                            stack: 'stack1'
                        },
                        {
                            label: 'Izin',
                            data: dataIzin,
                            backgroundColor: 'rgba(59, 130, 246, 0.9)',
                            borderWidth: 0,
                            stack: 'stack1'
                        },
                        {
                            label: 'Alpa',
                            data: dataAlpa,
                            backgroundColor: 'rgba(239, 68, 68, 0.9)',
                            borderWidth: 0,
                            stack: 'stack1'
                        }
                    ]
                    
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    // Mengaktifkan fitur interaksi bertumpuk
                    interaction: {
                        mode: 'index', // Saat dihover, langsung menampilkan tooltip untuk seluruh stack
                        intersect: false // Tidak perlu tepat kena bar
                    },
                    scales: {
                        y: {
                            stacked: true, // AKTIFKAN STACK PADA SUMBU Y
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        },
                        x: {
                            stacked: true // AKTIFKAN STACK PADA SUMBU X
                        }
                    },
                    plugins: {
                        // Memunculkan Legenda agar Admin tahu arti warnanya
                        legend: { 
                            display: true, 
                            position: 'bottom',
                            labels: {
                                font: { size: 12 },
                                padding: 15
                            }
                        },
                        // Tooltip bawaan otomatis memuat rincian keempat status tersebut karena mode 'index'
                        tooltip: {
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            boxPadding: 5 // Jarak warna kotak di dalam tooltip
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>