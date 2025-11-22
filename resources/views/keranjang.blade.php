@extends('layouts.app')

@section('title', 'Keranjang Saya - FishyGo')

@section('content')
    <div class="max-w-5xl mx-auto py-8">
        <h1 class="text-3xl md:text-4xl font-bold text-primary mb-2">
            Keranjang Saya
        </h1>
        <p class="text-sm text-gray-500 mb-6">
            Hapus produk yang tidak kamu perlukan.
        </p>

        @if (empty($items))
            <div class="bg-white rounded-xl shadow-sm p-6 text-center text-gray-500">
                Keranjang kamu masih kosong.
            </div>
        @else
            <div class="flex justify-between items-center mb-4">
                <span class="text-sm text-gray-600" data-cart-items-count>
                    {{ $items_count ?? count($items) }} produk di keranjang
                </span>
                <form action="{{ route('cart.clear') }}" method="POST" data-cart-clear="true">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline rounded-full">
                        Hapus Semua
                    </button>
                </form>
            </div>

            <div class="space-y-4">
                @foreach ($items as $item)
                    @php
                        $produk = $item['produk'];
                        $qty = $item['qty'];
                        $harga = $item['harga'];
                        $subtotal = $item['subtotal'];
                    @endphp
                    <div class="flex items-center gap-4 bg-white rounded-2xl shadow-sm px-4 py-4"
                         data-cart-item
                         data-produk-id="{{ $produk->produk_id }}">
                        <div class="pt-2">
                            <input type="checkbox" class="checkbox checkbox-sm" disabled>
                        </div>

                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                            @php
                                $imgPath = $produk->gambar_produk ? asset('storage/' . $produk->gambar_produk) : null;
                            @endphp
                            @if ($imgPath)
                                <img src="{{ $imgPath }}" alt="{{ $produk->nama_produk }}"
                                     class="w-full h-full object-cover" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">
                                    Tidak ada gambar
                                </div>
                            @endif
                        </div>

                        <div class="flex-1">
                            <a href="{{ $produk->slug ? route('produk.show', ['produk' => $produk->slug]) : '#' }}"
                               class="font-semibold text-base text-blue-600 hover:underline">
                                {{ $produk->nama_produk }}
                            </a>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $produk->satuan ?? '1 Kg' }}
                            </p>
                        </div>

                        <div class="text-right space-y-2">
                            <p class="font-semibold text-primary text-sm">
                                Rp {{ number_format($harga, 0, ',', '.') }}
                            </p>
                            <form action="{{ route('cart.update', $produk->produk_id) }}" method="POST"
                                  class="inline-flex items-center bg-gray-100 rounded-full px-2 py-1"
                                  data-cart-update="true">
                                @csrf
                                @method('PUT')
                                <button type="submit" name="mode" value="dec"
                                        class="px-2 text-lg font-bold text-gray-700">
                                    -
                                </button>
                                <span class="px-2 text-sm min-w-[1.5rem] text-center" data-cart-qty>
                                    {{ $qty }}
                                </span>
                                <button type="submit" name="mode" value="inc"
                                        class="px-2 text-lg font-bold text-gray-700">
                                    +
                                </button>
                            </form>
                            <form action="{{ route('cart.remove', $produk->produk_id) }}" method="POST" data-cart-remove="true">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-xs text-red-500 hover:underline">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 sticky bottom-0 bg-white border-t border-gray-200 pt-4 pb-4">
                <div class="max-w-5xl mx-auto flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500">Total</p>
                        <p class="text-xl font-bold text-primary" id="cart-total">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </p>
                    </div>
                    <button class="btn btn-primary rounded-full px-8" data-cart-checkout-count>
                        Checkout ({{ $cart_count ?? count($items) }})
                    </button>
                </div>
            </div>
        @endif
    </div>
@endsection
