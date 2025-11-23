@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-semibold">Detail Pesanan</h1>
            <div class="text-xs text-gray-500 mt-1">Kode: {{ $pesanan->kode_pesanan }}</div>
        </div>
        <a href="{{ route('admin.pesanan.index') }}" class="btn btn-sm">Kembali</a>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-gray-500">Status</div>
                        <div class="font-medium">{{ ucfirst(str_replace('_',' ', $pesanan->status)) }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">Total</div>
                        <div class="font-medium">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="font-semibold mb-3">Item Pesanan</h2>
                <div class="space-y-3">
                    @foreach($pesanan->items as $it)
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ $it->nama_produk_snapshot }}</div>
                                <div class="text-xs text-gray-500">Qty: {{ $it->qty }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm">Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-500">Subtotal: Rp {{ number_format($it->subtotal, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="font-semibold mb-2">Pelanggan</h2>
                <div class="text-sm">{{ $pesanan->user?->nama ?? '-' }}</div>
                <div class="text-xs text-gray-500">{{ $pesanan->user?->email }}</div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="font-semibold mb-2">Pembayaran</h2>
                @php($pay = $pesanan->pembayaran->first())
                @if($pay)
                    <div class="text-sm">Status: <span class="font-medium">{{ ucfirst($pay->status) }}</span></div>
                    <div class="text-sm">Jumlah: Rp {{ number_format($pay->amount, 0, ',', '.') }}</div>
                    <div class="text-sm">Metode: {{ $pay->gateway }} - {{ $pay->channel }}</div>
                    <div class="text-xs text-gray-500 mt-2">Ref: {{ $pay->reference }}</div>
                @else
                    <div class="text-sm text-gray-500">Belum ada informasi pembayaran.</div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="font-semibold mb-2">Alamat</h2>
                @if($pesanan->alamat)
                    <div class="text-sm">{{ $pesanan->alamat->penerima }}</div>
                    <div class="text-sm">{{ $pesanan->alamat->alamat_lengkap }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $pesanan->alamat->village_name }}, {{ $pesanan->alamat->district_name }}, {{ $pesanan->alamat->regency_name }}, {{ $pesanan->alamat->province_name }} {{ $pesanan->alamat->kode_pos }}</div>
                @else
                    <div class="text-sm text-gray-500">Alamat tidak tersedia.</div>
                @endif
            </div>
        </div>
    </div>
@endsection

