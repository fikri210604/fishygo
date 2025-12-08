<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="corporate">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FishyGo - Fresh Fish, Fast</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        html { scroll-behavior: smooth; }
    </style>
</head>

<body class="bg-white pt-16 overflow-x-hidden">

    {{-- NAVBAR --}}
    @auth
        @include('layouts.navbars.nav-user')
    @else
        @include('layouts.navbars.nav-public')
    @endauth


    {{-- ================= HERO SECTION ================= --}}
    <section id="home" class="relative h-screen overflow-hidden">
        <div id="hero-bg"
             class="absolute inset-0 bg-center bg-cover"
             @if(file_exists(public_path('assets/images/background.png')))
                 style="background-image: url('{{ asset('assets/images/background.png') }}');"
             @else
                 style="background-image: linear-gradient(135deg, #3b82f6, #9333ea, #ec4899);"
             @endif>
        </div>

        <div class="absolute inset-0 bg-black/30"></div>

        <div class="relative z-10 flex flex-col items-center justify-center text-center h-full px-6">

            {{-- Logo --}}
            @if(file_exists(public_path('assets/images/logo-fishygo.png')))
                <img src="{{ asset('assets/images/logo-fishygo.png') }}"
                     class="w-56 md:w-80 mb-6 drop-shadow-lg"
                     alt="FishyGo" loading="lazy" decoding="async">
            @else
                <div class="w-56 h-56 md:w-80 md:h-80 bg-white/20 rounded-full flex items-center justify-center mb-6 backdrop-blur-sm">
                    <div class="text-8xl md:text-9xl">🐟</div>
                </div>
            @endif

            <h1 class="text-3xl md:text-5xl font-bold text-white drop-shadow-lg">Ikan Segar dan Sehat</h1>
            <p class="mt-3 text-base md:text-lg font-light text-white">
                Berasal dari kolam budidaya terbaik — rasa lebih lembut dan cocok untuk olahan rumahan.            
            </p>

            <a href="#produk" class="btn btn-primary mt-6 px-8">Lihat Produk</a>
        </div>
    </section>


    {{-- ================= ABOUT US ================= --}}
    <section id="tentang" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">

            <div class="text-center mb-16">
                <span class="text-orange-600 uppercase tracking-widest text-sm">Tentang Kami</span>
                <h2 class="text-4xl md:text-5xl font-bold mt-2">Menghadirkan Produk Ikan Air Tawar Berkualitas Tinggi</h2>
                <p class="text-gray-600 mt-3 max-w-2xl mx-auto">
                    Dibesarkan dan dibudidayakan dengan sistem
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 sm:grid-cols-3">
                @foreach ([
                    ['img'=>'produk1.jpg','title'=>'Ikan Segar'],
                    ['img'=>'produk2.jpg','title'=>'Ikan Berkualitas Tinggi'],
                    ['img'=>'produk3.jpg','title'=>'Bumbu Rempah Berkualitas'],
                ] as $item)
                    <div class="relative group h-[360px] rounded-2xl overflow-hidden shadow-md sm:h-[240px]">
                        <img src="{{ asset('assets/images/'.$item['img']) }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700">

                        <div class="absolute bottom-6 left-6 text-xl font-semibold text-white drop-shadow-lg">
                            {{ $item['title'] }}
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>


    {{-- ================= LEADERS SECTION ================= --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-16 items-center">

            <div>
                <span class="text-orange-600 uppercase tracking-widest text-sm">Awesome Product</span>
                <h2 class="text-4xl font-bold mt-3">We’re Leaders in Agriculture</h2>
                <p class="text-gray-600 mt-5 leading-relaxed">
                    With 30+ years experience...
                </p>
            </div>

                 <div class="relative group h-[360px] rounded-2xl overflow-hidden shadow-md">
                    <img src="{{ asset('assets/images/produks.jpg') }}"
                         class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700">
                </div>
    </section>


    {{-- ================= PRODUK ================= --}}
<section id="produk" class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">

        <div class="grid md:grid-cols-3 gap-12">

            {{-- LEFT CONTENT --}}
            <div>
                <span class="text-blue-600 uppercase tracking-widest text-sm font-semibold">
                    Healthy Food
                </span>
                <h2 class="text-4xl font-bold mt-2 text-gray-900">Products</h2>
                <p class="text-gray-600 mt-4 leading-relaxed">
                    Our greens are tender...
                </p>
            </div>

            {{-- SLIDER WRAPPER --}}
            <div class="md:col-span-2 relative overflow-hidden scrollbar-hide"
                 x-data="centerCarousel()"
                 x-init="init($refs.track)">

                {{-- BUTTON LEFT --}}
                <button type="button"
                        aria-label="Sebelumnya"
                        @click="prev()"
                        class="md:flex absolute left-0 top-1/2 -translate-y-1/2 z-10
                               bg-white shadow w-9 h-9 rounded-full items-center justify-center">
                    <i class="ri-arrow-left-s-line text-xl"></i>
                </button>

                {{-- VIEWPORT --}}
                <div class="overflow-hidden px-1">
                    {{-- TRACK --}}
                    <div class="flex gap-10 min-w-max will-change-transform transition-transform duration-500"
                         x-ref="track"
                         :style="`transform: translateX(${translateX}px);`">

                        @forelse ($data as $d)
                            {{-- PRODUCT CARD --}}
                            <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden 
                                        w-[300px] flex-shrink-0 transition-all duration-300 cursor-pointer"
                                 :class="Number($el.dataset.idx) === index
                                         ? 'scale-105 z-10'
                                         : 'scale-95 opacity-80'"
                                 data-idx="{{ $loop->index }}"
                                 @click="go(Number($el.dataset.idx))">

                                {{-- IMAGE --}}
                                <div class="relative h-64 overflow-hidden">
                                    @php $img = $d->gambar_produk ? asset('storage/' . $d->gambar_produk) : null; @endphp

                                    @if ($img)
                                        <img src="{{ $img }}"
                                             alt="{{ $d->nama_produk }}"
                                             class="w-full h-full object-cover transition-transform duration-700 hover:scale-110">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-blue-400 to-blue-600
                                                    flex items-center justify-center">
                                            <span class="text-7xl text-white">🐟</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- DETAIL --}}
                                <div class="p-5">
                                    <h3 class="text-xl font-semibold text-gray-800">
                                        {{ $d->nama_produk }}
                                    </h3>
                                    <p class="text-gray-600 text-sm mt-1 line-clamp">
                                        {{ $d->deskripsi }}
                                    </p>
                                </div>

                            </div>
                        @empty
                            <div class="text-center py-10">Tidak ada produk.</div>
                        @endforelse

                    </div>
                </div>

                {{-- BUTTON RIGHT --}}
                <button type="button"
                        aria-label="Berikutnya"
                        @click="next()"
                        class="md:flex absolute right-0 top-1/2 -translate-y-1/2 z-10
                               bg-white shadow w-9 h-9 rounded-full items-center justify-center">
                    <i class="ri-arrow-right-s-line text-xl"></i>
                </button>

            </div> {{-- /slider --}}
        </div>
    </div>
