<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row">

        <!-- BAGIAN KIRI (Brand/Background) -->
        <div class="hidden lg:flex w-1/2 bg-cover bg-center"
            style="background-image: url('{{ asset('assets/images/background.png') }}');"></div>

        <!-- BAGIAN KANAN (Form) -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 md:px-12 py-10">
            <div class="w-full max-w-md">

                <!-- TITLE -->
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-extrabold tracking-tight text-primary">
                        Atur Ulang Password
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">
                        Silakan masukkan password baru untuk akun kamu
                    </p>
                </div>

                <!-- LOGO -->
                <div class="mt-8 text-center">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="FishyGo"
                        class="h-10 mx-auto opacity-80">
                </div>
                <!-- CARD -->
                <div class="bg-white shadow-md border border-gray-200 rounded-xl p-6">
                    <form method="POST" action="{{ route('password.store') }}" onsubmit="handleSubmitReset(event)">
                        @csrf

                        <!-- TOKEN -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- EMAIL -->
                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" type="email" name="email"
                                :value="old('email', $request->email)" required autofocus autocomplete="username"
                                class="block mt-1 w-full bg-gray-100 border-gray-300 rounded-md"
                                placeholder="nama@email.com" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>

                        <!-- PASSWORD BARU -->
                        <div class="mb-4">
                            <x-input-label for="password" :value="__('Kata Sandi Baru')" />
                            <x-text-input id="password" type="password" name="password" required
                                autocomplete="new-password"
                                class="block mt-1 w-full bg-gray-100 border-gray-300 rounded-md" />
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>

                        <!-- KONFIRMASI PASSWORD -->
                        <div class="mb-4">
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
                            <x-text-input id="password_confirmation" type="password"
                                name="password_confirmation" required autocomplete="new-password"
                                class="block mt-1 w-full bg-gray-100 border-gray-300 rounded-md" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="mt-6 flex flex-col gap-3">
                            <button type="submit" id="doResetBtn"
                                class="btn btn-primary w-full flex items-center justify-center gap-2">
                                <span class="btn-text">Atur Ulang Password</span>
                                <span class="loader hidden items-center gap-2">
                                    <span class="loading loading-dots loading-sm"></span>
                                    <span>Memproses...</span>
                                </span>
                            </button>

                            <a href="{{ route('login') }}" class="btn btn-ghost w-full text-center">
                                Kembali ke Login
                            </a>
                        </div>
                    </form>

                    <!-- TIPS -->
                    <p class="mt-5 text-xs text-gray-500 leading-relaxed text-center">
                        Gunakan minimal 8 karakter, campuran huruf besar, angka, dan simbol untuk keamanan maksimal.
                    </p>
                </div>   

            </div>
        </div>

    </div>

    <script>
        function handleSubmitReset(e) {
            const btn = document.getElementById('doResetBtn');
            const btnText = btn.querySelector('.btn-text');
            const loader = btn.querySelector('.loader');

            btn.disabled = true;
            btnText.classList.add('hidden');
            loader.classList.remove('hidden');
        }

        @if ($errors->any())
            window.addEventListener('DOMContentLoaded', function () {
                const btn = document.getElementById('doResetBtn');
                const btnText = btn.querySelector('.btn-text');
                const loader = btn.querySelector('.loader');

                btn.disabled = false;
                btnText.classList.remove('hidden');
                loader.classList.add('hidden');
            });
        @endif
    </script>
</x-guest-layout>
