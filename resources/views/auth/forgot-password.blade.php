<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row">

        {{-- BAGIAN KIRI - BACKGROUND PATTERN --}}
        <div class="hidden lg:flex w-1/2 bg-cover bg-center"
            style="background-image: url('{{ asset('assets/images/background.png') }}');">
        </div>

        {{-- BAGIAN KANAN - FORM RESET --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 md:px-12 py-10">

            <div class="w-full max-w-md">

                {{-- TITLE + LOGO --}}
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold text-primary mb-2">Reset Password</h1>
                    <img src="{{ asset('assets/images/logo.png') }}" alt="FishyGo" class="h-12 mx-auto">
                </div>

                {{-- Penjelasan --}}
                <p class="text-gray-600 text-sm text-center mb-4 leading-relaxed">
                    Masukkan alamat email Anda dan kami akan mengirimkan tautan
                    untuk mengatur ulang kata sandi.
                </p>

                {{-- STATUS SESSION --}}
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" onsubmit="handleResetSubmit(event)">
                    @csrf

                    {{-- EMAIL --}}
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" type="email" name="email"
                            :value="old('email')" required autofocus
                            class="block mt-1 w-full bg-gray-100 border-gray-300 rounded-md"
                            placeholder="example@gmail.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    {{-- TOMBOL KIRIM --}}
                    <button type="submit" id="resetBtn" class="w-full mt-5 btn btn-primary flex justify-center items-center gap-2">
                        <span class="btn-text">Kirim Tautan Reset</span>

                        {{-- Loader --}}
                        <span class="loader hidden items-center gap-2 text-sm">
                            <span class="dots-loading"></span>
                            <span>Memuat...</span>
                        </span>
                    </button>
                </form>

                {{-- Kembali ke login --}}
                <p class="text-center text-sm mt-4">
                    <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">
                        Kembali ke Login
                    </a>
                </p>

            </div>

        </div>
    </div>

    <script>
        function handleResetSubmit(event) {
            const btn = document.getElementById('resetBtn');
            const btnText = btn.querySelector('.btn-text');
            const loader = btn.querySelector('.loader');

            btn.disabled = true;
            btnText.classList.add('hidden');
            loader.classList.remove('hidden');
        }

        @if ($errors->any())
        window.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('resetBtn');
            const btnText = btn.querySelector('.btn-text');
            const loader = btn.querySelector('.loader');

            btn.disabled = false;
            btnText.classList.remove('hidden');
            loader.classList.add('hidden');
        });
        @endif
    </script>
</x-guest-layout>
