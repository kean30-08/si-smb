<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Ubah Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.') }}
        </p>
    </header>

    <form id="passwordForm" method="post" action="{{ route('password.update') }}" onsubmit="handlePasswordSubmit(event)"
        class="mt-6 space-y-6">
        @csrf
        @method('put')

        {{-- PASSWORD SAAT INI --}}
        <div>
            <x-input-label for="update_password_current_password" :value="__('Password Saat Ini')" />
            <div class="relative mt-1">
                <x-text-input id="update_password_current_password" name="current_password" type="password"
                    class="block w-full pr-10" autocomplete="current-password" />
                <button type="button" onclick="togglePassword('update_password_current_password', 'icon-current')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg id="icon-current" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        {{-- PASSWORD BARU --}}
        <div>
            <x-input-label for="update_password_password" :value="__('Password Baru')" />
            <div class="relative mt-1">
                <x-text-input id="update_password_password" name="password" type="password" class="block w-full pr-10"
                    autocomplete="new-password" />
                <button type="button" onclick="togglePassword('update_password_password', 'icon-new')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg id="icon-new" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        {{-- KONFIRMASI PASSWORD BARU --}}
        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password Baru')" />
            <div class="relative mt-1">
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    class="block w-full pr-10" autocomplete="new-password" />
                <button type="button" onclick="togglePassword('update_password_password_confirmation', 'icon-confirm')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg id="icon-confirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-bold">{{ __('Password berhasil diubah.') }}</p>
            @endif
        </div>
    </form>

    {{-- SCRIPT UNTUK FITUR SHOW/HIDE PASSWORD --}}
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            // SVG Mata Terbuka (Show)
            const iconEye =
                `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;

            // SVG Mata Dicoret (Hide)
            const iconEyeSlash =
                `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />`;

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = iconEyeSlash; // Ubah ke mata dicoret
            } else {
                input.type = 'password';
                icon.innerHTML = iconEye; // Ubah ke mata terbuka
            }
        }
    </script>
</section>
