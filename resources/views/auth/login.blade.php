<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row">
        <div class="hidden lg:flex w-1/2 bg-cover bg-center"
            style="background-image: url('{{ asset('assets/images/background.png') }}');">
        </div>
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 md:px-12 py-10">
            <div class="w-full max-w-md">
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold text-primary mb-2">Login ke FishyGo!</h1>
                    <img src="{{ asset('assets/images/logo.png') }}" alt="FishyGo" class="h-12 mx-auto" loading="lazy">
                </div>
                @if (session('verified'))
                    <div class="mb-4 p-3 rounded bg-green-50 text-green-700 border border-green-200">
                        Email kamu berhasil diverifikasi. Silakan login.
                    </div>
                @endif
                <x-auth-session-status class="mb-4" :status="session('status')" />

                @if ($errors->has('google'))
                    <div class="mb-4 w-full badge-error">
                        {{ $errors->first('google') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" onsubmit="globalButtonLoading(event)">
                    @csrf
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus
                            autocomplete="username" class="block mt-1 w-full bg-gray-100 border-gray-300 rounded-md"
                            placeholder="example@gmail.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" />

                        <div class="relative">
                            <x-text-input id="password" type="password" name="password" required
                                autocomplete="current-password"
                                class="block mt-1 w-full bg-gray-100 border-gray-300 rounded-md pr-10"
                                placeholder="**********" />

                            <span onclick="togglePassword()"
                                class="absolute right-3 top-3 cursor-pointer text-gray-500">
                                👁️
                            </span>
                        </div>

                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    {{-- INGAT SAYA + LUPA PASSWORD --}}
                    <div class="flex justify-between items-center mt-3 text-sm">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="checkbox checkbox-sm checkbox-primary" />
                            <span>Ingat Saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-primary font-medium hover:underline">
                                Lupa Password?
                            </a>
                        @endif
                    </div>

                    {{-- TOMBOL LOGIN --}}
                    <button type="submit" id="loginBtn" class="w-full mt-5 btn-primary">
                        <span class="btn-text">Masuk</span>
                        <span class="loader hidden items-center gap-2 text-sm">
                            <span class="loading dots-loading loading-md"></span>
                            <span>Memuat...</span>
                        </span>
                    </button>
                </form>

                {{-- LINK REGISTER --}}
                <p class="text-center text-sm mt-3">
                    Belum Punya Akun?
                    <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline">
                        Daftar
                    </a>
                </p>

                {{-- PEMISAH --}}
                <div class="flex items-center my-6">
                    <div class="border-t flex-grow"></div>
                    <span class="px-3 text-gray-400">Atau</span>
                    <div class="border-t flex-grow"></div>
                </div>

                {{-- TOMBOL GOOGLE --}}
                <a href="{{ route('auth.google.redirect') }}"
                    class="btn-outline w-full border flex items-center justify-center gap-3 py-2 text-gray-700 text-base">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="h-5 w-5" />
                    Lanjut dengan Google
                </a>

            </div>

        </div>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById("password");
            pwd.type = pwd.type === "password" ? "text" : "password";
        }

        window.hasFormError = @json($errors->any());

    </script>


</x-guest-layout>
