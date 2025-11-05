<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Nama -->
        <div>
            <x-input-label for="nama" :value="__('Nama')" />
            <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
        </div>

        <!-- Username -->
        <div class="mt-4">
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Avatar -->
        <div class="mt-4">
            <x-input-label for="avatar" :value="__('Avatar (opsional)')" />
            <input id="avatar" name="avatar" type="file" accept="image/*" class="block mt-1 w-full" />
            <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
        </div>

        <!-- Province (Wilayah.id API) -->
        <div class="mt-4">
            <x-input-label for="province_select" :value="__('Provinsi')" />
            <div class="mt-1 flex items-center gap-2">
                <select id="province_select" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Pilih Provinsi --</option>
                </select>
                <span id="province_spinner" class="hidden inline-flex">
                    <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </span>
            </div>
            <input type="hidden" id="province_id" name="province_id" value="{{ old('province_id') }}">
            <input type="hidden" id="province_name" name="province_name" value="{{ old('province_name') }}">
            <x-input-error :messages="$errors->get('province_id')" class="mt-2" />
        </div>

        <!-- Regency (Kabupaten/Kota) -->
        <div class="mt-4">
            <x-input-label for="regency_select" :value="__('Kabupaten/Kota')" />
            <div class="mt-1 flex items-center gap-2">
                <select id="regency_select" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" disabled>
                    <option value="">-- Pilih Kabupaten/Kota --</option>
                </select>
                <span id="regency_spinner" class="hidden inline-flex">
                    <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </span>
            </div>
            <input type="hidden" id="regency_id" name="regency_id" value="{{ old('regency_id') }}">
            <input type="hidden" id="regency_name" name="regency_name" value="{{ old('regency_name') }}">
            <x-input-error :messages="$errors->get('regency_id')" class="mt-2" />
        </div>

        <!-- District (Kecamatan) -->
        <div class="mt-4">
            <x-input-label for="district_select" :value="__('Kecamatan')" />
            <div class="mt-1 flex items-center gap-2">
                <select id="district_select" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" disabled>
                    <option value="">-- Pilih Kecamatan --</option>
                </select>
                <span id="district_spinner" class="hidden inline-flex">
                    <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </span>
            </div>
            <input type="hidden" id="district_id" name="district_id" value="{{ old('district_id') }}">
            <input type="hidden" id="district_name" name="district_name" value="{{ old('district_name') }}">
            <x-input-error :messages="$errors->get('district_id')" class="mt-2" />
        </div>

        <!-- Village (Kelurahan/Desa) -->
        <div class="mt-4">
            <x-input-label for="village_select" :value="__('Kelurahan/Desa')" />
            <div class="mt-1 flex items-center gap-2">
                <select id="village_select" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" disabled>
                    <option value="">-- Pilih Kelurahan/Desa --</option>
                </select>
                <span id="village_spinner" class="hidden inline-flex">
                    <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </span>
            </div>
            <input type="hidden" id="village_id" name="village_id" value="{{ old('village_id') }}">
            <input type="hidden" id="village_name" name="village_name" value="{{ old('village_name') }}">
            <x-input-error :messages="$errors->get('village_id')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>
