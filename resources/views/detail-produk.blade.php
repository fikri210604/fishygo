@extends('layouts.app')

@section('title', $produk->nama_produk . ' - FishyGo')
@section('content')

    {{-- Hero Banner --}}
    <div class="relative w-full h-64 bg-cover bg-center"
        style="background-image: url('{{ asset('assets/images/background.png') }}')">
        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
            <h1 class="text-white text-4xl font-bold">{{ $produk->nama_produk }}</h1>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-12">

        <h2 class="text-2xl font-bold text-primary mb-6">Detail Produk</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            {{-- Galeri Foto Produk --}}
            @if (empty($photoUrls))
                <div class="space-y-3">
                    <div class="skeleton w-full h-80 rounded-lg"></div>
                    <div class="flex gap-2">
                        <div class="skeleton h-16 w-16 rounded"></div>
                        <div class="skeleton h-16 w-16 rounded"></div>
                        <div class="skeleton h-16 w-16 rounded"></div>
                        <div class="skeleton h-16 w-16 rounded hidden md:block"></div>
                    </div>
                </div>
            @else
                <div x-data="{ i: {{ $primaryIndex }}, imgs: @json($photoUrls) }" class="w-full">
                    <div class="relative w-full h-80 md:h-96 overflow-hidden rounded-lg shadow-md">
                        <template x-if="imgs.length">
                            <img :src="imgs[i]" alt="{{ $produk->nama_produk }}" class="absolute inset-0 w-full h-full object-cover" loading="lazy" decoding="async">
                        </template>
                        <button type="button" class="btn btn-circle btn-sm absolute left-2 top-1/2 -translate-y-1/2"
                                @click="i = (i - 1 + imgs.length) % imgs.length" aria-label="Sebelumnya">‹</button>
                        <button type="button" class="btn btn-circle btn-sm absolute right-2 top-1/2 -translate-y-1/2"
                                @click="i = (i + 1) % imgs.length" aria-label="Berikutnya">›</button>
                    </div>
                    <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                        <template x-for="(src, idx) in imgs" :key="idx">
                            <button type="button" @click="i = idx"
                                    :class="'h-16 w-16 rounded overflow-hidden border ' + (idx === i ? 'border-primary' : 'border-base-200')">
                                <img :src="src" alt="thumb" class="w-full h-full object-cover" loading="lazy" decoding="async" width="64" height="64">
                            </button>
                        </template>
                    </div>
                </div>
            @endif


            {{-- Informasi Produk --}}
            <div>
                <h1 class="text-3xl font-bold">{{ $produk->nama_produk }}</h1>

                <p class="text-sm text-gray-500 mt-1">
                    Kategori: <span class="font-medium text-gray-800">{{ $produk->kategori->nama_kategori }}</span> •
                    Jenis: <span class="font-medium text-gray-800">{{ $produk->jenisIkan->jenis_ikan }}</span>
                </p>

                <p class="text-3xl font-bold text-primary mt-4">
                    Rp {{ number_format($produk->harga, 0, ',', '.') }}
                </p>

                {{-- TAB NAV --}}
                <div x-data="{ tab: 'detail' }" class="mt-6">

                    <div class="border-b flex gap-6 text-gray-600 font-semibold pb-2">
                        <button @click="tab = 'detail'"
                            :class="tab === 'detail' ? 'border-b-2 border-primary text-primary' : ''" class="pb-1">
                            Detail
                        </button>

                        <button @click="tab = 'review'"
                            :class="tab === 'review' ? 'border-b-2 border-primary text-primary' : ''" class="pb-1">
                            Review ({{ $produk->rating_count ?? 0 }})
                        </button>
                    </div>

                    {{-- TAB: DETAIL --}}
                    <div x-show="tab === 'detail'" class="mt-4 text-gray-700 leading-relaxed">
                        {!! nl2br(e($produk->deskripsi ?? 'Tidak ada deskripsi')) !!}
                    </div>

                    {{-- -TAB: REVIEW --}}
                    <div x-show="tab === 'review'" class="mt-4">
                        @forelse($produk->reviews ?? [] as $rev)
                            <div class="border rounded-lg p-4 mb-3 bg-gray-50">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold">{{ $rev->pengguna->username ?? 'Pengguna' }}</span>
                                    <span class="text-sm text-gray-500">{{ $rev->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-yellow-500 text-sm mb-1">
                                    {{ str_repeat('⭐', $rev->rating) }}
                                </div>
                                <p class="text-gray-700">{{ $rev->review }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500">Belum ada review.</p>
                        @endforelse
                        {{-- Form Tambah Review --}}
                        @auth
                            <form action="{{ route('produk.review.store', $produk->produk_id) }}" method="POST"
                                class="mt-4 space-y-3">
                                @csrf
                                <div>
                                    <label class="label"><span class="label-text">Rating</span></label>
                                    <select name="rating" class="select select-bordered w-full" required>
                                        <option value="5">⭐ ⭐ ⭐ ⭐ ⭐ (Sangat Baik)</option>
                                        <option value="4">⭐ ⭐ ⭐ ⭐ (Baik)</option>
                                        <option value="3">⭐ ⭐ ⭐ (Cukup)</option>
                                        <option value="2">⭐ ⭐ (Kurang)</option>
                                        <option value="1">⭐ (Buruk)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="label"><span class="label-text">Komentar</span></label>
                                    <textarea name="review" class="textarea textarea-bordered w-full" rows="3"
                                        required></textarea>
                                </div>
                                <button class="btn btn-primary w-full">Kirim Review</button>
                            </form>
                        @else
                            <p class="text-sm mt-4">Silakan <a href="{{ route('login') }}"
                                    class="text-primary underline">login</a> untuk menulis review.</p>
                        @endauth
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    @auth
                        <form action="{{ route('cart.add', $produk->produk_id) }}" method="POST" data-cart-add="true">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Tambahkan Ke Keranjang
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            Tambahkan Ke Keranjang
                        </a>
                    @endauth
                    <a href="{{ route('cart.index') }}" class="btn btn-outline">
                        Lihat Keranjang
                    </a>
                </div>
            </div>
        </div>

        {{-- Rekomendasi --}}
        <h2 class="text-2xl font-bold text-primary mt-16 mb-6">Temukan Rekomendasi Lainnya</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5 mb-16 mt-6">

            @forelse($rekomendasi as $r)
                <div class="card bg-base-100 shadow hover:shadow-md transition">
                    <figure class="h-40 overflow-hidden relative">
                        <div class="skeleton absolute inset-0 bg-gray-200 animate-pulse"></div>
                        <img src="{{ $r->gambar_produk ? asset('storage/' . $r->gambar_produk) : '' }}"
                            alt="{{ $r->nama_produk }}" class="absolute inset-0 w-full h-full object-cover opacity-0"
                            loading="lazy" decoding="async"
                            onload="this.classList.remove('opacity-0'); this.previousElementSibling.classList.add('hidden');">
                    </figure>
                    <div class="card-body p-3">
                        <p class="font-semibold text-sm">{{ $r->nama_produk }}</p>
                        <p class="text-sm text-primary font-bold">Rp {{ number_format($r->harga ?? 0, 0, ',', '.') }}</p>
                        <div class="card-actions justify-between mt-2">
                            @if(!empty($r->slug))
                                <a href="{{ route('produk.show', ['produk' => $r->slug]) }}"
                                    class="btn btn-xs bg-gray-200">Detail</a>
                            @else
                                <span class="btn btn-xs bg-gray-200 btn-disabled" title="Slug tidak tersedia">Detail</span>
                            @endif
                            @auth
                                <form action="{{ route('cart.add', $r->produk_id) }}" method="POST" data-cart-add="true">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-primary flex items-center gap-1">
                                        <span class="text-xs font-bold">+</span>
                                        <i class="ri-shopping-cart-2-line text-sm"></i>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-xs btn-primary flex items-center gap-1">
                                    <span class="text-xs font-bold">+</span>
                                    <i class="ri-shopping-cart-2-line text-sm"></i>
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

            @empty
                <p class="col-span-full text-gray-500 text-center">Belum ada rekomendasi.</p>
            @endforelse

        </div>


    </div>

@endsection
