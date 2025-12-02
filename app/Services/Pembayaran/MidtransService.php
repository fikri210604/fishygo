<?php

namespace App\Services\Pembayaran;

use App\Models\Pesanan;
use App\Models\Pembayaran;
use Midtrans\Snap;
use Midtrans\Notification;
use Midtrans\Transaction;
use App\Helpers\MidtransStatusMapper;
use Illuminate\Support\Str;
use App\Services\Pembayaran\StatusPembayaranService;

class MidtransService
{
    protected StatusPembayaranService $status;

    public function __construct(StatusPembayaranService $status)
    {
        $this->status = $status;
    }

    public function createSnapToken($user, Pesanan $pesanan, $request)
    {
        $pay = Pembayaran::firstOrCreate(
            ['pesanan_id' => $pesanan->pesanan_id],
            [
                'gateway' => 'midtrans',
                'channel' => 'snap',
                'amount' => $pesanan->total,
                'status' => 'pending',
                'order_id' => $pesanan->kode_pesanan,
                'reference' => $pesanan->kode_pesanan,
            ]
        );

        // Reuse token or refresh
        $existing = is_array($pay->gateway_payload) ? $pay->gateway_payload : [];
        $token = $existing['snap_token'] ?? null;
        $redirect = $existing['redirect_url'] ?? null;

        if (!$token || !$redirect) {
            $payload = $this->buildPayload($user, $pesanan, $pay, $request);
            try {
                $snap = Snap::createTransaction($payload);
                $token = $snap->token;
                $redirect = $snap->redirect_url;
            } catch (\Throwable $e) {
                $isDuplicate = false;
                $msg = (string) $e->getMessage();
                if (str_contains($msg, 'order_id') && str_contains($msg, 'already been taken')) {
                    $isDuplicate = true;
                } else {
                    try {
                        $s = Transaction::status($pay->order_id);
                        if (isset($s->transaction_status)) {
                            $isDuplicate = true;
                        }
                    } catch (\Throwable $e2) {
                        // ignore
                    }
                }
                if ($isDuplicate) {
                    $pay->order_id = $pesanan->kode_pesanan . '-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(4));
                    $pay->save();
                    $payload['transaction_details']['order_id'] = $pay->order_id;
                    $snap = Snap::createTransaction($payload);
                    $token = $snap->token;
                    $redirect = $snap->redirect_url;
                } else {
                    throw $e;
                }
            }

            $pay->gateway = 'midtrans';
            $pay->channel = 'snap';
            $pay->status = 'pending';
            $pay->gateway_payload = [
                'snap_token' => $token,
                'redirect_url' => $redirect,
                'snap_token_created_at' => now()->toDateTimeString(),
            ];
            $pay->expiry_time = now()->addMinutes(30);
            $pay->save();
        }

        return [
            'token' => $token,
            'redirect_url' => $redirect,
        ];
    }

    private function buildPayload($user, Pesanan $pesanan, Pembayaran $pay, $request)
    {
        $host = rtrim($request->getSchemeAndHttpHost(), '/');
        // Notif URL: gunakan env override jika ada, fallback ke route relatif
        $configuredNotif = config('midtrans.notification_url');
        try {
            $relativeNotif = route('midtrans.notification', [], false);
        } catch (\Throwable $e) {
            $relativeNotif = '/api/midtrans/notification';
        }
        try {
            $finishPath = route('payment.midtrans.finish', [], false);
        } catch (\Throwable $e) {
            $finishPath = '/payment/midtrans/finish';
        }
        try {
            $unfinishPath = route('payment.midtrans.unfinish', [], false);
        } catch (\Throwable $e) {
            $unfinishPath = '/payment/midtrans/unfinish';
        }
        try {
            $errorPath = route('payment.midtrans.error', [], false);
        } catch (\Throwable $e) {
            $errorPath = '/payment/midtrans/error';
        }

        return [
            'transaction_details' => [
                'order_id' => $pay->order_id,
                'gross_amount' => (int) $pesanan->total,
            ],
            'customer_details' => [
                'first_name' => $user->nama ?? $user->username,
                'email' => $user->email,
            ],
            'notification_url' => $configuredNotif ?: ($host . $relativeNotif),
            'callbacks' => [
                'finish' => $host . $finishPath,
                'unfinish' => $host . $unfinishPath,
                'error' => $host . $errorPath,
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'minute',
                'duration' => 30,
            ],
        ];
    }

    public function handleNotification()
    {
        $notif = new Notification();

        $orderId = $notif->order_id;
        $txStatus = $notif->transaction_status;

        $pay = Pembayaran::where('order_id', $orderId)->first()
            ?? Pembayaran::where('reference', $orderId)->first();

        if (!$pay)
            return;

        $mapped = MidtransStatusMapper::map($txStatus);
        $this->status->apply($pay, $mapped, $notif->getResponse());
    }

    public function handleRedirect(Pembayaran $pay)
    {
        try {
            $resp = Transaction::status($pay->order_id);
            $status = MidtransStatusMapper::map($resp->transaction_status);
            $this->status->apply($pay, $status);
            return $status;
        } catch (\Throwable $e) {
            return 'pending';
        }
    }
}
