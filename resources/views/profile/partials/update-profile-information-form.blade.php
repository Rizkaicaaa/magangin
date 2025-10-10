<section>
    <header class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
            <i class="fas fa-user-circle text-blue-600 text-2xl"></i>
            {{ __('Informasi Profil') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Perbarui informasi profil dan alamat email akun Anda untuk memastikan data tetap akurat.') }}
        </p>
    </header>

    <!-- Form Verifikasi Email -->
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <!-- Form Update Profil -->
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Nama -->
        <x-input-label for="nama_lengkap" :value="__('Nama Lengkap')" />
        <x-text-input id="nama_lengkap" name="nama_lengkap" type="text"
            class="mt-2 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm"
            :value="old('nama_lengkap', $user->nama_lengkap)" required />
        <x-input-error class="mt-2" :messages="$errors->get('nama_lengkap')" />


        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" name="email" type="email"
                class="mt-2 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

        </div>

        <!-- Tombol Simpan -->
        <div class="flex items-center gap-4">
            <x-primary-button class="!bg-blue-600 hover:!bg-blue-700 focus:!ring-blue-500">
                <i class="fas fa-save mr-2"></i> {{ __('Simpan Perubahan') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-green-600 font-medium">
                {{ __('Berhasil disimpan.') }}
            </p>
            @endif
        </div>
    </form>
</section>