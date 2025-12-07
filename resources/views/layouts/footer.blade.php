<footer class="bg-[#0d0d0d] text-gray-300 pt-20 pb-10 px-6">
    <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-12">

        {{-- BRAND & DESCRIPTION --}}
        <div>
            <div class="flex items-center gap-2 mb-6">
                @if(file_exists(public_path('assets/images/logo.png')))
                    <img src="{{ asset('assets/images/logo.png') }}" class="h-10" alt="logo" loading="lazy" decoding="async">
                @else
                    <span class="text-3xl font-bold text-white">FishyGo</span>
                @endif
            </div>

            <p class="text-gray-400 leading-relaxed text-sm">
                Developing effective distribution channels is
                crucial for reaching customers in foreign
                markets.
            </p>
        </div>

        {{-- ADDRESS --}}
        <div>
            <h3 class="text-lg font-semibold text-white mb-4">Address</h3>

            <p class="text-gray-400 text-sm leading-relaxed">
                Jl. Indonesia Raya No.45, Jakarta  
                Pusat, DKI Jakarta, Indonesia
            </p>

            <p class="mt-4 text-sm text-gray-300">info@fishygo.com</p>

            <p class="mt-2 text-yellow-400 text-sm font-medium">
                +62 8128 008 0275
            </p>
        </div>

        {{-- MENU --}}
        <div>
            <h3 class="text-lg font-semibold text-white mb-4">Menu</h3>

            <ul class="space-y-2 text-gray-400">
                <li><a href="#home" class="hover:text-white transition">Home</a></li>
                <li><a href="#tentang" class="hover:text-white transition">About</a></li>
                <li><a href="#produk" class="hover:text-white transition">Products</a></li>
                <li><a href="#teams" class="hover:text-white transition">Teams</a></li>
                <li><a href="#recent-news" class="hover:text-white transition">News</a></li>
                <li><a href="#kontak" class="hover:text-white transition">Contact</a></li>
            </ul>
        </div>

        {{-- SOCIAL --}}
        <div>
            <h3 class="text-lg font-semibold text-white mb-4">Get in Touch</h3>

            <div class="flex items-center gap-4">

                <a href="#" class="w-10 h-10 rounded-full border border-gray-500 flex items-center justify-center 
                                  hover:bg-white hover:text-black transition">
                    <i class="ri-instagram-line text-xl"></i>
                </a>

                <a href="#" class="w-10 h-10 rounded-full border border-gray-500 flex items-center justify-center 
                                  hover:bg-white hover:text-black transition">
                    <i class="ri-facebook-fill text-xl"></i>
                </a>

                <a href="#" class="w-10 h-10 rounded-full border border-gray-500 flex items-center justify-center 
                                  hover:bg-white hover:text-black transition">
                    <i class="ri-youtube-fill text-xl"></i>
                </a>

                <a href="#" class="w-10 h-10 rounded-full border border-gray-500 flex items-center justify-center 
                                  hover:bg-white hover:text-black transition">
                    <i class="ri-tiktok-fill text-xl"></i>
                </a>

            </div>
        </div>

    </div>

    {{-- Separator --}}
    <div class="border-t border-gray-700 mt-14 pt-6 text-center">
        <p class="text-gray-500 text-sm">
            Copyright © {{ date('Y') }} FishyGo. All rights reserved.
        </p>
    </div>
</footer>
