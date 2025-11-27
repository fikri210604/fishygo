@section('title', 'Daftar Akun - FishyGo')


<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row">

        {{-- BAGIAN KIRI GAMBAR --}}
        <div class="hidden lg:flex w-1/2 bg-cover bg-center"
            style="background-image: url('{{ asset('assets/images/background.png') }}');">
        </div>

        {{-- BAGIAN KANAN FORM --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 md:px-12 py-10">

            {{-- STEP 1: Hanya Email untuk kirim link verifikasi --}}
            <form method="POST" action="{{ route('register.start') }}" class="w-full max-w-md">
                @csrf

                {{-- TITLE --}}
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold text-primary mb-2">Bergabung ke FishyGo!</h1>
                    <img src="{{ asset('assets/images/logo.png') }}" class="h-12 mx-auto" decoding="async" fetchpriority="high" height="48" />
                    <p class="text-sm text-gray-500 mt-2">Masukkan email Anda untuk menerima link verifikasi.</p>
                </div>

                <div class="mb-4">
                    <x-input-label value="Email" />
                    <x-text-input type="email" name="email" class="w-full bg-gray-100" required
                        placeholder="example@gmail.com" value="{{ old('email') }}" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <button type="submit" class="btn btn-primary w-full mt-5">
                    Kirim Link Verifikasi
                </button>

                <p class="text-center text-sm mt-4">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">Login</a>
                </p>
            </form>
        </div>
    </div>
</x-guest-layout>
