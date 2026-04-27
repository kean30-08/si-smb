<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Refleksi Siswa - Sekolah Minggu</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- LIBRARY JQUERY & SELECT2 & SWEETALERT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* Modifikasi Kotak Select2 agar mirip dengan tinggi input Tailwind */
        .select2-container .select2-selection--single {
            height: 42px !important;
            border-color: #d1d5db !important;
            display: flex;
            align-items: center;
            border-radius: 0.375rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }

        /* FIX UNTUK MOBILE: Cegah Dropdown Meluber Keluar Layar */
        .select2-container {
            width: 100% !important;
        }

        .select2-dropdown {
            width: auto !important;
            max-width: 100%;
            border-radius: 0.375rem !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            border-color: #d1d5db !important;
        }
    </style>
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
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 text-sm text-blue-700">
                    <strong>Informasi:</strong> Form ini otomatis ditutup pada
                    <strong>{{ $waktuTutup->translatedFormat('d M Y, H:i') }}</strong>. Diperlukan Verifikasi OTP dari
                    email orang tua untuk mengirim jawaban.
                </div>

                <form action="{{ route('refleksi.store', $tanggal) }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Pencarian Nama Siswa dengan Select2 --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-bold text-indigo-700 mb-1">Cari Nama Kamu *</label>
                        <select id="siswa_id" name="siswa_id" required class="select2-siswa w-full">
                            <option value="" disabled selected>-- Ketik dan Pilih Nama Kamu --</option>
                            @foreach ($siswas as $s)
                                <option value="{{ $s->id }}" data-nis="{{ $s->nis }}"
                                    data-kelas="{{ $s->nilaiKehadiranAktif->kelas_id ?? '' }}"
                                    data-ortu="{{ $s->nama_orang_tua }}" data-email="{{ $s->email_orang_tua }}">
                                    {{ $s->nama_lengkap }} (NIS: {{ $s->nis }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Informasi Siswa (Dikunci / Readonly) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIS</label>
                            <input type="text" id="nis_display" readonly placeholder="Otomatis"
                                class="mt-1 block w-full rounded-md border-gray-200 bg-gray-100 text-gray-500 shadow-sm cursor-not-allowed font-medium">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kelas</label>
                            <select id="kelas_display" disabled
                                class="mt-1 block w-full rounded-md border-gray-200 bg-gray-100 text-gray-500 shadow-sm cursor-not-allowed font-medium">
                                <option value="">Otomatis Terisi</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Informasi Orang Tua & OTP --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Orang Tua</label>
                            <input type="text" id="ortu_display" readonly placeholder="Otomatis"
                                class="mt-1 block w-full rounded-md border-gray-200 bg-gray-100 text-gray-500 shadow-sm cursor-not-allowed font-medium">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email Orang Tua untuk Tujuan
                                OTP</label>
                            <input type="text" id="email_display" readonly placeholder="Pilih nama terlebih dahulu"
                                class="mt-1 block w-full rounded-md border-gray-200 bg-indigo-50 text-indigo-700 shadow-sm cursor-not-allowed font-bold tracking-wider">
                        </div>

                        {{-- Input OTP --}}
                        <div class="sm:col-span-2 border-t border-gray-200 pt-4 mt-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Verifikasi OTP *</label>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <input type="number" name="otp" placeholder="Masukkan 6 Digit OTP" required
                                    class="flex-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 tracking-widest font-bold text-center sm:text-left text-lg sm:text-base">

                                <button type="button" id="btnRequestOtp" onclick="requestOtp()"
                                    class="sm:w-auto bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-6 rounded-md shadow-sm transition whitespace-nowrap">
                                    Minta Kode OTP
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Klik tombol di atas untuk mengirim kode ke email orang
                                tua.</p>
                        </div>
                    </div>

                    {{-- Textarea Refleksi --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Rangkuman Kegiatan Hari Ini *</label>
                        <textarea name="rangkuman" rows="3" required placeholder="Ceritakan apa yang kamu pelajari hari ini..."
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('rangkuman') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Bagian yang Disukai *</label>
                        <textarea name="bagian_disukai" rows="3" required placeholder="Sebutkan hal yang paling kamu senangi..."
                            class="block w-full rounded-md border-green-400 border-l-4 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('bagian_disukai') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Bagian yang Kurang Disukai *</label>
                        <textarea name="bagian_kurang_disukai" rows="3" required
                            placeholder="Apakah ada yang membosankan? Ceritakan disini..."
                            class="block w-full rounded-md border-red-400 border-l-4 shadow-sm focus:border-red-500 focus:ring-red-500">{{ old('bagian_kurang_disukai') }}</textarea>
                    </div>

                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 rounded-md shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition">
                        Verifikasi & Kirim Refleksi Saya
                    </button>
                </form>
            @endif
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 dengan Fix Mobile Width & Dropdown Parent
            $('.select2-siswa').select2({
                placeholder: "-- Cari dan Pilih Nama Kamu --",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#siswa_id').parent()
            });

            // Logika Auto-Fill dari Option terpilih
            $('#siswa_id').on('change', function() {
                let selected = $(this).find('option:selected');

                $('#nis_display').val(selected.data('nis'));
                $('#kelas_display').val(selected.data('kelas'));
                $('#ortu_display').val(selected.data('ortu'));

                let email = selected.data('email');
                if (email) {
                    // Memecah email menjadi nama dan domain (contoh: budi123 dan gmail.com)
                    let parts = email.split("@");
                    let name = parts[0];
                    let domain = parts[1];

                    // Ambil maksimal 3 huruf pertama. Jika namanya sangat pendek (misal: "ab"), ambil 1 huruf saja.
                    let visibleLen = name.length > 3 ? 3 : 1;
                    let maskedName = name.substring(0, visibleLen) + "***";

                    $('#email_display').val(maskedName + "@" + domain);
                } else {
                    $('#email_display').val('BELUM TERDAFTAR!');
                }
            });
        });

        // Logika AJAX Kirim OTP
        function requestOtp() {
            const siswaId = document.getElementById('siswa_id').value;
            const btn = document.getElementById('btnRequestOtp');

            if (!siswaId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Nama',
                    text: 'Silakan cari dan pilih nama kamu terlebih dahulu!'
                });
                return;
            }

            const originalText = btn.innerHTML;
            btn.innerHTML = 'Mengirim...';
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            fetch('{{ route('refleksi.sendOtp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        siswa_id: siswaId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terkirim!',
                            text: data.message
                        });
                        // Timer Cooldown 60 Detik untuk mencegah spamming
                        let timeLeft = 60;
                        const timer = setInterval(() => {
                            if (timeLeft <= 0) {
                                clearInterval(timer);
                                btn.innerHTML = originalText;
                                btn.disabled = false;
                                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                            } else {
                                btn.innerHTML = `Tunggu (${timeLeft}s)`;
                                timeLeft -= 1;
                            }
                        }, 1000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message
                        });
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                        btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Server',
                        text: 'Terjadi kesalahan pada sistem koneksi.'
                    });
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                });
        }

        // Handle Alert Notifikasi dari Server Backend
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#4f46e5'
            }).then(() => {
                window.location.reload();
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: '<ul class="text-left text-sm">@foreach ($errors->all() as $error)<li>- {{ $error }}</li>@endforeach</ul>'
            });
        @endif
    </script>
</body>

</html>
