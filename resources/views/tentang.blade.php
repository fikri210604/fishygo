@extends('layouts.app')

@section('title', 'Tentang FishyGo')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-10">
        <div class="mb-10 text-center">
            <span class="text-primary font-semibold text-sm uppercase tracking-wider">Kenali Kami</span>
            <h1 class="text-3xl md:text-4xl font-bold mt-3 text-gray-800">Tentang FishyGo</h1>
            <p class="mt-4 text-gray-600 max-w-2xl mx-auto">
                FishyGo adalah platform yang menghubungkan nelayan lokal langsung kepada konsumen untuk pengiriman ikan
                segar yang cepat dan berkualitas.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-10 items-start">
            <div>
                <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-3xl p-8 text-white shadow-2xl">
                    <h2 class="text-2xl md:text-3xl font-bold mb-4">Misi Kami</h2>
                    <p class="text-base md:text-lg leading-relaxed mb-6">
                        Kami berkomitmen mendukung ekonomi lokal dengan membeli langsung dari nelayan dan pelaku usaha
                        perikanan, sekaligus memastikan keluarga Indonesia mendapatkan produk laut terbaik dengan harga
                        yang wajar.
                    </p>
                    <div class="stats stats-vertical lg:stats-horizontal shadow-xl bg-white/10 backdrop-blur-sm text-white">
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

            <div class="space-y-4">
                <div class="card bg-white shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <h3 class="font-bold text-lg mb-1">Pengiriman Cepat</h3>
                    <p class="text-sm text-gray-600">
                        Sistem distribusi dingin (cold chain) memastikan produk tetap segar sampai di tangan pelanggan
                        dengan estimasi pengiriman hingga 2 jam di area tertentu.
                    </p>
                </div>
                <div class="card bg-white shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <h3 class="font-bold text-lg mb-1">Kualitas Terjaga</h3>
                    <p class="text-sm text-gray-600">
                        Setiap produk melewati proses seleksi dan pengecekan kualitas sehingga hanya ikan dan seafood
                        terbaik yang kami kirimkan.
                    </p>
                </div>
                <div class="card bg-white shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <h3 class="font-bold text-lg mb-1">Dukungan Nelayan Lokal</h3>
                    <p class="text-sm text-gray-600">
                        Dengan berbelanja di FishyGo, kamu turut membantu meningkatkan kesejahteraan nelayan dan UMKM
                        lokal di sektor perikanan.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

