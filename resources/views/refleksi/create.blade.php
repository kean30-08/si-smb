<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Refleksi Siswa - Sekolah Minggu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100 font-sans antialiased min-h-screen flex flex-col items-center pt-8 pb-12 px-4 sm:px-6 lg:px-8">

    <div class="w-full max-w-2xl bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-indigo-600 px-6 py-8 text-center text-white">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2">Form Refleksi Kegiatan</h1>
            <p class="text-indigo-100 text-sm sm:text-base">Tanggal:
                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</p>
        </div>

        <div class="px-6 py-8 sm:p-10">
            @if ($statusForm === 'belum_buka')
                <div class="text-center py-10">
                    <div class="text-6xl mb-4">⏳</div>
                    <h2 class="text-2xl font-bold text-gray-800">Sabar ya! Form Belum Dibuka</h2>
                    <p class="mt-2 text-gray-600">Form refleksi ini baru bisa diakses setelah acara dimulai pada:</p>
                    <p class="mt-2 font-bold text-lg text-indigo-600">{{ $waktuBuka->translatedFormat('d F Y - H:i') }}
                    </p>
                </div>
            @elseif ($statusForm === 'sudah_tutup')
                <div class="text-center py-10">
                    <div class="text-6xl mb-4">🔒</div>
                    <h2 class="text-2xl font-bold text-gray-800">Yahh, Waktu Habis!</h2>
                    <p class="mt-2 text-gray-600">Batas pengisian form 24 jam untuk kegiatan ini telah berakhir pada:
                    </p>
                    <p class="mt-2 font-bold text-lg text-red-600">{{ $waktuTutup->translatedFormat('d F Y - H:i') }}
                    </p>
                </div>
            @else
                {{-- Form Muncul Jika Status "Buka" --}}
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 text-sm text-blue-700">
                    <strong>Informasi:</strong> Form ini akan otomatis ditutup pada
                    <strong>{{ $waktuTutup->translatedFormat('d M Y, H:i') }}</strong>.
                </div>

                <form action="{{ route('refleksi.store', $tanggal) }}" method="POST" class="space-y-6">
                    @csrf
                    {{-- Informasi Siswa --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Lengkap Siswa *</label>
                            <input type="text" name="nama_siswa" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIS *</label>
                            <input type="text" name="nis" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Orang Tua *</label>
                            <input type="text" name="nama_orang_tua" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email Orang Tua</label>
                            <input type="email" name="email_orang_tua" placeholder="Opsional"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Textarea Refleksi --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Rangkuman Kegiatan Hari Ini *</label>
                        <textarea name="rangkuman" rows="3" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Bagian yang Disukai *</label>
                        <textarea name="bagian_disukai" rows="3" required
                            class="block w-full rounded-md border-green-400 border-l-4 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Bagian yang Kurang Disukai *</label>
                        <textarea name="bagian_kurang_disukai" rows="3" required
                            class="block w-full rounded-md border-red-400 border-l-4 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 rounded-md shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition">
                        Kirim Refleksi Saya
                    </button>
                </form>
            @endif
        </div>
    </div>

    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#4f46e5'
            });
        @endif
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: '<ul class="text-left">@foreach ($errors->all() as $error)<li>- {{ $error }}</li>@endforeach</ul>'
            });
        @endif
    </script>
</body>

</html>
