<!-- Sidebar Overlay (Mobile Only) -->
<div x-show="$store.layout.sidebarOpen" x-cloak class="fixed inset-0 bg-black/40 z-40 md:hidden"
    @click="$store.layout.closeSidebar()"></div>

<!-- Sidebar -->
<aside
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transition-transform md:static md:translate-x-0"
    :class="{ '-translate-x-full': !$store.layout.sidebarOpen, 'translate-x-0': $store.layout.sidebarOpen }">

    <div class="flex flex-col h-full px-4 pt-4 pb-6 overflow-y-auto">

        <!-- BRAND + HAMBURGER -->
        <div class="flex items-center justify-between px-2 mb-6">
            <div class="text-xl font-extrabold text-gray-800 flex items-center gap-1 select-none">
                Fishy<b>GO</b>
                <span class="text-xs font-medium text-gray-500 ml-1">Admin</span>
            </div>

            <!-- Hamburger for Mobile -->
            <button @click="$store.layout.toggleSidebar()" class="md:hidden text-gray-600 hover:text-gray-900">
                <span class="material-symbols-outlined text-2xl">menu</span>
            </button>
        </div>

        <!-- NAVIGATION -->
        <nav class="flex-1">

            <!-- UTAMA -->
            <div class="px-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider mt-2 mb-2">Utama</div>
            @php($active = request()->routeIs('admin.dashboard'))
            <a href="{{ route('admin.dashboard') }}"
                class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] transition-colors pl-2 {{ $active ? 'bg-indigo-50 text-indigo-700 font-semibold border-l-4 border-indigo-500' : 'text-gray-700 hover:bg-gray-100' }}">
                <span
                    class="material-symbols-outlined text-[20px] shrink-0 {{ $active ? 'text-indigo-600' : 'text-gray-500 group-hover:text-gray-700' }}">dashboard</span>
                <span>Dashboard</span>
            </a>

            <!-- PRODUK -->
            <div x-data="{ open: false }" class="mt-4">
                <button @click="open = !open"
                    class="group flex items-center justify-between w-full px-3 py-2 rounded-md text-[15px] text-gray-700 hover:bg-gray-100 transition-colors">
                    <span class="flex items-center gap-3">
                        <span
                            class="material-symbols-outlined text-[20px] shrink-0 text-gray-500 group-hover:text-gray-700">inventory_2</span>
                        <span>Produk</span>
                    </span>
                    <span class="material-symbols-outlined text-[20px] text-gray-500 transition-transform duration-200"
                        :class="{ 'rotate-90': open }">chevron_right</span>
                </button>

                <div x-show="open" x-transition class="ml-8 mt-1 space-y-1">
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

            <!-- TRANSAKSI -->
            <div class="px-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider mt-4 mb-2">Transaksi</div>
            <a href="#"
                class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] text-gray-700 hover:bg-gray-100 transition-colors">
                <span
                    class="material-symbols-outlined text-[20px] shrink-0 text-gray-500 group-hover:text-gray-700">shopping_cart</span>
                <span>Transaksi</span>
            </a>
            <a href="#"
                class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] text-gray-700 hover:bg-gray-100 transition-colors">
                <span
                    class="material-symbols-outlined text-[20px] shrink-0 text-gray-500 group-hover:text-gray-700">mail</span>
                <span>Inbox</span>
            </a>

            <!-- AKUN & ARTIKEL -->
            <div class="px-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wider mt-4 mb-2">Akun & Artikel
            </div>
            @php($active = request()->routeIs('admin.users.*'))
            <a href="{{ route('admin.users.index') }}"
                class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] transition-colors pl-2 {{ $active ? 'bg-indigo-50 text-indigo-700 font-semibold border-l-4 border-indigo-500' : 'text-gray-700 hover:bg-gray-100' }}">
                <span
                    class="material-symbols-outlined text-[20px] shrink-0 {{ $active ? 'text-indigo-600' : 'text-gray-500 group-hover:text-gray-700' }}">groups</span>
                <span>Pengguna</span>
            </a>

            @php($active = request()->routeIs('admin.articles.*'))
            <a href="{{ route('admin.articles.index') }}"
                class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] transition-colors pl-2 {{ $active ? 'bg-indigo-50 text-indigo-700 font-semibold border-l-4 border-indigo-500' : 'text-gray-700 hover:bg-gray-100' }}">
                <span
                    class="material-symbols-outlined text-[20px] shrink-0 {{ $active ? 'text-indigo-600' : 'text-gray-500 group-hover:text-gray-700' }}">article</span>
                <span>Kelola Artikel</span>
            </a>
        </nav>

        <!-- BOTTOM SETTINGS -->
        <div class="border-t border-gray-200 pt-4 mt-4 space-y-1">
            <a href="{{ route('profile.edit') }}"
                class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] text-gray-700 hover:bg-gray-100 transition-colors">
                <span
                    class="material-symbols-outlined text-[20px] shrink-0 text-gray-500 group-hover:text-gray-700">settings</span>
                <span>Pengaturan</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="group flex items-center gap-3 px-3 py-2 rounded-md text-[15px] text-gray-700 hover:bg-gray-100 transition-colors w-full text-left">
                    <span
                        class="material-symbols-outlined text-[20px] shrink-0 text-gray-500 group-hover:text-gray-700">logout</span>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</aside>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('layout', {
            sidebarOpen: false,
            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
            },
            closeSidebar() {
                this.sidebarOpen = false;
            }
        });
    });
</script>
