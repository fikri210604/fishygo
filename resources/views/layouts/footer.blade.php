 <footer class="bg-gray-900 text-white py-12 px-6">
    <div class="max-w-6xl mx-auto text-center">

        {{-- Logo & Brand --}}
        <div class="flex items-center justify-center gap-3 mb-4">
            @if(file_exists(public_path('assets/images/logo.png')))
                <img src="{{ asset('assets/images/logo.png') }}" class="h-10" alt="FishyGo Logo" />
            @else
                <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-bold text-xl">
                    F
                </div>
            @endif
            <span class="text-2xl font-bold tracking-tight">FishyGo</span>
        </div>

        {{-- Tagline --}}
        <p class="text-gray-400 mb-6">Fresh Fish, Fast — Ikan Segar untuk Keluarga Indonesia</p>

        {{-- Social Icons --}}
        <div class="flex justify-center gap-3 mb-6">

            <a href="#" class="btn btn-circle btn-outline btn-sm hover:bg-primary hover:border-primary transition">
                <i class="ri-facebook-fill text-lg"></i>
            </a>
        
            <a href="#" class="btn btn-circle btn-outline btn-sm hover:bg-primary hover:border-primary transition">
                <i class="ri-instagram-fill text-lg"></i>
            </a>
        
            <a href="#" class="btn btn-circle btn-outline btn-sm hover:bg-primary hover:border-primary transition">
                <i class="ri-tiktok-fill text-lg"></i>
            </a>
        
            <a href="#" class="btn btn-circle btn-outline btn-sm hover:bg-primary hover:border-primary transition">
                <i class="ri-twitter-x-line text-lg"></i>
            </a>
        
        </div>
        

        {{-- Footer Text --}}
        <p class="text-gray-500 text-sm">© {{ date('Y') }} FishyGo. All rights reserved.</p>

    </div>
</footer>