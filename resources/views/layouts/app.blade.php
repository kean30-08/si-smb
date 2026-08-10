<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SMBVDC') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000, // Hilang otomatis dalam 4 detik
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Menangkap Session 'success' dari Controller
        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}"
            });
        @endif

        // Menangkap Session 'error' manual dari Controller
        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}"
            });
        @endif

        // [BAGIAN BARU] Menangkap Error Validasi Otomatis dari Laravel
        @if ($errors->any())
            Toast.fire({
                icon: 'warning', // Pakai warning atau error terserah Anda
                title: 'Validasi Gagal!',
                text: "{{ $errors->first() }}" // Ini akan mengambil pesan error pertama ("Kelas sudah dibuat...")
            });
        @endif
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
    {{-- ======================================================== --}}
    {{-- GLOBAL NOTIFICATION: PENGINGAT TAHUN AJARAN              --}}
    {{-- ======================================================== --}}
    @php
        // 1. Dapatkan Bulan dan Tahun saat ini (Real-time)
        $now = \Carbon\Carbon::now();
        $currentYear = $now->year;
        $currentMonth = $now->month;

        // 2. Tentukan Tahun Ajaran yang SEHARUSNYA berjalan
        if ($currentMonth >= 7) {
            // Juli - Desember: Semester Ganjil (Tahun Ini / Tahun Depan)
            $expectedTa = $currentYear . '/' . ($currentYear + 1) . ' Ganjil';
        } else {
            // Januari - Juni: Semester Genap (Tahun Lalu / Tahun Ini)
            $expectedTa = ($currentYear - 1) . '/' . $currentYear . ' Genap';
        }

        // 3. Ambil Tahun Ajaran yang saat ini sedang AKTIF di database
        $activeTa = \App\Models\TahunAjaran::where('status', 'aktif')->first();
        $activeTaName = $activeTa ? $activeTa->tahun_ajaran : 'Belum Ada TA Aktif';

        // 4. Cek apakah TA Aktif tidak sama dengan TA yang Seharusnya
        $isTaMismatch = $activeTaName !== $expectedTa;
    @endphp

    @if ($isTaMismatch)
        <div x-data="{ showTaReminder: !sessionStorage.getItem('hideTaReminder') }"
             x-show="showTaReminder" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-10" 
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-10"
             style="display: none;"
             class="fixed bottom-6 right-6 z-[9999] w-80 sm:w-96 bg-white rounded-xl shadow-2xl border border-red-200 overflow-hidden">
            
            {{-- Header Merah / Kuning Peringatan --}}
            <div class="bg-red-500 px-4 py-3 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="text-white font-bold text-sm sm:text-base">Peringatan Sistem!</h3>
            </div>

            {{-- Konten Peringatan --}}
            <div class="p-4 bg-white">
                <p class="text-sm text-gray-600 mb-4 leading-relaxed">
                    Sistem mendeteksi bahwa Tahun Ajaran yang aktif saat ini <strong>tertinggal atau tidak sesuai</strong> dengan bulan berjalan.
                </p>
                
                <div class="bg-gray-50 border border-gray-200 rounded p-3 text-sm mb-5">
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-500">TA Aktif Sistem:</span>
                        <span class="font-bold text-red-600">{{ $activeTaName }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Seharusnya:</span>
                        <span class="font-bold text-green-600">{{ $expectedTa }}</span>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end items-center gap-2">
                    <button @click="showTaReminder = false; sessionStorage.setItem('hideTaReminder', 'true')" type="button" class="px-4 py-2 text-xs font-semibold text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        Abaikan Dulu
                    </button>
                    <a href="{{ route('tahun_ajaran.index') }}" class="inline-flex items-center gap-1 px-4 py-2 text-xs font-bold text-white bg-red-500 hover:bg-red-600 shadow-md rounded-lg transition">
                        Ganti TA Sekarang
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    @endif
    {{-- ======================================================== --}}
</body>

</html>
