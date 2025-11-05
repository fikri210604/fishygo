<x-guest-layout>
    <!-- Status Sesi -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->has('google'))
        <div class="mb-4 text-sm text-red-600">
            {{ $errors->first('google') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Alamat Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Kata Sandi -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Ingat Saya -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6">
        <div class="flex items-center">
            <div class="flex-grow border-t border-gray-200"></div>
            <span class="mx-3 text-gray-500 text-sm">atau</span>
            <div class="flex-grow border-t border-gray-200"></div>
        </div>
        <a href="{{ route('auth.google.redirect') }}"
           class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            {{-- Simple Google icon using SVG --}}
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-5 w-5" aria-hidden="true">
                <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 31.7 29.3 35 24 35c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.7 1.1 7.8 3l5.7-5.7C33.7 5.1 29.1 3 24 3 12.9 3 4 11.9 4 23s8.9 20 20 20 19-8.9 19-20c0-1.3-.1-2.2-.4-3.5z"/>
                <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 16.1 18.9 13 24 13c3 0 5.7 1.1 7.8 3l5.7-5.7C33.7 5.1 29.1 3 24 3 16.1 3 9.2 7.1 6.3 14.7z"/>
                <path fill="#4CAF50" d="M24 43c5.2 0 9.9-1.9 13.4-5.1l-6.2-5.1C29 34.8 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-7.9l-6.5 5C9.1 39.1 16 43 24 43z"/>
                <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.1 3.2-3.4 5.7-6.1 7.4l6.2 5.1c-3.8 3.5-8.8 5.5-14.4 5.5-7.9 0-14.8-3.9-18.9-9.9l6.5-5C9.4 32.7 13.8 36 19 36c2.7 0 5-.8 6.9-2.2 1.9-1.4 3.3-3.3 4.2-5.5.4-1.1.7-2.3.7-3.5 0-1.2-.2-2.4-.5-3.5z"/>
            </svg>
            <span>Masuk dengan Google</span>
        </a>
    </div>
</x-guest-layout>
