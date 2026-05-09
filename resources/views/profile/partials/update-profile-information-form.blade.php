<section>
    @php
        $isAdmin = auth()->user()->isAdmin();
    @endphp

    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informasi Profil') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Perbarui informasi profil dan detail kontak Anda.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- TAMBAHKAN ID "profileForm" dan event onsubmit --}}
    <form id="profileForm" method="post" action="{{ route('profile.update') }}" onsubmit="handleProfileSubmit(event)"
        class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" name="name" type="text"
                class="mt-1 block w-full bg-gray-100 text-gray-500 cursor-not-allowed" :value="old('name', $user->name)" required
                readonly />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email Login')" />
            {{-- HAPUS readonly DAN cursor-not-allowed AGAR EMAIL BISA DIUBAH --}}
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)"
                required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        @if (!$isAdmin)
            @php
                $dataPengajar = \App\Models\Pengajar::where('user_id', auth()->id())->first();
                $jkAsli = old('jenis_kelamin', $dataPengajar->jenis_kelamin ?? '');
                $teksJk = $jkAsli == 'L' ? 'Laki-laki' : ($jkAsli == 'P' ? 'Perempuan' : '');
            @endphp

            <div>
                <x-input-label for="nomor_hp" :value="__('Nomor HP / WA')" />
                <x-text-input id="nomor_hp" name="nomor_hp" type="text"
                    class="mt-1 block w-full bg-gray-100 text-gray-500 cursor-not-allowed" :value="old('nomor_hp', $dataPengajar->nomor_hp ?? '')" readonly />
            </div>

            <div>
                <x-input-label for="jenis_kelamin_display" :value="__('Jenis Kelamin')" />
                <x-text-input id="jenis_kelamin_display" type="text"
                    class="mt-1 block w-full bg-gray-100 text-gray-500 cursor-not-allowed" :value="$teksJk" readonly />
                <input type="hidden" name="jenis_kelamin" value="{{ $jkAsli }}">
            </div>

            <div class="md:col-span-2">
                <x-input-label for="alamat" :value="__('Alamat Lengkap')" />
                <textarea id="alamat" name="alamat" rows="3"
                    class="mt-1 block w-full bg-gray-100 text-gray-500 cursor-not-allowed border-gray-300 rounded-md shadow-sm"
                    readonly>{{ old('alamat', $dataPengajar->alamat ?? '') }}</textarea>
            </div>
        @endif

        {{-- AKTIFKAN KEMBALI TOMBOL SAVE --}}
        <div class="flex items-center gap-4 md:col-span-2 mt-2">
            <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-bold">
                    {{ __('Data berhasil disimpan.') }}
                </p>
            @endif
        </div>
    </form>
</section>
