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

            {{-- Form Ganti Password --}}
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

    <script>
        let pendingForm = null;
        let currentEmail = "{{ auth()->user()->email }}";
        let otpModal = document.getElementById('otpModal');
        let otpMessage = document.getElementById('otpMessage');
        let btnVerifikasi = document.getElementById('btnVerifikasi');
        let otpInput = document.getElementById('otpInput');

        // State Manajemen OTP
        let isChangingEmail = false;
        let currentOtpStep = 1; // 1: Verif Email Lama, 2: Verif Email Baru
        let newEmailInputVal = "";

        /**
         * Pencegat Form Email
         */
        function handleProfileSubmit(e) {
            newEmailInputVal = document.getElementById('email').value;
            // Jika email diubah, mulai dari step 1 (email lama)
            if (newEmailInputVal.trim() !== currentEmail.trim()) {
                e.preventDefault();
                pendingForm = document.getElementById('profileForm');
                isChangingEmail = true;
                currentOtpStep = 1;
                triggerSendOtp();
            }
        }

        /**
         * Pencegat Form Password
         */
        function handlePasswordSubmit(e) {
            e.preventDefault();
            pendingForm = document.getElementById('passwordForm');
            isChangingEmail = false;
            currentOtpStep = 1; // Hanya butuh 1 step untuk ganti password
            triggerSendOtp();
        }

        /**
         * Mengirim request pengiriman OTP ke backend
         */
        function triggerSendOtp() {
            otpModal.classList.remove('hidden');
            otpInput.value = '';
            btnVerifikasi.innerHTML = 'Mengirim...';
            btnVerifikasi.disabled = true;

            // Tentukan target: Jika sedang ganti email & sudah step 2, targetnya 'new'
            let target = (isChangingEmail && currentOtpStep === 2) ? 'new' : 'old';
            let data = {
                target: target
            };

            if (target === 'new') {
                data.new_email = newEmailInputVal;
                otpMessage.innerHTML = 'Tahap 2/2: Sedang mengirimkan kode ke email BARU Anda...';
            } else {
                otpMessage.innerHTML = (isChangingEmail ? 'Tahap 1/2: ' : '') +
                    'Sedang mengirimkan kode keamanan ke email LAMA Anda...';
            }

            fetch("{{ route('profile.sendOtp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            }).then(r => r.json()).then(res => {
                if (res.success) {
                    btnVerifikasi.innerHTML = 'Verifikasi';
                    btnVerifikasi.disabled = false;
                    otpMessage.innerHTML = res.message;

                    Swal.fire({
                        icon: 'info',
                        title: target === 'new' ? 'Cek Email Baru' : 'Cek Email Lama',
                        text: res.message,
                        confirmButtonColor: '#3085d6',
                    });
                } else {
                    closeOtpModal();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message
                    });
                }
            }).catch(err => {
                closeOtpModal();
                Swal.fire({
                    icon: 'error',
                    title: 'Error Jaringan',
                    text: 'Gagal terhubung ke server.'
                });
            });
        }

        function closeOtpModal() {
            otpModal.classList.add('hidden');
            pendingForm = null;
        }

        function submitOtp() {
            let otp = otpInput.value;
            if (otp.length < 6) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Masukkan 6 digit OTP.'
                });
                return;
            }

            btnVerifikasi.innerHTML = 'Mengecek...';
            btnVerifikasi.disabled = true;

            let target = (isChangingEmail && currentOtpStep === 2) ? 'new' : 'old';

            fetch("{{ route('profile.verifyOtp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    otp: otp,
                    target: target
                })
            }).then(r => r.json()).then(res => {
                if (res.success) {
                    if (isChangingEmail && currentOtpStep === 1) {
                        // Verifikasi tahap 1 berhasil, lanjut ke tahap 2 (Email Baru)
                        currentOtpStep = 2;
                        Swal.fire({
                            icon: 'success',
                            title: 'Email Lama Terverifikasi',
                            text: 'Memproses pengiriman ke email baru...',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            triggerSendOtp
                        (); // Panggil fungsi kirim OTP lagi, sekarang otomatis target="new"
                        });
                    } else {
                        // Selesai! (Bisa karena ganti password saja, atau sudah melalui step 2 ganti email)
                        otpModal.classList.add('hidden');
                        Swal.fire({
                            icon: 'success',
                            title: 'Verifikasi Tuntas!',
                            text: 'Menyimpan perubahan data Anda...',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            pendingForm.submit();
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Ditolak!',
                        text: res.message
                    });
                    btnVerifikasi.innerHTML = 'Verifikasi';
                    btnVerifikasi.disabled = false;
                    otpInput.value = '';
                }
            }).catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Gagal memverifikasi OTP.'
                });
                btnVerifikasi.innerHTML = 'Verifikasi';
                btnVerifikasi.disabled = false;
            });
        }
    </script>
</x-app-layout>
