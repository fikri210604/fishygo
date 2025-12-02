<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row">
        <div class="hidden lg:flex w-1/2 bg-cover bg-center"
            style="background-image: url('{{ asset('assets/images/background.png') }}');">
        </div>
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 md:px-6 py-6">
            <div class="w-full max-w-md">

                <!-- LOGO -->
                <div class="mt-8 text-center">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="FishyGo"
                        class="h-10 mx-auto opacity-80">
                </div>

                <div class="text-center mb-6">
                    <h1 class="text-3xl font-extrabold tracking-tight text-primary">Lupa Password</h1>
                    <p class="text-gray-500 text-sm mt-1">
                        Kami akan mengirimkan tautan untuk mereset password kamu.
                    </p>
                </div>
                @if (session('status'))
                    <div class="alert alert-success text-sm">
                        {{ session('status') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('password.email') }}" onsubmit="handleResetSubmit(event)">
                    @csrf
                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus
                            autocomplete="username"
                            class="block mt-1 w-full bg-gray-100 border-gray-300 rounded-md"
                            placeholder="nama@email.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>
                    <div class="mt-6 flex flex-col gap-3">
                        <button type="submit" id="resetBtn"
                            class="btn btn-primary w-full flex items-center justify-center gap-2">
                            <span class="btn-text">Kirim Tautan Reset</span>
                            <span class="loader hidden items-center gap-2">
                                <span class="loading dots-loading loading-md"></span>
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
                    Tips: Jika email tidak masuk, tunggu 1–2 menit lalu periksa folder Spam/Promotions.
                    Pastikan alamat email benar.
                </p>

                
            </div>
        </div>

    </div>
</x-guest-layout>

<script>
    function handleResetSubmit(e) {
        const btn = document.getElementById('resetBtn');
        const text = btn.querySelector('.btn-text');
        const loader = btn.querySelector('.loader');

        text.classList.add('hidden');
        loader.classList.remove('hidden');
    }
</script>
