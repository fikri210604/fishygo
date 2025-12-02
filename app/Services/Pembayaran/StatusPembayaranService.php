<?php

namespace App\Services\Pembayaran;

use App\Models\Pembayaran;
use App\Helpers\MidtransStatusMapper;

class StatusPembayaranService
{
    public function apply(Pembayaran $pay, string $status, array $rawNotif = []): void
    {
        $pay->status = $status;

        if ($status === 'paid' && !$pay->dibayar_pada) {
            $pay->dibayar_pada = now();
        }

        $payload = is_array($pay->gateway_payload) ? $pay->gateway_payload : [];
        if ($rawNotif) {
            $payload['notification'] = $rawNotif;
        }

        $pay->gateway_payload = $payload;
        $pay->save();

        // Update pesanan
        if ($status === 'paid') {
            $pesanan = $pay->pesanan()->first();
            if ($pesanan && in_array($pesanan->status, ['menunggu_pembayaran', 'menunggu_konfirmasi'])) {

                $pickup = (bool) data_get($pesanan->alamat_snapshot, 'pickup', false);
                $pesanan->status = $pickup ? 'siap_diambil' : 'diproses';
                $pesanan->save();

                if (!$pickup) {
                    $pesanan->ensurePengirimanForDelivery();
                }
            }
        }
    }
}
