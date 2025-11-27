@extends('layouts.app')

@section('title', 'Detail Pesanan - FishyGo')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-primary mb-2">Detail Pesanan</h1>
    <p class="text-sm text-gray-500 mb-6">Kode: {{ $pesanan->kode_pesanan }}</p>

    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-semibold">Status</div>
                        <div class="text-sm text-gray-600">{{ ucfirst(str_replace('_',' ', $pesanan->status)) }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold">Total</div>
                        <div class="text-sm text-gray-600">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</div>
                    </div>
                </div>
                @if($pesanan->status === 'menunggu_pembayaran')
                    <form action="{{ route('pesanan.cancel', $pesanan->pesanan_id) }}" method="POST" class="mt-4 border-t pt-4">
                        @csrf
                        <div class="grid md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm mb-1">Alasan</label>
                                <select name="reason" class="select select-bordered w-full">
                                    <option value="">Pilih alasan</option>
                                    <option value="change_mind">Berubah pikiran</option>
                                    <option value="wrong_order">Salah pilih produk</option>
                                    <option value="payment_issue">Kendala pembayaran</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm mb-1">Catatan</label>
                                <input type="text" name="note" class="input input-bordered w-full" />
                            </div>
                        </div>
                        <button class="btn btn-error btn-sm text-white mt-3" onclick="return confirm('Batalkan pesanan ini?')">Batalkan Pesanan</button>
                    </form>
                @elseif($pesanan->status === 'dibatalkan')
                    <div class="mt-3 text-sm text-red-600">Dibatalkan pada {{ optional($pesanan->cancelled_at)->format('d M Y H:i') }}</div>
                @endif
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
        <div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="font-semibold mb-3">Pembayaran</h2>
                @php($pay = $pesanan->pembayaran->first())
                @if($pay)
                    <div class="text-sm">Status: <span class="font-medium">{{ ucfirst($pay->status) }}</span></div>
                    <div class="text-sm">Jumlah: Rp {{ number_format($pay->amount, 0, ',', '.') }}</div>
                    <div class="text-sm">Metode: {{ $pay->gateway }} - {{ $pay->channel }}</div>
                    <div class="text-xs text-gray-500 mt-2">Ref: {{ $pay->reference }}</div>
                @else
                    <div class="text-sm text-gray-500">Belum ada informasi pembayaran.</div>
                @endif

                @if(in_array($pesanan->status, ['menunggu_pembayaran','menunggu_konfirmasi']) && $pesanan->metode_pembayaran === 'midtrans')
                    <button id="btn-pay-midtrans" class="btn btn-primary w-full mt-4">Bayar dengan Midtrans</button>

                    <script
                        src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
                        data-client-key="{{ config('midtrans.client_key') }}"
                        defer
                    ></script>
                    <script>
                        (function(){
                            const btn = document.getElementById('btn-pay-midtrans');
                            if(!btn) return;
                            const pesananId = @json($pesanan->pesanan_id);
                            const snapUrl = @json(route('payment.midtrans.snap'));
                            const redirectAfter = @json(route('pesanan.show', $pesanan->pesanan_id));
                            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                            function enable(v){ btn.disabled = !v; btn.classList.toggle('btn-disabled', !v); }

                            btn.addEventListener('click', async function(){
                                try {
                                    enable(false);
                                    const res = await fetch(snapUrl, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': token,
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({ pesanan_id: pesananId })
                                    });
                                    const data = await res.json();
                                    if(!res.ok) { alert(data?.message || 'Gagal membuat transaksi'); enable(true); return; }
                                    if(!(window.snap && data.token)) { alert('Snap tidak tersedia'); enable(true); return; }
                                    window.snap.pay(data.token, {
                                        onSuccess: function(){ window.location.href = redirectAfter; },
                                        onPending: function(){ window.location.href = redirectAfter; },
                                        onError: function(){ alert('Pembayaran gagal atau dibatalkan.'); enable(true); },
                                        onClose: function(){ enable(true); }
                                    });
                                } catch(e) {
                                    alert('Terjadi kesalahan.');
                                    enable(true);
                                }
                            });
                        })();
                    </script>
                @endif

                @if(in_array($pesanan->status, ['menunggu_pembayaran','menunggu_konfirmasi']) && $pesanan->metode_pembayaran === 'cod')
                    @php($pickup = (bool) data_get($pesanan->alamat_snapshot, 'pickup', false))
                    <div class="alert alert-info mt-4">
                        @if($pickup)
                            Metode pembayaran COD (Ambil di tempat). Silakan lakukan pembayaran saat pengambilan.
                        @else
                            Metode pembayaran COD. Silakan siapkan pembayaran saat pesanan diantarkan.
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
