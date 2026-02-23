<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mulai Absensi Kelas (Scanner)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Info Kegiatan Hari Ini --}}
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 shadow-sm rounded-r-lg">
                <div class="flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600 mr-2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    <h3 class="font-bold text-blue-800">Jadwal Hari Ini: {{ \Carbon\Carbon::parse($hari_ini)->translatedFormat('l, d F Y') }}</h3>
                </div>
                @if($agendas->isEmpty())
                    <p class="text-sm text-red-600 font-semibold">Tidak ada kegiatan yang dijadwalkan hari ini. Absensi tidak dapat dilakukan.</p>
                @else
                    <ul class="list-disc list-inside text-sm text-blue-700">
                        @foreach($agendas as $agenda)
                            <li><strong>{{ $agenda->nama_kegiatan }}</strong> ({{ \Carbon\Carbon::parse($agenda->waktu_mulai)->format('H:i') }})</li>
                        @endforeach
                    </ul>
                    <p class="text-xs text-blue-600 mt-2 italic">*Satu kali scan akan otomatis mengisi kehadiran untuk semua kegiatan di atas.</p>
                @endif
            </div>

            {{-- Area Kamera Scanner --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500">
                <div class="p-6 text-gray-900 text-center">
                    
                    <h3 class="text-lg font-bold mb-4 text-gray-700">Arahkan Kamera ke Barcode Siswa</h3>
                    
                    {{-- Kotak tempat kamera akan muncul --}}
                    <div id="reader" class="mx-auto w-full max-w-md rounded-lg overflow-hidden border-2 border-dashed border-gray-300"></div>
                    
                    <p class="mt-4 text-sm text-gray-500">Pastikan pencahayaan cukup agar barcode mudah terbaca.</p>

                </div>
            </div>

        </div>
    </div>

    {{-- Script untuk HTML5-QRCode dan Logika AJAX --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Mencegah scanner mengirim data bertubi-tubi saat 1 barcode ditahan di depan kamera
        let isProcessing = false;

        // Fungsi yang dijalankan saat barcode BERHASIL terbaca
        function onScanSuccess(decodedText, decodedResult) {
            // Jika sedang memproses data sebelumnya, abaikan scan ini
            if (isProcessing) return;
            
            isProcessing = true; // Kunci proses

            // Putar suara Bip (Opsional, agar terasa seperti mesin kasir)
            let audio = new Audio('https://www.soundjay.com/buttons/sounds/beep-07a.mp3');
            audio.play().catch(e => console.log("Audio autoplay diblokir browser"));

            // Kirim data Barcode ke Controller secara diam-diam (AJAX) tanpa refresh halaman
            fetch('{{ route('absensi.prosesScan') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ barcode: decodedText })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Munculkan Pop-up Sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Terekam!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        isProcessing = false; // Buka kunci setelah pop-up hilang (kamera siap scan siswa berikutnya)
                    });
                } else {
                    // Munculkan Pop-up Gagal (Misal format salah atau siswa tidak ada)
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message,
                        timer: 2500,
                        showConfirmButton: false
                    }).then(() => {
                        isProcessing = false;
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({ icon: 'error', title: 'Error Jaringan', text: 'Gagal terhubung ke server.' });
                isProcessing = false;
            });
        }

        // Fungsi saat kamera gagal membaca (diabaikan saja, karena kamera akan terus mencoba mencari gambar yang pas)
        function onScanFailure(error) {
            // console.warn(`Code scan error = ${error}`);
        }

        // Konfigurasi dan Penyalakan Kamera
        // fps = Kecepatan baca per detik, qrbox = ukuran area fokus kotak di layar
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: {width: 250, height: 250} },
            /* verbose= */ false
        );
        
        // Mulai Scanner
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);

    </script>
</x-app-layout>