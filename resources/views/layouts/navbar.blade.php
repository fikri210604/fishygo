<nav x-data="{ open: false }" class="fixed top-0 left-0 w-full z-50 bg-white text-gray-800 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                @can('access-admin')
                    <!-- Sidebar hamburger (admin, mobile only) -->
                    <div class="-ml-2 mr-2 flex items-center md:hidden">
                        <button @click="$store.layout.toggleSidebar()" type="button"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange-400">
                            <span class="sr-only">Open sidebar</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>
                    </div>
                @endcan

                <div class="shrink-0 flex items-center">
                    @can('access-admin')
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="Fishy Go" class="h-12">
                        </a>
                    @elsecan('access-kurir')
                        <a href="{{ route('kurir.dashboard') }}" class="flex items-center">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="Fishy Go" class="h-12">
                        </a>
                    @else
                        <a href="{{ route('home') }}" class="flex items-center">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="Fishy Go" class="h-12">
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Desktop menu -->
            <div class="hidden md:flex items-center space-x-10">
                <a href="{{ url('/') }}"
                    class="font-semibold {{ request()->is('/') ? 'text-orange-400' : 'text-gray-800 hover:text-orange-400' }}">
                    Home
                </a>

                <a href="{{ route('home') }}"
                    class="font-semibold {{ request()->routeIs('home') ? 'text-orange-400' : 'text-gray-800 hover:text-orange-400' }}">
                    Produk
                </a>

                <a href="{{ url('/#tentang') }}" class="font-semibold text-gray-800 hover:text-orange-400">
                    Tentang
                </a>

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md bg-white text-gray-700 hover:text-orange-500 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->username }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth

                <!-- Cart icon -->
                <a href="#" class="text-gray-800 hover:text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 3h2l1 5m0 0h13l-1.2 6H7.8M6 8L5 4m2.8 10h10.4M9 20a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z" />
                    </svg>
                </a>
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center md:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-900 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': ! open, 'inline-flex': open }" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden md:hidden bg-white border-t border-gray-200 shadow-sm">
        <div class="px-4 pt-2 pb-3 space-y-1">
            <a href="{{ url('/') }}"
                class="block py-2 text-sm font-semibold {{ request()->is('/') ? 'text-orange-400' : 'text-gray-800' }}">
                Home
            </a>

            <a href="{{ route('home') }}"
                class="block py-2 text-sm font-semibold {{ request()->routeIs('home') ? 'text-orange-400' : 'text-gray-800' }}">
                Produk
            </a>

            <a href="{{ url('/#tentang') }}" class="block py-2 text-sm font-semibold text-gray-800">
                Tentang
            </a>

            <a href="#" class="block py-2 text-sm font-semibold text-gray-800">
                Keranjang
            </a>
        </div>

        @auth
            <div class="border-t border-gray-200 pt-3 pb-4 px-4 space-y-1">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->username }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>

                <a href="{{ route('profile.edit') }}"
                    class="block mt-2 text-sm text-gray-800 hover:text-orange-400">
                    {{ __('Profile') }}
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="w-full text-left text-sm text-gray-800 hover:text-orange-400">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        @endauth
    </div>
</nav>
