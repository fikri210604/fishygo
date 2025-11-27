<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Pembayaran;
use Midtrans\Snap;
use Midtrans\Notification;
use App\Services\PesananService;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    private function clientKey(): ?string
    {
        return config('midtrans.client_key');
    }

    // Snap token untuk Midtrans (user harus login)
    public function midtransSnap(Request $request)
    {
        try {
            $user = $request->user();
            $pesananId = (string) $request->input('pesanan_id');
            if (!$pesananId) return response()->json(['message' => 'pesanan_id wajib diisi'], 422);

            $pesanan = Pesanan::where('pesanan_id', $pesananId)->first();
            if (!$pesanan) return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
            if ($pesanan->pengguna_id !== $user->id) return response()->json(['message' => 'Tidak berhak'], 403);

            if (!config('midtrans.server_key')) return response()->json(['message' => 'Konfigurasi Midtrans belum lengkap'], 500);

            // Pastikan ada catatan pembayaran
            $pay = Pembayaran::firstOrCreate(
                ['pesanan_id' => $pesanan->pesanan_id],
                [
                    'gateway' => 'midtrans',
                    'channel' => 'snap',
                    'amount' => $pesanan->total,
                    'status' => 'pending',
                    'reference' => $pesanan->kode_pesanan,
                    'order_id' => $pesanan->kode_pesanan,
                ]
            );

            $payload = [
                'transaction_details' => [
                    'order_id' => $pay->order_id,
                    'gross_amount' => (int) round((float) $pesanan->total),
                ],
                'customer_details' => [
                    'first_name' => $user->nama ?: $user->username,
                    'email' => $user->email,
                ],
            ];

            $snap = Snap::createTransaction($payload);
            $token = $snap['token'] ?? null;
            $redirect = $snap['redirect_url'] ?? null;

            $pay->gateway = 'midtrans';
            $pay->channel = 'snap';
            $pay->status = 'pending';
            $pay->gateway_payload = ['snap_token' => $token, 'redirect_url' => $redirect];
            $pay->save();

            return response()->json([
                'token' => $token,
                'redirect_url' => $redirect,
                'client_key' => $this->clientKey(),
            ]);
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) $this->logException($e, ['action' => 'pembayaran.midtransSnap']);
            return response()->json(['message' => 'Gagal membuat Snap token'], 500);
        }
    }

    // Webhook Midtrans (notifikasi pembayaran)
    public function midtransNotification(Request $request)
    {
        try {
            if (!config('midtrans.server_key')) return response()->json(['message' => 'Konfigurasi Midtrans belum lengkap'], 500);

            $notif = new Notification();
            $orderId = (string) ($notif->order_id ?? '');
            $txStatus = (string) ($notif->transaction_status ?? '');

            $pay = Pembayaran::where('order_id', $orderId)->first();
            if (!$pay) $pay = Pembayaran::where('reference', $orderId)->first();
            if (!$pay) return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);

            $map = [
                'settlement' => 'paid',
                'capture' => 'paid',
                'pending' => 'pending',
                'cancel' => 'cancelled',
                'deny' => 'cancelled',
                'expire' => 'expired',
                'refund' => 'refunded',
                'partial_refund' => 'refunded',
            ];
            $status = $map[$txStatus] ?? $txStatus;

            $pay->status = $status;
            if ($status === 'paid' && empty($pay->dibayar_pada)) $pay->dibayar_pada = now();
            // simpan raw response untuk audit
            $existing = is_array($pay->gateway_payload) ? $pay->gateway_payload : [];
            $pay->gateway_payload = array_merge($existing, ['notification' => $notif->getResponse()]);
            $pay->save();

            return response()->json(['message' => 'OK']);
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) $this->logException($e, ['action' => 'pembayaran.midtransNotification']);
            return response()->json(['message' => 'Gagal memproses notifikasi'], 500);
        }
    }

    // Konfirmasi COD oleh Admin: tandai pembayaran COD sebagai 'paid'
    public function codConfirm(Request $request, Pesanan $pesanan)
    {
        $this->authorize('access-admin');
        try {
            if ($pesanan->metode_pembayaran !== 'cod') {
                return back()->with('error', 'Metode pembayaran bukan COD.');
            }
            $pay = Pembayaran::where('pesanan_id', $pesanan->pesanan_id)->first();
            if (!$pay) {
                return back()->with('error', 'Data pembayaran tidak ditemukan.');
            }
            $pay->status = 'paid';
            if (empty($pay->dibayar_pada)) $pay->dibayar_pada = now();
            $pay->save();
            return back()->with('success', 'Pembayaran COD dikonfirmasi.');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) $this->logException($e, ['action' => 'pembayaran.codConfirm', 'pesanan_id' => $pesanan->pesanan_id]);
            return back()->with('error', 'Gagal mengonfirmasi COD.');
        }
    }

    // Batalkan pesanan COD oleh Admin
    public function codCancel(Request $request, Pesanan $pesanan, PesananService $service)
    {
        $this->authorize('access-admin');
        try {
            $reason = (string) $request->input('reason', 'cod_cancel');
            $note = (string) $request->input('note', '');
            $user = Auth::user();
            $service->cancel($pesanan, $user, $reason, $note);
            return back()->with('success', 'Pesanan COD dibatalkan.');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) $this->logException($e, ['action' => 'pembayaran.codCancel', 'pesanan_id' => $pesanan->pesanan_id]);
            return back()->with('error', 'Gagal membatalkan pesanan COD.');
        }
    }
}
