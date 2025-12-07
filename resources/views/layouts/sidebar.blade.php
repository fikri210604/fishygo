<!-- Sidebar Overlay (Mobile Only) -->
<div x-show="$store.layout.sidebarOpen" x-cloak class="fixed inset-0 bg-black/40 z-40 md:hidden"
    @click="$store.layout.closeSidebar()"></div>

<!-- Sidebar -->
<aside
    class="fixed inset-y-0 left-0 z-50 bg-white border-r border-gray-200 transition-all"
    :class="{
        '-translate-x-full md:translate-x-0': !$store.layout.sidebarOpen,
        'translate-x-0': $store.layout.sidebarOpen,
        'w-64': !$store.layout.sidebarCollapsed,
        'w-16': $store.layout.sidebarCollapsed,
    }">

    <div class="flex flex-col h-full px-3 pt-3 pb-4 overflow-y-auto">

        <!-- BRAND + TOGGLES -->
        <div class="flex items-center justify-between mb-4">
            <a href="{{ route(auth()->user()?->isKurir() ? 'kurir.dashboard' : 'admin.dashboard') }}" class="flex items-center gap-2">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="h-8 w-8 object-contain" />
                <span class="font-extrabold text-gray-800" x-show="!$store.layout.sidebarCollapsed">FishyGO</span>
            </a>
            <div class="flex items-center gap-1">
                <!-- Collapse/Expand (Desktop) -->
                <button @click="$store.layout.sidebarCollapsed = !$store.layout.sidebarCollapsed" class="hidden md:inline-flex text-gray-600 hover:text-gray-900" :title="$store.layout.sidebarCollapsed ? 'Perluas' : 'Ciutkan'">
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>
                <!-- Mobile hamburger -->
                <button @click="$store.layout.toggleSidebar()" class="md:hidden text-gray-600 hover:text-gray-900">
                    <span class="material-symbols-outlined text-2xl">menu</span>
                </button>
            </div>
        </div>

        <!-- NAVIGATION -->
        <nav class="flex-1">

            @can('access-admin')
            <!-- UTAMA (Admin) -->
            <div class="px-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider mt-2 mb-2">Utama</div>
            @php($active = request()->routeIs('admin.dashboard'))
            <a href="{{ route('admin.dashboard') }}"
                class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] transition-colors pl-2 {{ $active ? 'bg-indigo-50 text-indigo-700 font-semibold border-l-4 border-indigo-500' : 'text-gray-700 hover:bg-gray-100' }}"
                :class="{ 'justify-center': $store.layout.sidebarCollapsed }">
                <span
                    class="material-symbols-outlined text-[20px] shrink-0 {{ $active ? 'text-indigo-600' : 'text-gray-500 group-hover:text-gray-700' }}">dashboard</span>
                <span x-show="!$store.layout.sidebarCollapsed">Dashboard</span>
            </a>

            <!-- PRODUK (Admin) -->
            <div x-data="{ open: false }" class="mt-4">
                <button @click="open = !open"
                    class="group flex items-center justify-between w-full px-3 py-2 rounded-md text-[15px] text-gray-700 hover:bg-gray-100 transition-colors"
                    :class="{ 'justify-center': $store.layout.sidebarCollapsed }">
                    <span class="flex items-center gap-3">
                        <span
                            class="material-symbols-outlined text-[20px] shrink-0 text-gray-500 group-hover:text-gray-700">inventory_2</span>
                        <span x-show="!$store.layout.sidebarCollapsed">Produk</span>
                    </span>
                    <span x-show="!$store.layout.sidebarCollapsed" class="material-symbols-outlined text-[20px] text-gray-500 transition-transform duration-200"
                        :class="{ 'rotate-90': open }">chevron_right</span>
                </button>

                <div x-show="open && !$store.layout.sidebarCollapsed" x-transition class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('admin.kategori.index') }}"
                        class="flex items-center gap-3 px-3 py-1.5 rounded-md text-[14px] text-gray-600 hover:bg-gray-100 transition-colors {{ request()->routeIs('admin.kategori.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
                        <span class="material-symbols-outlined text-[20px] shrink-0 text-gray-500">category</span>
                        <span>Kategori</span>
                    </a>
                    <a href="{{ route('admin.jenis-ikan.index') }}"
                        class="flex items-center gap-3 px-3 py-1.5 rounded-md text-[14px] text-gray-600 hover:bg-gray-100 transition-colors {{ request()->routeIs('admin.jenis-ikan.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
                        <span class="material-symbols-outlined text-[20px] shrink-0 text-gray-500">set_meal</span>
                        <span>Jenis Ikan</span>
                    </a>
                    <a href="{{ route('admin.produk.index') }}"
                        class="flex items-center gap-3 px-3 py-1.5 rounded-md text-[14px] text-gray-600 hover:bg-gray-100 transition-colors {{ request()->routeIs('admin.produk.*') ? 'bg-indigo-50 text-indigo-700' : '' }}">
                        <span class="material-symbols-outlined text-[20px] shrink-0 text-gray-500">inventory_2</span>
                        <span>List Produk</span>
                    </a>
                </div>
            </div>

            <!-- TRANSAKSI (Admin) -->
            <div class="px-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider mt-4 mb-2">Transaksi</div>
            @php($active = request()->routeIs('admin.pesanan.*'))
            <a href="{{ route('admin.pesanan.index') }}"
                class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] transition-colors {{ $active ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}"
                :class="{ 'justify-center': $store.layout.sidebarCollapsed }">
                <span class="relative material-symbols-outlined text-[20px] shrink-0 {{ $active ? 'text-indigo-600' : 'text-gray-500 group-hover:text-gray-700' }}">
                    shopping_cart
                    @if(($notifTransaksi ?? 0) > 0)
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] leading-none"
                              x-show="$store.layout.sidebarCollapsed"
                              x-cloak>{{ $notifTransaksi }}</span>
                    @endif
                </span>
                <span x-show="!$store.layout.sidebarCollapsed" class="flex-1 flex items-center">
                    <span>Transaksi</span>
                    @if(($notifTransaksi ?? 0) > 0)
                        <span class="ml-auto inline-flex items-center justify-center min-w-[20px] h-[20px] px-1.5 rounded-full bg-red-500 text-white text-[11px]">
                            {{ $notifTransaksi }}
                        </span>
                    @endif
                </span>
            </a>
            <a href="#"
                class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] text-gray-700 hover:bg-gray-100 transition-colors"
                :class="{ 'justify-center': $store.layout.sidebarCollapsed }">
                <span
                    class="material-symbols-outlined text-[20px] shrink-0 text-gray-500 group-hover:text-gray-700">mail</span>
                <span x-show="!$store.layout.sidebarCollapsed">Inbox</span>
            </a>
            @endcan

            @can('access-kurir')
            <!-- KURIR -->
            <div class="px-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider mt-4 mb-2" x-show="!$store.layout.sidebarCollapsed">Kurir</div>
            @php($active = request()->routeIs('kurir.dashboard'))
            <a href="{{ route('kurir.dashboard') }}"
                class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] transition-colors pl-2 {{ $active ? 'bg-indigo-50 text-indigo-700 font-semibold border-l-4 border-indigo-500' : 'text-gray-700 hover:bg-gray-100' }}"
                :class="{ 'justify-center': $store.layout.sidebarCollapsed }">
                <span class="material-symbols-outlined text-[20px] shrink-0 {{ $active ? 'text-indigo-600' : 'text-gray-500 group-hover:text-gray-700' }}">local_shipping</span>
                <span x-show="!$store.layout.sidebarCollapsed">Dashboard Kurir</span>
            </a>
            @php($active = request()->routeIs('kurir.pengiriman.*'))
            <a href="{{ route('kurir.pengiriman.index') }}"
                class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] transition-colors pl-2 {{ $active ? 'bg-indigo-50 text-indigo-700 font-semibold border-l-4 border-indigo-500' : 'text-gray-700 hover:bg-gray-100' }}"
                :class="{ 'justify-center': $store.layout.sidebarCollapsed }">
                <span class="material-symbols-outlined text-[20px] shrink-0 {{ $active ? 'text-indigo-600' : 'text-gray-500 group-hover:text-gray-700' }}">assignment</span>
                <span x-show="!$store.layout.sidebarCollapsed">Pengiriman</span>
            </a>
            @endcan

            @can('access-admin')
            <!-- AKUN & ARTIKEL (Admin) -->
            <div class="px-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider mt-4 mb-2">Akun & Artikel</div>
            @php($active = request()->routeIs('admin.users.*'))
            <a href="{{ route('admin.users.index') }}"
                class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] transition-colors pl-2 {{ $active ? 'bg-indigo-50 text-indigo-700 font-semibold border-l-4 border-indigo-500' : 'text-gray-700 hover:bg-gray-100' }}"
                :class="{ 'justify-center': $store.layout.sidebarCollapsed }">
                <span
                    class="material-symbols-outlined text-[20px] shrink-0 {{ $active ? 'text-indigo-600' : 'text-gray-500 group-hover:text-gray-700' }}">groups</span>
                <span x-show="!$store.layout.sidebarCollapsed">Pengguna</span>
            </a>

            @php($active = request()->routeIs('admin.articles.*'))
            <a href="{{ route('admin.articles.index') }}"
                class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] transition-colors pl-2 {{ $active ? 'bg-indigo-50 text-indigo-700 font-semibold border-l-4 border-indigo-500' : 'text-gray-700 hover:bg-gray-100' }}"
                :class="{ 'justify-center': $store.layout.sidebarCollapsed }">
                <span
                    class="material-symbols-outlined text-[20px] shrink-0 {{ $active ? 'text-indigo-600' : 'text-gray-500 group-hover:text-gray-700' }}">article</span>
                <span x-show="!$store.layout.sidebarCollapsed">Kelola Artikel</span>
            </a>
            @endcan            
        </nav>

        <!-- BOTTOM SETTINGS -->
        <div class="border-t border-gray-200 pt-4 mt-4 space-y-1">
            <a href="{{ route('profile.edit') }}"
                class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] text-gray-700 hover:bg-gray-100 transition-colors">
                <span
                    class="material-symbols-outlined text-[20px] shrink-0 text-gray-500 group-hover:text-gray-700">settings</span>
                <span>Pengaturan</span>
            </a>

            @can('access-admin')
            @php($active = request()->routeIs('admin.settings.roles.*') || request()->routeIs('admin.settings.permissions.*'))
            <a href="{{ route('admin.settings.roles.index') }}"
                class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] transition-colors pl-2 {{ $active ? 'bg-indigo-50 text-indigo-700 font-semibold border-l-4 border-indigo-500' : 'text-gray-700 hover:bg-gray-100' }}">
                <span class="material-symbols-outlined text-[20px] shrink-0 {{ $active ? 'text-indigo-600' : 'text-gray-500 group-hover:text-gray-700' }}">admin_panel_settings</span>
                <span>Role & Permission</span>
            </a>
            @endcan

            <x-alert-confirmation
                modal-id="confirm-logout-sidebar"
                title="Keluar akun?"
                message="Anda akan keluar dari sesi saat ini."
                confirm-text="Keluar"
                cancel-text="Batal"
                variant="danger"
                action="{{ route('logout') }}"
                method="POST"
            >
                <span class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] text-gray-700 hover:bg-gray-100 transition-colors w-full text-left cursor-pointer">
                    <span class="material-symbols-outlined text-[20px] shrink-0 text-gray-500 group-hover:text-gray-700">logout</span>
                    <span>Keluar</span>
                </span>
            </x-alert-confirmation>
        </div>
    </div>
</aside>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('layout', {
                sidebarOpen: false,
                sidebarCollapsed: false,
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                },
                closeSidebar() {
                    this.sidebarOpen = false;
                }
            });
        });
    </script>
