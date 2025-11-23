<nav class="fixed top-0 left-0 w-full z-50 bg-white text-gray-800 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-6">
        <!-- Logo -->
        <div class="shrink-0 flex items-center">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Fishy Go" class="h-12" decoding="async" fetchpriority="high" height="48">
            </a>
        </div>

        <!-- Links (sesuai navbar.blade.php) -->
        <div class="hidden md:flex items-center space-x-8">
            <a href="{{ url('/') }}" class="font-semibold {{ request()->is('/') ? 'text-orange-400' : 'text-gray-800 hover:text-orange-400' }}">Home</a>
            <a href="{{ route('home') }}" class="font-semibold {{ request()->routeIs('home') ? 'text-orange-400' : 'text-gray-800 hover:text-orange-400' }}">Produk</a>
            <a href="{{ route('tentang') }}" class="font-semibold text-gray-800 hover:text-orange-400">Tentang</a>
        </div>

        <!-- Search -->
        <form method="GET" action="{{ route('home') }}" class="hidden lg:flex items-center flex-1 max-w-xl">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..." class="input input-bordered w-full">
            <button class="btn btn-primary ml-2">Cari</button>
        </form>

        @php
            $cartSession = session('cart', []);
            $cartCount = 0;
            if (is_array($cartSession)) {
                foreach ($cartSession as $row) {
                    $cartCount += (int) ($row['qty'] ?? 1);
                }
            }
        @endphp

        <!-- Cart icon -->
        <a href="{{ route('cart.index') }}" class="relative ml-2 text-gray-700 hover:text-orange-500">
            <i class="ri-shopping-cart-2-line text-2xl"></i>
            <span id="navbar-cart-count" class="absolute -top-1 -right-2 bg-orange-500 text-white text-[10px] leading-none rounded-full px-1.5 py-0.5 {{ $cartCount > 0 ? '' : 'hidden' }}">
                {{ $cartCount }}
            </span>
        </a>

        <!-- Profile dropdown -->
        @auth
            @php
                $avatar = Auth::user()->avatar ?? null;
                if ($avatar) {
                    $avatarUrl = \Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://'])
                        ? $avatar
                        : asset('storage/' . $avatar);
                }
            @endphp
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center px-3 py-2 text-sm rounded-md bg-white text-gray-700 hover:text-orange-500">
                        @if(!empty($avatar ?? null))
                            <img src="{{ $avatarUrl }}" alt="avatar" class="h-8 w-8 rounded-full object-cover mr-2" loading="lazy" decoding="async" width="32" height="32">
                        @else
                            <i class="ri-user-3-line text-xl mr-2"></i>
                        @endif
                        <div>{{ Auth::user()->username }}</div>
                        <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>
                    <x-dropdown-link :href="route('pesanan.history')">Riwayat Pesanan</x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Keluar</x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        @endauth
    </div>

    <!-- Mobile: links + search -->
    <div class="md:hidden px-4 pb-2 space-y-2">
        <div class="flex items-center gap-6">
            <a href="{{ url('/') }}" class="font-semibold {{ request()->is('/') ? 'text-orange-400' : 'text-gray-800 hover:text-orange-400' }}">Home</a>
            <a href="{{ route('home') }}" class="font-semibold {{ request()->routeIs('home') ? 'text-orange-400' : 'text-gray-800 hover:text-orange-400' }}">Produk</a>
            <a href="{{ route('tentang') }}" class="font-semibold text-gray-800 hover:text-orange-400">Tentang</a>
        </div>
        <form method="GET" action="{{ route('home') }}" class="flex items-center">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..." class="input input-bordered w-full">
            <button class="btn ml-2">Cari</button>
        </form>
        <div class="mt-2">
            <a href="{{ route('cart.index') }}" class="inline-flex items-center text-gray-800 hover:text-orange-500">
                <i class="ri-shopping-cart-2-line text-xl mr-1"></i>
                <span>Keranjang (<span id="navbar-cart-count-mobile">{{ $cartCount }}</span>)</span>
            </a>
        </div>
    </div>
</nav>
