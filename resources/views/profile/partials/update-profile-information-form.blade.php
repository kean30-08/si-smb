<section>
    @php
        // Definisikan dulu siapa itu Admin
        $isAdmin = !\App\Models\Pengajar::where('user_id', auth()->id())->exists();
    @endphp

    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informasi Profil') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Perbarui informasi profil dan detail kontak Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- KELAS GRID DITERAPKAN DI SINI --}}
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf
        @method('patch')

        {{-- KOTAK 1 (Akan otomatis di Kiri) --}}
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-gray-100 text-gray-500 cursor-not-allowed" :value="old('name', $user->name)" required readonly/>
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- KOTAK 2 (Akan otomatis di Kanan) --}}
        <div>
            <x-input-label for="email" :value="__('Email Login')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-gray-100 text-gray-500 cursor-not-allowed" :value="old('email', $user->email)" required readonly />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        {{-- DATA TAMBAHAN KHUSUS PENGAJAR --}}
        @if(!$isAdmin)
            @php
                $dataPengajar = \App\Models\Pengajar::where('user_id', auth()->id())->first();
            @endphp

            {{-- KOTAK 3 (Otomatis turun ke baris baru, letaknya di Kiri) --}}
            <div>
                <x-input-label for="nomor_hp" :value="__('Nomor HP / WA')" />
                <x-text-input id="nomor_hp" name="nomor_hp" type="text" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" :value="old('nomor_hp', $dataPengajar->nomor_hp ?? '')" />
                <x-input-error class="mt-2" :messages="$errors->get('nomor_hp')" />
            </div>

            {{-- KOTAK 4 (Otomatis di Kanan, bersebelahan dengan No HP) --}}
            <div>
                <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')" />
                <select id="jenis_kelamin" name="jenis_kelamin" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="L" {{ old('jenis_kelamin', $dataPengajar->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $dataPengajar->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('jenis_kelamin')" />
            </div>

            {{-- KOTAK 5 (ALAMAT). Kita gunakan md:col-span-2 agar memanjang full --}}
            <div class="md:col-span-2">
                <x-input-label for="alamat" :value="__('Alamat Lengkap')" />
                <textarea id="alamat" name="alamat" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alamat', $dataPengajar->alamat ?? '') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
            </div>
        @endif

        {{-- AREA TOMBOL SIMPAN. Kita rentangkan juga 2 kolom --}}
        <div class="flex items-center gap-4 md:col-span-2 mt-2">
            <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-bold"
                >{{ __('Data berhasil disimpan.') }}</p>
            @endif
        </div>
    </form>
</section>