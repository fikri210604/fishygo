<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pengaturan Profil
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="profileTabs" x-init="init()">
            <div class="grid md:grid-cols-4 gap-6">

                <aside class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 h-fit">
                    <nav class="space-y-1">
                
                        <button type="button" @click="setTab('info')"
                                :class="tab==='info' ? 'bg-orange-50 text-orange-600' : 'hover:bg-gray-100 text-gray-700'"
                                class="w-full text-left px-3 py-2 rounded-md font-medium">
                            Informasi Akun
                        </button>
                
                        @if(!auth()->user()?->can('access-admin') && !auth()->user()?->can('access-kurir'))
                        <button type="button" @click="setTab('alamat')"
                                :class="tab==='alamat' ? 'bg-orange-50 text-orange-600' : 'hover:bg-gray-100 text-gray-700'"
                                class="w-full text-left px-3 py-2 rounded-md font-medium">
                            Alamat Pengiriman
                        </button>
                        @endif
                
                        <button type="button" @click="setTab('password')"
                                :class="tab==='password' ? 'bg-orange-50 text-orange-600' : 'hover:bg-gray-100 text-gray-700'"
                                class="w-full text-left px-3 py-2 rounded-md font-medium">
                            Ubah Password
                        </button>
                
                        <button type="button" @click="setTab('hapus')"
                                :class="tab==='hapus' ? 'bg-red-50 text-red-600' : 'hover:bg-gray-100 text-gray-700'"
                                class="w-full text-left px-3 py-2 rounded-md font-medium">
                            Hapus Akun
                        </button>
                
                        {{-- ================== LOGOUT BUTTON ================== --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-3 py-2 rounded-md font-medium text-red-600 hover:bg-red-50">
                                Logout
                            </button>
                        </form>
                    </nav>
                </aside>
                

                <!-- Content -->
                <section class="md:col-span-3 space-y-6">
                    <div x-show="tab==='info'" id="info" x-cloak class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    @if(!auth()->user()?->can('access-admin') && !auth()->user()?->can('access-kurir'))
                    <div x-show="tab==='alamat'" id="alamat" x-cloak class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        @include('profile.partials.update-address-form')
                    </div>
                    @endif

                    <div x-show="tab==='password'" id="password" x-cloak class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        @include('profile.partials.update-password-form')
                    </div>

                    <div x-show="tab==='hapus'" id="hapus" x-cloak class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
