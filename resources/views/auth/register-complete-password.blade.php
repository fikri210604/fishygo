@section('title', 'Buat Password - FishyGo')

<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- BAGIAN KIRI GAMBAR -->
        <div class="hidden lg:flex w-1/2 bg-cover bg-center"
             style="background-image: url('{{ asset('assets/images/background.png') }}');">
        </div>

        <!-- BAGIAN KANAN FORM -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 md:px-12 py-10">
            <div class="w-full max-w-md">
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-primary mb-2">Buat Password</h1>
                    <p class="text-gray-600">Untuk akun: <span class="font-semibold">{{ $email }}</span></p>
                </div>

                <form method="POST" action="{{ route('register.complete') }}" class="card bg-white shadow-lg p-6 space-y-4">
                    @csrf
                    <div>
                        <x-input-label value="Password" />
                        <x-text-input type="password" name="password" class="w-full bg-gray-100" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Konfirmasi Password" />
                        <x-text-input type="password" name="password_confirmation" class="w-full bg-gray-100" required />
                    </div>
                    <button type="submit" class="btn btn-primary w-full">Daftar & Masuk</button>
                </form>

                <div class="text-center text-sm mt-4">
                    <a href="{{ route('register.complete.profile') }}" class="text-gray-600 hover:underline">Kembali ke Profil</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
