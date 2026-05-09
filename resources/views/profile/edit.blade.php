<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Profile') }}
            </h2>
            <div class="w-full sm:w-auto flex">
                <a href="{{ route('dashboard') }}"
                    class="w-full sm:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Form Ganti Email --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="w-full md:max-w-4xl mx-auto">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Form Ganti Password (Sudah di-uncomment) --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="w-full md:max-w-4xl mx-auto">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL OTP KEAMANAN --}}
    <div id="otpModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Verifikasi Keamanan
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4" id="otpMessage">
                                    Mempersiapkan kode keamanan...
                                </p>
                                <input type="text" id="otpInput" maxlength="6"
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md font-bold text-center tracking-widest text-lg"
                                    placeholder="123456">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="btnVerifikasi" onclick="submitOtp()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition">
                        Verifikasi
                    </button>
                    <button type="button" onclick="closeOtpModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT PENCEGAT FORM & AJAX OTP --}}
    {{-- SCRIPT PENCEGAT FORM & AJAX OTP DENGAN SWEETALERT --}}
    <script>
        let pendingForm = null;
        let currentEmail = "{{ auth()->user()->email }}";
        let otpModal = document.getElementById('otpModal');
        let otpMessage = document.getElementById('otpMessage');
        let btnVerifikasi = document.getElementById('btnVerifikasi');

        // Pencegat Form Email
        function handleProfileSubmit(e) {
            let newEmail = document.getElementById('email').value;
            // Jika email diubah, cegat dan minta OTP
            if (newEmail.trim() !== currentEmail.trim()) {
                e.preventDefault();
                pendingForm = document.getElementById('profileForm');
                openOtpModal();
            }
            // Jika email tidak berubah, biarkan form ter-submit secara normal
        }

        // Pencegat Form Password
        function handlePasswordSubmit(e) {
            e.preventDefault(); // Selalu cegat jika mau ganti password
            pendingForm = document.getElementById('passwordForm');
            openOtpModal();
        }

        function openOtpModal() {
            otpModal.classList.remove('hidden');
            document.getElementById('otpInput').value = '';

            btnVerifikasi.innerHTML = 'Mengirim OTP...';
            btnVerifikasi.disabled = true;
            otpMessage.innerHTML = 'Sedang mengirimkan kode ke email lama Anda...';

            fetch("{{ route('profile.sendOtp') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    btnVerifikasi.innerHTML = 'Verifikasi';
                    btnVerifikasi.disabled = false;
                    otpMessage.innerHTML = data.message;

                    // Notif Pop-up Berhasil Kirim
                    Swal.fire({
                        icon: 'info',
                        title: 'Cek Email Anda',
                        text: data.message,
                        confirmButtonColor: '#3085d6',
                    });
                } else {
                    closeOtpModal();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message,
                        confirmButtonColor: '#d33',
                    });
                }
            }).catch(err => {
                closeOtpModal();
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Gagal terhubung ke server. Periksa koneksi internet Anda.',
                    confirmButtonColor: '#d33',
                });
            });
        }

        function closeOtpModal() {
            otpModal.classList.add('hidden');
            pendingForm = null;
        }

        function submitOtp() {
            let otp = document.getElementById('otpInput').value;
            if (otp.length < 6) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Masukkan 6 digit OTP terlebih dahulu.',
                    confirmButtonColor: '#f8bb86',
                });
                return;
            }

            btnVerifikasi.innerHTML = 'Mengecek...';
            btnVerifikasi.disabled = true;

            fetch("{{ route('profile.verifyOtp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    otp: otp
                })
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    // OTP Valid! Sembunyikan modal dan tampilkan animasi sukses SweetAlert
                    otpModal.classList.add('hidden');

                    Swal.fire({
                        icon: 'success',
                        title: 'Verifikasi Berhasil!',
                        text: 'Menyimpan perubahan data Anda...',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    }).then(() => {
                        pendingForm.submit(); // Lepaskan form setelah animasi SweetAlert selesai
                    });

                } else {
                    // OTP Salah
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Ditolak!',
                        text: data.message,
                        confirmButtonColor: '#d33',
                    });
                    btnVerifikasi.innerHTML = 'Verifikasi';
                    btnVerifikasi.disabled = false;
                    document.getElementById('otpInput').value = ''; // Kosongkan input
                }
            }).catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Gagal memverifikasi OTP. Coba lagi.',
                    confirmButtonColor: '#d33',
                });
                btnVerifikasi.innerHTML = 'Verifikasi';
                btnVerifikasi.disabled = false;
            });
        }
    </script>
</x-app-layout>
