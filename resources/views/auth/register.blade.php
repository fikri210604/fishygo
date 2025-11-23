<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row">

        {{-- BAGIAN KIRI GAMBAR --}}
        <div class="hidden lg:flex w-1/2 bg-cover bg-center"
            style="background-image: url('{{ asset('assets/images/background.png') }}');">
        </div>

        {{-- BAGIAN KANAN FORM --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 md:px-12 py-10">
            <form method="POST" action="{{ route('register.start') }}" class="w-full max-w-md">
                @csrf

                {{-- TITLE --}}
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold text-primary mb-2">Bergabung ke FishyGo!</h1>
                    <img src="{{ asset('assets/images/logo.png') }}" class="h-12 mx-auto" decoding="async" fetchpriority="high" height="48" />
                </div>

                {{-- PROGRESS BAR (2 langkah)
                <div class="w-full flex flex-col items-center mb-8 select-none">
                    <div class="relative w-full max-w-md">
                        <div class="absolute top-1/2 -translate-y-1/2 w-full h-1 bg-gray-300 rounded-full"></div>
                        <div class="absolute top-1/2 -translate-y-1/2 h-1 bg-primary rounded-full transition-all duration-500"
                            style="width:50%"></div>
                        <div class="relative flex justify-between">
                            <span class="w-4 h-4 rounded-full bg-primary"></span>
                            <span class="w-4 h-4 rounded-full bg-gray-300"></span>
                            <span class="w-4 h-4 rounded-full bg-gray-300"></span>
                        </div>
                    </div>
                    <div class="flex justify-between w-full max-w-md text-xs mt-3 text-center">
                        <span class="font-semibold text-primary">Akun</span>
                        <span class="text-gray-400">Informasi Pribadi</span>
                        <span class="text-gray-400">Selesai</span>

                    </div>
                </div> --}}

                {{-- STEP 1 --}}
                <div x-ref="step1">

                    <div class="mb-4">
                        <x-input-label value="Username" />
                        <x-text-input name="username" class="w-full bg-gray-100" required placeholder="myusername"
                            value="{{ old('username') }}" />
                        <x-input-error :messages="$errors->get('username')" class="mt-1" />
                    </div>

                    <div class="mb-4">
                        <x-input-label value="Email" />
                        <x-text-input type="email" name="email" class="w-full bg-gray-100" required
                            placeholder="example@gmail.com" value="{{ old('email') }}" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div class="mb-4">
                        <x-input-label value="Password" />
                        <x-text-input type="password" name="password" class="w-full bg-gray-100" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="mb-4">
                        <x-input-label value="Konfirmasi Password" />
                        <x-text-input type="password" name="password_confirmation" class="w-full bg-gray-100"
                            required />
                    </div>

                    <button type="submit" id="nextBtn" class="btn btn-primary w-full mt-5">
                        Lanjut
                    </button>


                </div>

                <p class="text-center text-sm mt-4">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">Login</a>
                </p>

            </form>

        </div>
    </div>

</x-guest-layout>
