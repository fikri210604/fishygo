@extends('layouts.app')

@section('title', 'Checkout - FishyGo')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-primary mb-2">Checkout</h1>
    <p class="text-sm text-gray-500 mb-6">Periksa alamat dan ringkasan pesanan sebelum lanjut.</p>

    @if (session('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="font-semibold mb-3">Alamat Pengiriman</h2>
                <form id="form-checkout" action="{{ route('checkout.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm mb-1">Pilih Alamat</label>
                        <select name="alamat_id" class="select select-bordered w-full">
                            @foreach($alamats as $al)
                                <option value="{{ $al->id }}" {{ optional($alamatTerpilih)->id === $al->id ? 'selected' : '' }}>
                                    {{ $al->penerima }} - {{ $al->alamat_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="select select-bordered w-full">
                                <option value="manual">Transfer Manual</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Catatan (opsional)</label>
                            <input type="text" name="catatan" class="input input-bordered w-full" value="{{ old('catatan') }}" />
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="font-semibold mb-3">Produk</h2>
                <div class="space-y-3">
                    @foreach ($items as $it)
                        @php($produk = $it['produk'])
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ $produk->nama_produk }}</div>
                                <div class="text-xs text-gray-500">Qty: {{ $it['qty'] }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm">Rp {{ number_format($it['harga'], 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-500">Subtotal: Rp {{ number_format($it['subtotal'], 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="font-semibold mb-3">Ringkasan</h2>
                <div class="flex items-center justify-between text-sm">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-sm mt-1">
                    <span>Ongkir</span>
                    <span>Rp 0</span>
                </div>
                <div class="flex items-center justify-between font-semibold text-base mt-3">
                    <span>Total</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <button form="form-checkout" class="btn btn-primary w-full mt-4">Buat Pesanan</button>
            </div>
        </div>
    </div>
</div>
@endsection

