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
        <button type="button" class="btn btn-ghost btn-sm ml-auto" @click="show = false" aria-label="Tutup">✕</button>
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
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Produk..."
                class="input input-bordered w-full pl-10" />
        </div>
    </form>


    {{-- Kategori --}}
    <h2 class="text-xl font-bold mb-4">Kategori Produk</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @foreach($kategori as $k)
            <a href="{{ route('dashboard', ['kategori' => $k->kategori_produk_id] + request()->only('q', 'jenis')) }}"
                class="block">
                <div class="card bg-white shadow-sm hover:shadow-md transition p-3">
                    <figure class="h-28 w-full overflow-hidden rounded relative">
                        <div class="skeleton absolute inset-0 bg-gray-200 animate-pulse"></div>
                        <img src="{{ $k->gambar_kategori ? asset('storage/' . $k->gambar_kategori) : '' }}"
                            alt="{{ $k->nama_kategori }}" class="absolute inset-0 w-full h-full object-cover opacity-0"
                            loading="lazy"
                            onload="this.classList.remove('opacity-0'); this.previousElementSibling.classList.add('hidden');">
                    </figure>
                    <div class="text-center font-medium mt-2">{{ $k->nama_kategori }}</div>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Filter Jenis Ikan --}}
    <h2 class="text-xl font-bold mb-4">Temukan Berbagai Produk Ikan</h2>
    <div class="flex flex-wrap gap-2 mb-8">
        @php($active = request('jenis') === null)
        <a href="{{ route('dashboard', request()->except('jenis')) }}" class="px-4 py-1.5 text-sm rounded-full border transition-all duration-200
       {{ $active
    ? 'bg-[#6A453B] border-[#6A453B] text-white'
    : 'bg-[#E6E6E6] border-[#046DBD] text-[#046DBD] hover:bg-[#d2d2d2]' }}">
            Semua
        </a>
        @foreach($jenis_ikan as $j)
        @php($active = (string) request('jenis') === (string) $j->jenis_ikan_id)
        <a href="{{ route('dashboard', ['jenis' => $j->jenis_ikan_id] + request()->except('page')) }}" class="px-4 py-1.5 text-sm rounded-full border transition-all duration-200
           {{ $active
        ? 'bg-[#6A453B] border-[#6A453B] text-white'
        : 'bg-[#E6E6E6] border-[#046DBD] text-[#046DBD] hover:bg-[#d2d2d2]' }}">
            {{ $j->jenis_ikan }}
        </a>
        @endforeach

    </div>



    {{-- Daftar Produk --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5 mb-6">
        @forelse($produk as $p)
            <div class="card bg-base-100 shadow hover:shadow-md transition">
                <figure class="h-40 overflow-hidden relative">
                    <div class="skeleton absolute inset-0 bg-gray-200 animate-pulse"></div>
                    <img src="{{ $p->gambar_produk ? asset('storage/' . $p->gambar_produk) : '' }}"
                        alt="{{ $p->nama_produk }}" class="absolute inset-0 w-full h-full object-cover opacity-0"
                        loading="lazy"
                        onload="this.classList.remove('opacity-0'); this.previousElementSibling.classList.add('hidden');">
                </figure>
                <div class="card-body p-3">
                    <p class="font-semibold text-sm">{{ $p->nama_produk }}</p>
                    <p class="text-sm text-primary font-bold">Rp {{ number_format($p->harga ?? 0, 0, ',', '.') }}</p>
                    <div class="card-actions justify-between mt-2">
                        <a href="#" class="btn btn-xs bg-gray-200">Detail</a>
                        <a href="#" class="btn btn-xs btn-primary">Beli</a>
                    </div>
                </div>
            </div>
        @empty
            <p class="col-span-full text-gray-500 text-center">Produk belum tersedia.</p>
        @endforelse
    </div>

    <div class="mb-10 flex justify-center">
        <div class="join">
            <a href="{{ $produk->previousPageUrl() ?: '#' }}"
                class="join-item btn btn-sm {{ $produk->onFirstPage() ? 'btn-disabled' : '' }}">«</a>
            <button class="join-item btn btn-sm">Page {{ $produk->currentPage() }} / {{ $produk->lastPage() }}</button>
            <a href="{{ $produk->nextPageUrl() ?: '#' }}"
                class="join-item btn btn-sm {{ $produk->hasMorePages() ? '' : 'btn-disabled' }}">»</a>
        </div>
    </div>

    {{-- Artikel --}}
    <h2 class="text-xl font-bold mb-4">Temukan Berbagai Artikel Menarik</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($artikel as $a)
            <a href="{{ route('articles.show', $a->slug) }}" class="block card shadow-sm hover:shadow-md transition">
                <figure class="h-40 overflow-hidden relative">
                    <div class="skeleton absolute inset-0 bg-gray-200 animate-pulse"></div>
                    <img src="{{ $a->thumbnail ? asset('storage/' . $a->thumbnail) : '' }}" alt="{{ $a->judul }}"
                        class="absolute inset-0 w-full h-full object-cover opacity-0" loading="lazy"
                        onload="this.classList.remove('opacity-0'); this.previousElementSibling.classList.add('hidden');">
                </figure>
                <div class="card-body p-4">
                    <h3 class="font-semibold leading-snug">{{ $a->judul }}</h3>
                </div>
            </a>
        @endforeach
    </div>

</div>

<div class="mb-16 flex justify-center">
    <div class="join">
        <a href="{{ $kategori->previousPageUrl() ?: '#' }}"
            class="join-item btn btn-sm {{ $kategori->onFirstPage() ? 'btn-disabled' : '' }}">«</a>
        <button class="join-item btn btn-sm">Page {{ $kategori->currentPage() }} / {{ $kategori->lastPage() }}</button>
        <a href="{{ $kategori->nextPageUrl() ?: '#' }}"
            class="join-item btn btn-sm {{ $kategori->hasMorePages() ? '' : 'btn-disabled' }}">»</a>
    </div>
</div>

@endsection