</section>



    <section id="recent-news" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6 sm:max-w-5xl">

        <div class="text-center mb-16">
            <span class="text-orange-600 uppercase tracking-widest text-sm">Latest Update</span>
            <h1 class="text-4xl md:text-5xl font-bold mt-2 uppercase">Recent News</h1>
        </div>
    
        <div class="relative overflow-hidden" x-data="centerCarousel()" x-init="init($refs.track)">

            <button type="button" aria-label="Sebelumnya" @click="prev()"
                class="flex absolute md:flex top-1/2 -translate-y-1/2 z-30 
       bg-white shadow-lg w-10 h-10 md:w-12 md:h-12 rounded-full
       items-center justify-center hover:bg-gray-50">
                <i class="ri-arrow-left-s-line text-2xl"></i>
            </button>

            <div class="overflow-x-auto scrollbar-hide">
                <div class="flex gap-6 min-w-max py-2 will-change-transform transition-transform duration-500" x-ref="track" :style="`transform: translateX(${translateX}px);`">
    
                    @forelse($artikel as $a)
                        <a href="{{ route('articles.show', $a->slug) }}" data-idx="{{ $loop->index }}"
                            class="block min-w-[75%] sm:min-w-[45%] md:min-w-[30%] lg:min-w-[25%]
                                   bg-white rounded-xl shadow-sm transition flex-shrink-0"
                            :class="Number($el.dataset.idx) === index ? 'scale-105 z-10' : 'scale-95 opacity-80'">
    
                            <figure class="h-48 sm:h-56 md:h-64 overflow-hidden relative rounded-t-xl">
                                <div class="skeleton absolute inset-0 bg-gray-200 animate-pulse"></div>
    
                                <img src="{{ asset('storage/' . $a->thumbnail) }}"
                                    alt="{{ $a->judul }}"
                                    class="absolute inset-0 w-full h-full object-cover opacity-0 transition duration-500"
                                    onload="this.classList.remove('opacity-0'); this.previousElementSibling.classList.add('hidden');">
                            </figure>
    
                            <div class="p-4">
                                <div class="flex items-center text-sm text-gray-400 mb-2">
                                    <i class="ri-calendar-fill text-lg mr-1"></i>
                                    <span class="font-medium">{{ $a->diterbitkan_pada }}</span>
                                </div>
                                <h3 class="font-semibold text-gray-900 leading-snug line-clamp-2">
                                    {{ $a->judul }}
                                </h3>
                            </div>
    
                        </a>
                    @empty
                        <p class="text-gray-500 text-center">Artikel belum tersedia.</p>
                    @endforelse
    
                </div>
            </div>
    
            {{-- BUTTON RIGHT --}}
            <button type="button" aria-label="Berikutnya" @click="next()"
                class="md:flex absolute right-0 top-1/2 -translate-y-1/2 z-20 bg-white shadow-lg w-12 h-12 rounded-full
                       items-center justify-center hover:bg-gray-50">
                <i class="ri-arrow-right-s-line text-2xl"></i>
            </button>
    
        </div>
        </div>
    
    </section>
    


    {{-- FOOTER --}}
    @include('layouts.footer')


    <script>
        // Centered-card carousel for welcome page sections
        function centerCarousel() {
            return {
                index: 0,
                translateX: 0,
                track: null,
                slides: [],
                widths: [],
                positions: [],
                init(track) {
                    this.track = track || (this.$refs ? this.$refs.track : null) || this.track;
                    if (!this.track) {
                        this.$nextTick(() => {
                            this.track = (this.$refs ? this.$refs.track : null) || this.track;
                            if (this.track) { this.measure(); this.center(); }
                        });
                    } else {
                        this.$nextTick(() => { this.measure(); this.center(); });
                    }
                    window.addEventListener('resize', () => { this.measure(); this.center(); });
                },
                measure() {
                    if (!this.track) return;
                    this.slides = Array.from((this.track && this.track.children) ? this.track.children : []);
                    this.widths = this.slides.map(el => el.offsetWidth || 0);
                    this.positions = this.slides.map(el => el.offsetLeft || 0);
                },
                center() {
                    if (!this.track) return;
                    const container = this.track.parentElement || this.track;
                    const w = container.clientWidth || 0;
                    const pos = this.positions[this.index] ?? 0;
                    const cw = this.widths[this.index] ?? 0;
                    this.translateX = Math.round(w / 2 - (pos + cw / 2));
                },
                next() { if (this.index < this.slides.length - 1) { this.index++; this.center(); } },
                prev() { if (this.index > 0) { this.index--; this.center(); } },
                go(i) { this.index = Math.max(0, Math.min(i, this.slides.length - 1)); this.center(); }
            }
        }

        // HERO PARALLAX ZOOM (disable on small screens, clamp zoom)
        (function(){
            const bg = document.getElementById('hero-bg');
            if (!bg) return;
            const isMobile = window.matchMedia('(max-width: 640px)').matches;
            if (isMobile) { bg.style.transform = 'none'; return; }
            function onScroll(){
                const scroll = window.scrollY || 0;
                const zoom = Math.min(1.25, 1 + scroll / 2500);
                bg.style.transform = `scale(${zoom})`;
                bg.style.transformOrigin = 'center';
            }
            document.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        })();
    </script>

</body>
</html>
