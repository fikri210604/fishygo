<nav class="fixed top-0 left-0 w-full z-50 bg-white text-gray-800 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        
        <!-- Logo -->
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="h-10" decoding="async" fetchpriority="high" height="40">
        </a>

        <!-- Menu utama -->
        <div class="hidden md:flex items-center gap-6">
            <a href="{{ url('/') }}#home" class="hover:text-orange-500 font-medium">Home</a>
            <a href="{{ url('/') }}#produk" class="hover:text-orange-500 font-medium">Produk</a>
            <a href="{{ route('articles.index') }}" class="hover:text-orange-500 font-medium">Artikel</a>
        </div>
        <div class="flex items-center gap-2">
            @guest
                <!-- Belum login -->
                <a href="{{ route('login') }}" class="btn btn-ghost">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
            @endguest

            @auth
                <!-- Sudah login -->
                <a href="{{ route('dashboard') }}" class="btn btn-ghost">
                    Halo, {{ Auth::user()->nama ?? 'Pengguna' }}
                </a>

                <x-alert-confirmation
                    modal-id="confirm-logout-nav-public"
                    title="Keluar akun?"
                    message="Anda akan keluar dari sesi saat ini."
                    confirm-text="Keluar"
                    cancel-text="Batal"
                    variant="danger"
                    action="{{ route('logout') }}"
                    method="POST"
                >
                    <span class="btn btn-outline">Logout</span>
                </x-alert-confirmation>
            @endauth

        </div>
    </div>
</nav>
