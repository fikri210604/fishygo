<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Kiri: Gambar -->
        <div class="hidden lg:flex w-1/2 bg-cover bg-center" style="background-image: url('{{ asset('assets/images/background.png') }}');"></div>

        <!-- Kanan: Konten -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 md:px-12 py-10">
            <div class="w-full max-w-md">
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold text-primary mb-2">Verifikasi Email</h1>
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="h-12 mx-auto" loading="lazy">
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 p-3 rounded bg-green-50 text-green-700 border border-green-200">
                        Tautan verifikasi baru telah dikirim ke email kamu.
                    </div>
                @endif

                <div class="text-gray-700 text-sm leading-relaxed mb-6">
                    <p class="mb-2">Terima kasih telah mendaftar! Kami sudah mengirim tautan verifikasi ke email kamu.</p>
                    <p>Silakan buka email dan klik tautan verifikasi untuk melanjutkan.</p>
                </div>

                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('verification.send') }}" class="contents">
                        @csrf
                        <button type="submit" class="btn btn-primary">Kirim Ulang Email</button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="ml-auto">
                        @csrf
                        <button type="submit" class="btn">Keluar</button>
                    </form>
                </div>

                <div class="mt-6 text-xs text-gray-500">
                    Belum menerima email? Cek folder Spam/Promosi, atau klik "Kirim Ulang Email" di atas.
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
