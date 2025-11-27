@section('title', 'Verifikasi Email')

@endsection

<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row">

        {{-- BAGIAN KIRI GAMBAR --}}
        <div class="hidden lg:flex w-1/2 bg-cover bg-center"
            style="background-image: url('{{ asset('assets/images/background.png') }}');">
        </div>
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 md:px-12 py-10">
            <div class="w-full max-w-md text-center">
                <h1 class="text-2xl font-bold text-primary mb-2">Periksa Email Anda</h1>
                <p class="text-gray-600">Kami telah mengirimkan link verifikasi ke <span
                        class="font-semibold">{{ $email }}</span>. Silakan buka email dan klik tombol verifikasi untuk
                    melanjutkan pendaftaran.</p>

                @if (session('status') === 'verification-link-sent')
                    <div class="mt-4 text-green-600">Tautan verifikasi telah dikirim.</div>
                @endif

                <div class="mt-6 flex flex-col gap-3">
                    <form method="POST" action="{{ route('register.resend') }}" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-primary w-full">Kirim Ulang Tautan</button>
                    </form>
                    <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:underline">Ganti email</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>