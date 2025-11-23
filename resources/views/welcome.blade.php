<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="corporate">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FishyGo - Fresh Fish, Fast</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">


    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>


<body class="bg-white pt-16">

    @auth
        @include('layouts.navbars.nav-user')
    @else
        @include('layouts.navbars.nav-public')
    @endauth

    {{-- HERO --}}
    <section id="home" class="min-h-screen flex flex-col justify-center items-center text-center px-6"
        @if(file_exists(public_path('assets/images/background.png')))
            style="background:url('{{ asset('assets/images/background.png') }}') center/cover no-repeat;"
        @else
            class="bg-gradient-to-br from-blue-500 via-purple-600 to-pink-500"
        @endif
    >
        @if(file_exists(public_path('assets/images/logo-fishygo.png')))
            <img src="{{ asset('assets/images/logo-fishygo.png') }}" loading="lazy" decoding="async" class="w-56 md:w-80 mb-6 drop-shadow-lg" alt="FishyGo">
        @else
            <div class="w-56 h-56 md:w-80 md:h-80 bg-white/20 rounded-full flex items-center justify-center mb-6 backdrop-blur-sm">
                <div class="text-8xl md:text-9xl">🐟</div>
            </div>
        @endif
        <h1 class="text-3xl md:text-5xl font-bold text-primary drop-shadow-lg">Fresh Fish, Fast</h1>
        <p class="mt-3 text-base md:text-lg font-light">Ikan segar langsung dari nelayan ke meja Anda</p>
        <a href="#produk" class="btn btn-neutral mt-6 px-8">Lihat Produk</a>
    </section>

    {{-- KATEGORI --}}
    <section id="kategori" class="px-6 md:px-10 py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-primary font-semibold text-sm uppercase tracking-wider">Pilih Kategori</span>
                <h2 class="text-4xl md:text-5xl font-bold mt-3 text-gray-800">Kategori Ikan</h2>
                <p class="mt-4 text-gray-600 text-lg max-w-2xl mx-auto">Temukan berbagai jenis ikan segar berkualitas</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-6xl mx-auto">
                @php
                    $kategoris = isset($kategoris) ? $kategoris : [
                        (object)['id' => 1, 'nama' => 'Ikan Laut', 'deskripsi' => 'Tangkapan segar dari laut', 'icon' => '🌊'],
                        (object)['id' => 2, 'nama' => 'Ikan Air Tawar', 'deskripsi' => 'Budidaya air tawar', 'icon' => '🏞️'],
                        (object)['id' => 3, 'nama' => 'Seafood', 'deskripsi' => 'Udang, cumi, kerang', 'icon' => '🦐'],
                        (object)['id' => 4, 'nama' => 'Ikan Premium', 'deskripsi' => 'Pilihan spesial', 'icon' => '⭐'],
                    ];
                @endphp

                @foreach($kategoris as $kategori)
                    <a href="#produk" class="card bg-white shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                        <div class="card-body items-center text-center p-6">
                            <div class="text-5xl mb-3">
                                {{ $kategori->icon ?? '🐟' }}
                            </div>
                            <h3 class="font-bold text-lg">{{ $kategori->nama ?? 'Kategori' }}</h3>
                            <p class="text-sm text-gray-600">{{ $kategori->deskripsi ?? 'Deskripsi kategori' }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- PRODUK --}}
    <section id="produk" class="px-6 md:px-10 py-24 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-primary font-semibold text-sm uppercase tracking-wider">Pilihan Terbaik</span>
                <h2 class="text-4xl md:text-5xl font-bold mt-3 text-gray-800">Produk Kami</h2>
                <p class="mt-4 text-gray-600 text-lg max-w-2xl mx-auto">Ikan segar pilihan dengan kualitas terbaik</p>
            </div>

            @php
                $produks = isset($produks) ? $produks : [
                    (object)[
                        'id' => 1,
                        'nama' => 'Ikan Kerapu',
                        'deskripsi' => 'Segar langsung dari nelayan',
                        'harga' => 85000,
                        'stok' => 10,
                        'gambar' => 'ikan1.jpg',
                        'rating' => 4.9,
                        'badge' => 'Best Seller'
                    ],
                    (object)[
                        'id' => 2,
                        'nama' => 'Ikan Tuna',
                        'deskripsi' => 'Kualitas premium untuk sushi',
                        'harga' => 120000,
                        'stok' => 5,
                        'gambar' => 'ikan2.jpg',
                        'rating' => 5.0,
                        'badge' => 'Fresh'
                    ],
                    (object)[
                        'id' => 3,
                        'nama' => 'Ikan Kakap',
                        'deskripsi' => 'Rasa lezat untuk keluarga',
                        'harga' => 95000,
                        'stok' => 8,
                        'gambar' => 'ikan3.jpg',
                        'rating' => 4.8,
                        'badge' => 'Popular'
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($produks as $produk)
                    <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                        <figure class="relative h-64 overflow-hidden">
                            @if(isset($produk->gambar) && file_exists(public_path('assets/img/' . $produk->gambar)))
                                <img src="{{ asset('assets/img/' . $produk->gambar) }}" 
                                     alt="{{ $produk->nama ?? 'Produk' }}" 
                                     class="h-full w-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                    <span class="text-8xl">🐠</span>
                                </div>
                            @endif
                            
                            @if(isset($produk->badge))
                                <div class="badge badge-warning absolute top-4 right-4 font-semibold">
                                    {{ $produk->badge }}
                                </div>
                            @endif

                            @if(isset($produk->stok) && $produk->stok <= 5)
                                <div class="badge badge-error absolute top-4 left-4 font-semibold">
                                    Stok Terbatas
                                </div>
                            @endif
                        </figure>
                        
                        <div class="card-body">
                            <h3 class="card-title text-2xl text-gray-800">
                                {{ $produk->nama ?? 'Nama Produk' }}
                            </h3>
                            <p class="text-gray-600">
                                {{ $produk->deskripsi ?? 'Deskripsi produk' }}
                            </p>
                            
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-2xl font-bold text-primary">
                                    Rp {{ isset($produk->harga) ? number_format($produk->harga, 0, ',', '.') : '0' }}
                                </span>
                                <span class="text-sm text-gray-500">/kg</span>
                            </div>
                            
                            @if(isset($produk->rating))
                                <div class="flex gap-1 mt-2">
                                    @for($i = 0; $i < 5; $i++)
                                        <span class="text-warning">{{ $i < floor($produk->rating) ? '⭐' : '☆' }}</span>
                                    @endfor
                                    <span class="text-sm text-gray-600">({{ $produk->rating }})</span>
                                </div>
                            @endif
                            
                            @if(isset($produk->stok))
                                <div class="text-sm text-gray-600 mt-2">
                                    Stok: <span class="font-semibold">{{ $produk->stok }} kg</span>
                                </div>
                            @endif
                            
                            <div class="card-actions justify-end mt-4">
                                <button class="btn btn-primary btn-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                                    </svg>
                                    Pesan Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-6xl mb-4">🐟</div>
                        <p class="text-gray-500 text-lg">Belum ada produk tersedia</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- TENTANG --}}
    <section id="tentang" class="px-6 md:px-10 py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-primary font-semibold text-sm uppercase tracking-wider">Kenali Kami</span>
                <h2 class="text-4xl md:text-5xl font-bold mt-3 text-gray-800">Tentang FishyGo</h2>
            </div>

            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="order-2 md:order-1">
                    <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-3xl p-8 text-white shadow-2xl">
                        <h3 class="text-3xl font-bold mb-4">Misi Kami</h3>
                        <p class="text-lg leading-relaxed mb-6">
                            FishyGo menghubungkan nelayan lokal langsung kepada konsumen untuk pengiriman ikan segar cepat dan berkualitas. Kami berkomitmen untuk mendukung ekonomi lokal sambil menyediakan produk laut terbaik untuk keluarga Indonesia.
                        </p>
                        <div class="stats stats-vertical shadow-xl bg-white/10 backdrop-blur-sm text-white">
                            <div class="stat">
                                <div class="stat-value text-warning">500+</div>
                                <div class="stat-title text-white/80">Nelayan Partner</div>
                            </div>
                            <div class="stat">
                                <div class="stat-value text-warning">10K+</div>
                                <div class="stat-title text-white/80">Pelanggan Puas</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="order-1 md:order-2">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="card bg-white shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                            <div class="text-5xl mb-3">🚚</div>
                            <h4 class="font-bold text-lg">Pengiriman Cepat</h4>
                            <p class="text-sm text-gray-600 mt-2">Diantar dalam 2 jam</p>
                        </div>
                        <div class="card bg-white shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                            <div class="text-5xl mb-3">❄️</div>
                            <h4 class="font-bold text-lg">Tetap Segar</h4>
                            <p class="text-sm text-gray-600 mt-2">Cold chain system</p>
                        </div>
                        <div class="card bg-white shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                            <div class="text-5xl mb-3">✅</div>
                            <h4 class="font-bold text-lg">Kualitas Terjamin</h4>
                            <p class="text-sm text-gray-600 mt-2">100% quality check</p>
                        </div>
                        <div class="card bg-white shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                            <div class="text-5xl mb-3">🌊</div>
                            <h4 class="font-bold text-lg">Langsung Nelayan</h4>
                            <p class="text-sm text-gray-600 mt-2">Tanpa perantara</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- KONTAK --}}
    <section id="kontak" class="px-6 md:px-10 py-24 bg-white">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <span class="text-primary font-semibold text-sm uppercase tracking-wider">Hubungi Kami</span>
                <h2 class="text-4xl md:text-5xl font-bold mt-3 text-gray-800">Kontak Kami</h2>
                <p class="mt-4 text-gray-600 text-lg">Kami siap melayani Anda 24/7</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mb-12">
                <div class="card bg-white shadow-xl p-8 text-center hover:bg-gradient-to-br hover:from-blue-500 hover:to-purple-600 hover:text-white transition-all duration-300 hover:scale-105">
                    <div class="text-6xl mb-4">📍</div>
                    <h3 class="text-xl font-bold mb-3">Lokasi</h3>
                    <p class="opacity-80">Bandar Lampung</p>
                    <p class="opacity-80">Lampung</p>
                    <p class="opacity-80">Indonesia</p>
                    <button class="btn btn-outline btn-sm mt-4">Lihat Peta</button>
                </div>

                <div class="card bg-white shadow-xl p-8 text-center hover:bg-gradient-to-br hover:from-blue-500 hover:to-purple-600 hover:text-white transition-all duration-300 hover:scale-105">
                    <div class="text-6xl mb-4">📞</div>
                    <h3 class="text-xl font-bold mb-3">WhatsApp</h3>
                    <p class="opacity-80 mb-2">Chat langsung dengan kami</p>
                    <p class="text-2xl font-bold">08xx-xxxx-xxxx</p>
                    <button class="btn btn-success btn-sm mt-4 text-white">
                        Chat WhatsApp
                    </button>
                </div>

                <div class="card bg-white shadow-xl p-8 text-center hover:bg-gradient-to-br hover:from-blue-500 hover:to-purple-600 hover:text-white transition-all duration-300 hover:scale-105">
                    <div class="text-6xl mb-4">✉️</div>
                    <h3 class="text-xl font-bold mb-3">Email</h3>
                    <p class="opacity-80 mb-2">Kirim pertanyaan Anda</p>
                    <p class="text-lg font-bold break-all">support@fishygo.com</p>
                    <button class="btn btn-outline btn-sm mt-4">Kirim Email</button>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.footer')

   
    

    <script>
        const sections = document.querySelectorAll("section[id]");
        const navLinks = document.querySelectorAll(".nav-link");
    
        function activateLink(id) {
            navLinks.forEach(link => {
                link.classList.remove("border-b-2", "border-warning", "text-warning");
                if (link.getAttribute("href") === `#${id}`) {
                    link.classList.add("border-b-2", "border-warning", "text-warning");
                }
            });
        }
    
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    activateLink(entry.target.id);
                }
            });
        }, { threshold: 0.6 });
    
        sections.forEach(section => observer.observe(section));
    </script>
    
</body>

</html>
