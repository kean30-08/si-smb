<x-app-layout>
    <style>
        #reader button {
            background-color: #4f46e5 !important;
            /* Warna Biru Indigo Tailwind */
            color: white !important;
            padding: 10px 20px !important;
            border-radius: 8px !important;
            border: none !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            cursor: pointer !important;
            margin: 10px 5px !important;
            transition: background-color 0.3s ease !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }

        #reader button:hover {
            background-color: #4338ca !important;
        }

        #reader a {
            color: #4f46e5 !important;
            text-decoration: none !important;
            font-weight: 600 !important;
            display: inline-block !important;
            margin-top: 15px !important;
            padding: 5px 10px !important;
            border: 1px solid #e0e7ff !important;
            border-radius: 6px !important;
            background-color: #2d10be !important;
        }

        #reader a:hover {
            text-decoration: underline !important;
            background-color: #e0e7ff !important;
        }

        #reader span {
            font-family: 'Segoe UI', sans-serif !important;
            color: #4b5563 !important;
            /* Abu-abu gelap */
        }

        #reader {
            border: none !important;
            border-radius: 12px !important;
            overflow: hidden !important;
        }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mulai Absensi Kelas (Scanner)') }}
            </h2>
            <a href="{{ route('absensi.index', ['tanggal' => $agenda->tanggal, 'agenda_id' => $agenda->id]) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- INFORMASI AGENDA YANG SEDANG DI-SCAN --}}
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 shadow-sm rounded-r-lg">
                <div class="flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="text-blue-600 mr-2">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                        <line x1="16" x2="16" y1="2" y2="6" />
                        <line x1="8" x2="8" y1="2" y2="6" />
                        <line x1="3" x2="21" y1="10" y2="10" />
                    </svg>
                    <h3 class="font-bold text-blue-800 text-lg">Sesi Absensi: {{ $agenda->nama_kegiatan }}</h3>
                </div>

                <div class="ml-7 text-sm text-blue-700">
                    <p><strong>Tanggal:</strong>
                        {{ \Carbon\Carbon::parse($agenda->tanggal)->translatedFormat('l, d F Y') }}</p>
                    <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($agenda->waktu_mulai)->format('H:i') }} -
                        {{ $agenda->waktu_selesai ? \Carbon\Carbon::parse($agenda->waktu_selesai)->format('H:i') : 'Selesai' }}
                    </p>
                </div>
                <p class="text-xs text-blue-600 mt-3 italic">*Pindai barcode siswa untuk merekam kehadiran khusus pada
                    sesi kegiatan ini saja.</p>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500">
                <div class="p-6 text-gray-900 text-center">

                    <h3 class="text-lg font-bold mb-4 text-gray-700">Arahkan Kamera ke Barcode Siswa</h3>

                    {{-- Kotak tempat kamera akan muncul --}}
                    <div id="reader"
                        class="mx-auto w-full max-w-md rounded-lg overflow-hidden border-2 border-dashed border-gray-300">
                    </div>

                    <p class="mt-4 text-sm text-gray-500">Pastikan pencahayaan cukup agar barcode mudah terbaca.</p>

                </div>
            </div>

        </div>
    </div>

    {{-- Script untuk HTML5-QRCode dan Logika AJAX --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        // Mencegah scanner mengirim data bertubi-tubi saat 1 barcode ditahan di depan kamera
        let isProcessing = false;

        // Fungsi yang dijalankan saat barcode BERHASIL terbaca
        function onScanSuccess(decodedText, decodedResult) {
            // Jika sedang memproses data sebelumnya, abaikan scan ini
            if (isProcessing) return;

            isProcessing = true; // Kunci proses

            // Putar suara
            let audio = new Audio('https://www.soundjay.com/buttons/sounds/beep-07a.mp3');
            audio.play().catch(e => console.log("Audio autoplay diblokir browser"));

            // Kirim data Barcode dan AGENDA_ID ke Controller
            fetch('{{ route('absensi.prosesScan') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        barcode: decodedText,
                        agenda_id: '{{ $agenda->id }}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // PERBAIKAN: Hapus timer, nyalakan tombol OK
                        Swal.fire({
                            icon: 'success',
                            title: 'Terekam!',
                            text: data.message,
                            showConfirmButton: true,
                            confirmButtonText: 'OK, Lanjut',
                            confirmButtonColor: '#4f46e5' // Warna tombol serasi dengan tema
                        }).then((result) => {
                            // Kunci baru dibuka SETELAH tombol OK ditekan
                            isProcessing = false;
                        });
                    } else {
                        // PERBAIKAN: Hapus timer, nyalakan tombol OK
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal / Info!',
                            text: data.message,
                            showConfirmButton: true,
                            confirmButtonText: 'OK, Mengerti',
                            confirmButtonColor: '#d33' // Warna merah untuk error/peringatan
                        }).then((result) => {
                            isProcessing = false;
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Jaringan',
                        text: 'Gagal terhubung ke server.',
                        showConfirmButton: true,
                        confirmButtonText: 'Tutup'
                    }).then(() => {
                        isProcessing = false;
                    });
                });
        }

        function onScanFailure(error) {
            // Biarkan kosong agar tidak spam log
        }

        // Konfigurasi dan Penyalakan Kamera
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                },
                supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
            },
            /* verbose= */
            false
        );

        // Mulai Scanner
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>
</x-app-layout>
