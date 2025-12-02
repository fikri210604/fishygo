<?php

namespace App\Helpers;

class MidtransStatusMapper
{
    public static function map(string $txStatus): string
    {
        return [
            'settlement'     => 'paid',
            'capture'        => 'paid',
            'pending'        => 'pending',
            'cancel'         => 'cancelled',
            'deny'           => 'cancelled',
            'expire'         => 'expired',
            'refund'         => 'refunded',
            'partial_refund' => 'refunded',
        ][$txStatus] ?? $txStatus;
    }
}
