<?php

namespace App\Services\Pembayaran;

use App\Models\Pembayaran;

class PembayaranManualService
{
    public function upload(Pembayaran $pay, $file)
    {
        $path = $file->store('bukti-transfer', 'public');
        $payload = is_array($pay->gateway_payload) ? $pay->gateway_payload : [];

        $payload['manual_proof_path'] = $path;
        $payload['manual_proof_uploaded_at'] = now();

        $pay->gateway_payload = $payload;
        $pay->save();

        return $path;
    }

    public function confirm(Pembayaran $pay)
    {
        $pay->status = 'paid';
        $pay->dibayar_pada = now();
        $pay->gateway = 'manual';
        $pay->channel = 'transfer';
        $pay->save();
    }

    public function reject(Pembayaran $pay, string $reason)
    {
        $payload = is_array($pay->gateway_payload) ? $pay->gateway_payload : [];
        $payload['manual_reject_reason'] = $reason;
        $payload['manual_rejected_at'] = now();

        $pay->gateway_payload = $payload;
        $pay->status = 'rejected';
        $pay->save();
    }
}
