<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="nama" :value="__('Nama')" />
                <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full text-gray-900" :value="old('nama', $user->nama)" required autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('nama')" />
            </div>
            <div>
                <x-input-label for="username" :value="__('Username')" />
                <x-text-input id="username" name="username" type="text" class="mt-1 block w-full text-gray-900" :value="old('username', $user->username)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('username')" />
            </div>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full text-gray-900" :value="old('email', $user->email)" required autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="address" :value="__('Alamat Lengkap (Jalan, RT/RW, No Rumah)')" />
            <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-900">{{ old('address', $user->address) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="phone" :value="__('Nomor HP')" />
                <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full text-gray-900" :value="old('phone', $user->phone)" autocomplete="tel" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>
            <div>
                <x-input-label for="kode_pos" :value="__('Kode Pos')" />
                <x-text-input id="kode_pos" name="kode_pos" type="text" class="mt-1 block w-full text-gray-900" :value="old('kode_pos', optional($primaryAddress ?? null)->kode_pos)" />
                <x-input-error class="mt-2" :messages="$errors->get('kode_pos')" />
            </div>
        </div>

        <div>
            <x-input-label for="avatar" :value="__('Avatar')" />
            @if ($user->avatar)
                <div class="mb-2">
                    <img src="{{ asset('storage/'.$user->avatar) }}" alt="avatar" class="h-16 w-16 rounded-full object-cover">
                </div>
            @endif
            <input id="avatar" name="avatar" type="file" accept="image/*" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <!-- Wilayah: Provinsi, Kabupaten/Kota, Kecamatan, Kelurahan/Desa -->
        <div class="grid grid-cols-1 gap-4 mt-4">
            <div>
                <x-input-label for="province_select" :value="__('Provinsi')" />
                <div class="mt-1 flex items-center gap-2">
                    <select id="province_select" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900">
                        <option value="">-- Pilih Provinsi --</option>
                    </select>
                    <span id="province_spinner" class="hidden inline-flex">
                        <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </span>
                </div>
                <input type="hidden" id="province_id" name="province_id" value="{{ old('province_id', optional($primaryAddress ?? null)->province_id) }}">
                <input type="hidden" id="province_name" name="province_name" value="{{ old('province_name', optional($primaryAddress ?? null)->province_name) }}">
                <x-input-error class="mt-2" :messages="$errors->get('province_id')" />
            </div>

            <div>
                <x-input-label for="regency_select" :value="__('Kabupaten/Kota')" />
                <div class="mt-1 flex items-center gap-2">
                    <select id="regency_select" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900" disabled>
                        <option value="">-- Pilih Kabupaten/Kota --</option>
                    </select>
                    <span id="regency_spinner" class="hidden inline-flex">
                        <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </span>
                </div>
                <input type="hidden" id="regency_id" name="regency_id" value="{{ old('regency_id', optional($primaryAddress ?? null)->regency_id) }}">
                <input type="hidden" id="regency_name" name="regency_name" value="{{ old('regency_name', optional($primaryAddress ?? null)->regency_name) }}">
                <x-input-error class="mt-2" :messages="$errors->get('regency_id')" />
            </div>

            <div>
                <x-input-label for="district_select" :value="__('Kecamatan')" />
                <div class="mt-1 flex items-center gap-2">
                    <select id="district_select" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900" disabled>
                        <option value="">-- Pilih Kecamatan --</option>
                    </select>
                    <span id="district_spinner" class="hidden inline-flex">
                        <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </span>
                </div>
                <input type="hidden" id="district_id" name="district_id" value="{{ old('district_id', optional($primaryAddress ?? null)->district_id) }}">
                <input type="hidden" id="district_name" name="district_name" value="{{ old('district_name', optional($primaryAddress ?? null)->district_name) }}">
                <x-input-error class="mt-2" :messages="$errors->get('district_id')" />
            </div>

            <div>
                <x-input-label for="village_select" :value="__('Kelurahan/Desa')" />
                <div class="mt-1 flex items-center gap-2">
                    <select id="village_select" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-900" disabled>
                        <option value="">-- Pilih Kelurahan/Desa --</option>
                    </select>
                    <span id="village_spinner" class="hidden inline-flex">
                        <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </span>
                </div>
                <input type="hidden" id="village_id" name="village_id" value="{{ old('village_id', optional($primaryAddress ?? null)->village_id) }}">
                <input type="hidden" id="village_name" name="village_name" value="{{ old('village_name', optional($primaryAddress ?? null)->village_name) }}">
                <x-input-error class="mt-2" :messages="$errors->get('village_id')" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="rt" :value="__('RT')" />
                    <x-text-input id="rt" name="rt" type="text" class="mt-1 block w-full text-gray-900" :value="old('rt', optional($primaryAddress ?? null)->rt)" />
                    <x-input-error class="mt-2" :messages="$errors->get('rt')" />
                </div>
                <div>
                    <x-input-label for="rw" :value="__('RW')" />
                    <x-text-input id="rw" name="rw" type="text" class="mt-1 block w-full text-gray-900" :value="old('rw', optional($primaryAddress ?? null)->rw)" />
                    <x-input-error class="mt-2" :messages="$errors->get('rw')" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
