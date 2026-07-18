<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full placeholder:italic" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" placeholder="Masukkan Email..." />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" />

            <x-text-input id="password" class="block mt-1 w-full placeholder:italic" type="password" name="password" required
                autocomplete="current-password" placeholder="Masukkan Kata Sandi..." />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />

            {{-- Link Berita dihapus dari sini --}}
        </div>

        {{-- Kita ubah justify-end menjadi justify-between agar Berita bisa di kiri, dan yang lain di kanan --}}
        <div class="flex items-center justify-between mt-4">

            <a href="{{ route('pemberitahuan.index') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-200 border border-transparent rounded-md font-semibold text-xs text-blue-700 uppercase tracking-widest hover:bg-blue-300 transition">
                Berita
            </a>

            <div class="flex items-center">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Ingat Saya') }}</span>
                </label>

                <x-primary-button class="ms-3">
                    {{ __('Masuk') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</x-guest-layout>
