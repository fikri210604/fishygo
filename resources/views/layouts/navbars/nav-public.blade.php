<nav class="fixed top-0 left-0 w-full z-50 bg-white text-gray-800 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        
        <!-- Logo -->
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="h-10">
        </a>

        <!-- Menu utama -->
        <div class="hidden md:flex items-center gap-6">
            <a href="{{ url('/') }}#home" class="hover:text-orange-500 font-medium">Home</a>
            <a href="{{ url('/') }}#kategori" class="hover:text-orange-500 font-medium">Kategori</a>
            <a href="{{ url('/') }}#produk" class="hover:text-orange-500 font-medium">Produk</a>
        </div>

        <!-- Tombol kanan -->
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

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-outline" type="submit">Logout</button>
                </form>
            @endauth

        </div>
    </div>
</nav>
