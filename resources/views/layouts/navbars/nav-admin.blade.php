<nav class="sticky top-0 z-40 bg-white text-gray-800 shadow-sm">
    <div class="h-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">

        <div class="flex items-center gap-3">

            {{-- MOBILE SIDEBAR TOGGLE --}}
            <button @click="$store.layout.toggleSidebar()" type="button"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 md:hidden">
                <span class="sr-only">Open sidebar</span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo"
                     class="h-10" loading="lazy">
            </a>
        </div>

        @auth
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="inline-flex items-center px-3 py-2 text-sm rounded-md bg-white text-gray-700 hover:text-orange-500">
                    <div>{{ Auth::user()->username }}</div>
                    <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06
                                     1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                              clip-rule="evenodd"/>
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>

                @can('access-admin')
                    <x-dropdown-link :href="route('admin.dashboard')">Dashboard</x-dropdown-link>
                @endcan

                <x-alert-confirmation
                    modal-id="confirm-logout-nav-admin"
                    title="Keluar akun?"
                    message="Anda akan keluar dari sesi saat ini."
                    confirm-text="Keluar"
                    cancel-text="Batal"
                    variant="danger"
                    action="{{ route('logout') }}"
                    method="POST"
                >
                    <span class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Keluar</span>
                </x-alert-confirmation>
            </x-slot>
        </x-dropdown>
        @endauth
    </div>
</nav>
