<!-- Mobile overlay -->
<div x-show="$store.layout.sidebarOpen" x-cloak class="fixed inset-0 bg-black/30 z-40 md:hidden" @click="$store.layout.closeSidebar()"></div>

<aside class="fixed inset-y-0 left-0 z-50 w-64 transform bg-white border-r border-gray-200 transition-transform md:static md:translate-x-0 md:w-64"
       :class="{ '-translate-x-full': !$store.layout.sidebarOpen, 'translate-x-0': $store.layout.sidebarOpen }">
    <div class="h-full py-6 px-4 overflow-y-auto">
        <nav class="space-y-1">
            @php
                $dashboardRoute = optional(Auth::user())->dashboardRoute() ?? 'dashboard';
            @endphp
            <a href="{{ route($dashboardRoute) }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs($dashboardRoute) ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">Dashboard</a>

            @can('access-admin')
                <div class="mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin</div>
                <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">Users</a>
                <a href="{{ route('admin.admins.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.admins.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">Admins</a>
            @endcan

            <div class="mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Akun</div>
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('profile.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">Profile</a>
        </nav>
    </div>
</aside>
