@extends('layouts.app')

@section('title', 'Checkout - FishyGo')

@section('content')
<div class="max-w-5xl mx-auto py-8">

    {{-- Header --}}
    <h1 class="text-3xl md:text-4xl font-bold text-primary mb-2">Checkout</h1>
    <p class="text-sm text-gray-500 mb-6">
        Periksa alamat dan ringkasan pesanan sebelum melanjutkan proses.
    </p>

    {{-- Flash messages --}}
    @if (session('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif


    <div class="grid md:grid-cols-3 gap-6">
        
        {{-- LEFT SECTION --}}
        <div class="md:col-span-2 space-y-6">

            {{-- Shipping Address --}}
            <div class="bg-white shadow-sm rounded-xl p-4">
                <h2 class="font-semibold mb-3">Alamat Pengiriman</h2>

                <form id="form-checkout" action="{{ route('checkout.store') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Select Address --}}
                    <div>
                        <label class="block text-sm mb-1">Pilih Alamat</label>
                        <select name="alamat_id" class="select select-bordered w-full">
                            @foreach($alamats as $al)
                                <option value="{{ $al->id }}" 
                                    {{ optional($alamatTerpilih)->id === $al->id ? 'selected' : '' }}> - {{ $al->alamat_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Payment Section --}}
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Metode Pembayaran</label>

                            <input type="hidden" name="metode_pembayaran" id="metode_pembayaran" 
                                   value="{{ old('metode_pembayaran', 'midtrans') }}">
                            <input type="hidden" name="pickup" id="pickup" 
                                   value="{{ old('pickup', 0) }}">

                            <div id="pay-methods" class="space-y-2">

                                {{-- Midtrans (default) --}}
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="checkbox" data-value="midtrans"
                                        {{ old('metode_pembayaran','midtrans') === 'midtrans' ? 'checked' : '' }}>
                                    <span>Transfer / Virtual Account (Midtrans)</span>
                                </label>

                                {{-- COD --}}
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="checkbox" data-value="cod"
                                        {{ old('metode_pembayaran') === 'cod' ? 'checked' : '' }}>
                                    <span>Bayar di Tempat (COD)</span>
                                </label>

                                {{-- COD Options --}}
                                <div id="cod-options" class="ml-7 mt-2 hidden space-y-2">

                                    {{-- Antar --}}
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="__cod_type" class="radio" data-pickup="0"
                                            {{ old('pickup') ? '' : 'checked' }}>
                                        <span>Diantar ke alamat</span>
                                    </label>

                                    {{-- Pickup --}}
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="__cod_type" class="radio" data-pickup="1"
                                            {{ old('pickup') ? 'checked' : '' }}>
                                        <span>Ambil di tempat</span>
                                    </label>

                                    {{-- Pickup Address --}}
                                    <div id="pickup-address" class="hidden bg-gray-100 p-3 rounded text-sm text-gray-700">
                                        <strong>Alamat Pengambilan Barang:</strong><br>
                                        Jl. Pahlawan No. 45, Bandar Lampung<br>
                                        (Gudang Ikan Segar)
                                    </div>
                                </div>

                                {{-- Transfer Manual --}}
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="checkbox" data-value="manual"
                                        {{ old('metode_pembayaran') === 'manual' ? 'checked' : '' }}>
                                    <span>Transfer Manual</span>
                                </label>

                            </div>
                        </div>

                        {{-- Note --}}
                        <div>
                            <label class="block text-sm mb-1">Catatan (opsional)</label>
                            <input type="text" name="catatan" class="input input-bordered w-full"
                                value="{{ old('catatan') }}">
                        </div>
                    </div>

                </form>
            </div>

            {{-- Product List --}}
            <div class="bg-white shadow-sm rounded-xl p-4">
                <h2 class="font-semibold mb-3">Produk</h2>

                <div class="space-y-3">
                    @foreach ($items as $it)
                        @php($produk = $it['produk'])
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ $produk->nama_produk }}</div>
                                <div class="text-xs text-gray-500">Qty: {{ $it['qty'] }}</div>
                            </div>

                            <div class="text-right text-sm">
                                <div>Rp {{ number_format($it['harga'], 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-500">
                                    Subtotal: Rp {{ number_format($it['subtotal'], 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                    @endforeach
                </div>
            </div>
        </div>


        {{-- RIGHT SECTION --}}
        <div>
            <div class="bg-white shadow-sm rounded-xl p-4">

                <h2 class="font-semibold mb-3">Ringkasan</h2>

                <div class="flex justify-between text-sm">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between text-sm mt-1">
                    <span>Ongkir</span>
                    <span>Rp 0</span>
                </div>

                <div class="flex justify-between font-semibold text-base mt-3">
                    <span>Total</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>

                <button form="form-checkout" class="btn btn-primary w-full mt-4">
                    Buat Pesanan
                </button>

            </div>
        </div>

    </div>
</div>

@endsection
