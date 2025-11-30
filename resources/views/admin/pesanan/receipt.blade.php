<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $pesanan->kode_pesanan }}</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'; font-size: 12px; }
        .wrap { max-width: 360px; margin: 0 auto; padding: 12px; }
        .title { text-align: center; margin-bottom: 8px; font-weight: 700; }
        .muted { color: #555; }
        .row { display: flex; justify-content: space-between; margin: 2px 0; }
        .hr { border-top: 1px dashed #aaa; margin: 8px 0; }
        .tot { font-weight: 700; }
        @media print { .noprint { display: none; } }
    </style>
    <script>
        window.addEventListener('load', function(){
            if (window.location.hash === '#print') { window.print(); }
        });
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="wrap">
        <div class="title">Struk Pembayaran</div>
        <div class="row"><span>Kode</span><span>{{ $pesanan->kode_pesanan }}</span></div>
        <div class="row"><span>Tanggal</span><span>{{ now()->format('d/m/Y H:i') }}</span></div>
        <div class="row"><span>Pelanggan</span><span>{{ $pesanan->user?->nama ?? '-' }}</span></div>
        <div class="row"><span>Metode</span><span>{{ $pay?->gateway ?? 'cod' }}{{ $pay?->channel? ' - '.$pay->channel : '' }}</span></div>
        @if($pay && $pay->dibayar_pada)
            <div class="row"><span>Dibayar</span><span>{{ optional($pay->dibayar_pada)->format('d/m/Y H:i') }}</span></div>
        @endif
        @if($pay && $pay->paidBy)
            <div class="row"><span>Kasir</span><span>{{ $pay->paidBy->nama ?? $pay->paidBy->email }}</span></div>
        @endif
        <div class="hr"></div>

        @foreach($pesanan->items as $it)
            <div class="row">
                <span>{{ $it->nama_produk_snapshot }} x{{ $it->qty }}</span>
                <span>Rp {{ number_format($it->subtotal, 0, ',', '.') }}</span>
            </div>
        @endforeach

        <div class="hr"></div>
        <div class="row"><span>Subtotal</span><span>Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</span></div>
        <div class="row"><span>Ongkir</span><span>Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</span></div>
        <div class="row"><span>Diskon</span><span>Rp {{ number_format($pesanan->diskon, 0, ',', '.') }}</span></div>
        <div class="row tot"><span>Total</span><span>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</span></div>

        <div class="hr"></div>
        <div class="row"><span class="muted">Terima kasih</span><span class="muted">FishyGo</span></div>

        <div class="noprint" style="margin-top:10px; text-align:center">
            <a href="#" onclick="window.print(); return false;" style="padding:6px 10px; border:1px solid #888; border-radius:6px; text-decoration:none;">Cetak</a>
        </div>
    </div>
</body>
</html>
