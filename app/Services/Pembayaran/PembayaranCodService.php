<?php

namespace App\Services\Pembayaran;

use App\Models\Pesanan;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;

class PembayaranCodService
{
    public function confirm(Pesanan $pesanan)
    {
        $pay = Pembayaran::where('pesanan_id', $pesanan->pesanan_id)->firstOrFail();

        $pay->status = 'paid';
        $pay->dibayar_pada = now();
        $pay->gateway = 'cod';
        $pay->channel = 'cod';
        $pay->paid_by_id = Auth::id();
        $pay->save();

        if (in_array($pesanan->status, ['menunggu_pembayaran', 'menunggu_konfirmasi'])) {
            $pickup = (bool) data_get($pesanan->alamat_snapshot, 'pickup', false);
            $pesanan->status = $pickup ? 'siap_diambil' : 'diproses';
            $pesanan->save();

            if (!$pickup) {
                $pesanan->ensurePengirimanForDelivery();
            }
        }
    }
}
