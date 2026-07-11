<section>
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

    <form id="profileForm" method="post" action="{{ route('profile.update') }}" onsubmit="handleProfileSubmit(event)"
        class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            {{-- Hapus atribut readonly dan class cursor-not-allowed --}}
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email Login')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)"
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        @php
            $dataPengajar = \App\Models\Pengajar::where('user_id', auth()->id())->first();
            $jkAsli = old('jenis_kelamin', $dataPengajar->jenis_kelamin ?? '');
            $teksJk = $jkAsli == 'L' ? 'Laki-laki' : ($jkAsli == 'P' ? 'Perempuan' : '');
        @endphp

        <div>
            <x-input-label for="nomor_hp" :value="__('Nomor HP / WA')" />
            {{-- Hapus atribut readonly dan class cursor-not-allowed --}}
            <x-text-input id="nomor_hp" name="nomor_hp" type="text" class="mt-1 block w-full" :value="old('nomor_hp', $dataPengajar->nomor_hp ?? '')"
                required />
            <x-input-error class="mt-2" :messages="$errors->get('nomor_hp')" />
        </div>

        <div>
            <x-input-label for="jenis_kelamin_display" :value="__('Jenis Kelamin')" />
            {{-- Jenis Kelamin tetap tidak bisa diubah (readonly) --}}
            <x-text-input id="jenis_kelamin_display" type="text"
                class="mt-1 block w-full bg-gray-100 text-gray-500 cursor-not-allowed" :value="$teksJk" readonly />
            <input type="hidden" name="jenis_kelamin" value="{{ $jkAsli }}">
        </div>

        <div class="md:col-span-2">
            <x-input-label for="alamat" :value="__('Alamat Lengkap')" />
            {{-- Hapus atribut readonly dan class cursor-not-allowed --}}
            <textarea id="alamat" name="alamat" rows="3"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required>{{ old('alamat', $dataPengajar->alamat ?? '') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
        </div>

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
