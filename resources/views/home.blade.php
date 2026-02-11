@extends('layouts.app')

@section('title', 'Produk Kami | FishyGo')

@section('content')

@section('hide-toast', true)

@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.200ms role="alert"
        class="alert alert-success shadow mb-6 mx-auto max-w-6xl">
        <i class="fa-solid fa-circle-check text-2xl text-green-600"></i>
        <div>
            <div class="font-semibold">Berhasil</div>
            <div class="text-sm">{{ session('success') }}</div>
        </div>
        <button type="button" class="btn btn-ghost btn-sm ml-auto" @click="show = false" aria-label="Tutup">�</button>
    </div>
@endif

{{-- Hero Banner --}}
<div class="relative w-full h-64 bg-cover bg-center"
    style="background-image: url('{{ asset('assets/images/background.png') }}')">
    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
        <h1 class="text-white text-4xl font-bold">Produk Kami</h1>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 py-8">

    {{-- Search --}}
    <form method="GET" class="flex justify-center mb-8">
        <div class="relative w-full max-w-xl">
            <i class="ri-search-line absolute left-3 top-2.5 text-gray-400 text-lg"></i>
            <input type="text" id="searchInput" name="q" value="{{ request('q') }}" placeholder="Cari Produk..."
                class="input input-bordered w-full pl-10" autocomplete="off" />
        </div>
    </form>

    {{-- Kategori --}}
    <h2 class="text-xl font-bold mb-4">Kategori Produk</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
        @forelse($kategori as $k)
            <a href="{{ route('home', ['kategori' => $k->kategori_produk_id] + request()->only('q', 'jenis')) }}"
                class="block" data-load-products>
                <div class="card bg-white shadow-sm hover:shadow-md transition p-3">
                    <figure class="h-48 w-full overflow-hidden rounded relative">
                        <div class="skeleton absolute inset-0 bg-gray-200 animate-pulse"></div>
                        <img src="{{ $k->gambar_kategori ? asset('storage/' . $k->gambar_kategori) : '' }}"
                            alt="{{ $k->nama_kategori }}" class="absolute inset-0 w-full h-full object-cover opacity-0"
                            loading="lazy" decoding="async" sizes="(max-width: 640px) 50vw, 25vw"
                            onload="this.classList.remove('opacity-0'); this.previousElementSibling.classList.add('hidden');">
                    </figure>
                    <div class="text-center font-medium mt-2">{{ $k->nama_kategori }}</div>
                </div>
            </a>
        @empty
            <p class="text-gray-500">Belum ada kategori.</p>
        @endforelse
    </div>

    {{-- Filter Jenis Ikan --}}
    <h2 class="text-xl font-bold mb-4">Temukan Berbagai Produk Ikan</h2>

    <div class="mb-8 overflow-x-auto scrollbar-hide mt-4">
        <div class="flex gap-2 w-max">
            @php($active = request('jenis') === null)
            <a href="{{ route('home', request()->except('jenis')) }}"
                class="px-4 py-1.5 text-sm whitespace-nowrap rounded-full border transition-all duration-200
               {{ $active ? 'bg-[#6A453B] border-[#6A453B] text-white' : 'bg-[#E6E6E6] border-[#046DBD] text-[#046DBD] hover:bg-[#d2d2d2]' }}">
                Semua
            </a>
            @foreach($jenis_ikan as $j)
            @php($active = (string) request('jenis') === (string) $j->jenis_ikan_id)
            <a href="{{ route('home', ['jenis' => $j->jenis_ikan_id] + request()->except('page')) }}"
                class="px-4 py-1.5 text-sm whitespace-nowrap rounded-full border transition-all duration-200
                 {{ $active ? 'bg-[#6A453B] border-[#6A453B] text-white' : 'bg-[#E6E6E6] border-[#046DBD] text-[#046DBD] hover:bg-[#d2d2d2]' }}">
                {{ $j->jenis_ikan }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- Daftar Produk (AJAX Pagination) --}}
    <div id="product-list" data-ajax-pagination="produk">
        @include('partials.products-grid', ['produk' => $produk])
    </div>

    {{-- Artikel --}}
    <h2 class="text-xl font-bold mb-4">Temukan Berbagai Artikel Menarik</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($artikel as $a)
            <a href="{{ route('articles.show', $a->slug) }}" class="block card shadow-sm hover:shadow-md transition">
                <figure class="h-48 overflow-hidden relative">
                    <div class="skeleton absolute inset-0 bg-gray-200 animate-pulse"></div>
                    <img src="{{ $a->thumbnail ? asset('storage/' . $a->thumbnail) : '' }}" alt="{{ $a->judul }}"
                        class="absolute inset-0 w-full h-full object-cover opacity-0" loading="lazy" decoding="async"
                        sizes="(max-width: 1024px) 50vw, 33vw"
                        onload="this.classList.remove('opacity-0'); this.previousElementSibling.classList.add('hidden');">
                </figure>
                <div class="card-body p-4">
                    <h3 class="font-semibold leading-snug">{{ $a->judul }}</h3>
                </div>
            </a>
        @empty
            <p class="col-span-full text-gray-500 text-center">Artikel belum tersedia.</p>
        @endforelse
    </div>

</div>

<div class="mb-16 flex justify-center">
    <div class="join">
        <a href="{{ $kategori->previousPageUrl() ?: '#' }}"
            class="join-item btn btn-sm {{ $kategori->onFirstPage() ? 'btn-disabled' : '' }}">�</a>
        <button class="join-item btn btn-sm">Page {{ $kategori->currentPage() }} / {{ $kategori->lastPage() }}</button>
        <a href="{{ $kategori->nextPageUrl() ?: '#' }}"
            class="join-item btn btn-sm {{ $kategori->hasMorePages() ? '' : 'btn-disabled' }}">�</a>
    </div>
</div>

@endsection
