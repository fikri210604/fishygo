<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Pembayaran;
use Midtrans\Snap;
use Midtrans\Notification;
use App\Services\PesananService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

            if (!config('midtrans.server_key')) {
                Log::warning('Midtrans config incomplete: missing server_key', ['action' => 'pembayaran.midtransSnap']);
                return response()->json(['message' => 'Konfigurasi Midtrans belum lengkap'], 500);
            }

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

            // Reuse existing token if available
            $existingPayload = is_array($pay->gateway_payload) ? $pay->gateway_payload : [];
            $token = (string) ($existingPayload['snap_token'] ?? '');
            $redirect = (string) ($existingPayload['redirect_url'] ?? '');

            if (!$token) {
                $payload = [
                    'transaction_details' => [
                        'order_id' => $pay->order_id,
                        'gross_amount' => (int) round((float) $pesanan->total),
                    ],
                    'customer_details' => [
                        'first_name' => $user->nama ?: $user->username,
                        'email' => $user->email,
                    ],
                    // Ensure Midtrans knows where to send notifications
                    'notification_url' => route('midtrans.notification'),
                ];

                try {
                    $snap = Snap::createTransaction($payload);
                    $token = $snap->token ?? null;
                    $redirect = $snap->redirect_url ?? null;
                } catch (\Throwable $e) {
                    // Handle duplicate order_id by regenerating a suffix once
                    $msg = strtolower($e->getMessage());
                    if (str_contains($msg, 'order id') && str_contains($msg, 'used')) {
                        $pay->order_id = $pesanan->kode_pesanan . '-' . Str::upper(Str::random(4));
                        $pay->save();
                        $payload['transaction_details']['order_id'] = $pay->order_id;
                        $snap = Snap::createTransaction($payload);
                        $token = $snap->token ?? null;
                        $redirect = $snap->redirect_url ?? null;
                    } else {
                        throw $e;
                    }
                }
            }

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
            return response()->json(['message' => $this->errorMessage($e, 'Gagal membuat Snap token')], 500);
        }
    }

    // Webhook Midtrans (notifikasi pembayaran)
    public function midtransNotification(Request $request)
    {
        try {
            if (!config('midtrans.server_key')) {
                Log::warning('Midtrans config incomplete: missing server_key', ['action' => 'pembayaran.midtransNotification']);
                return response()->json(['message' => 'Konfigurasi Midtrans belum lengkap'], 500);
            }

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

            // Update status pesanan ketika pembayaran lunas
            if ($status === 'paid') {
                $pesanan = $pay->pesanan()->first();
                if ($pesanan && in_array($pesanan->status, ['menunggu_pembayaran','menunggu_konfirmasi'], true)) {
                    $pickup = (bool) data_get($pesanan->alamat_snapshot, 'pickup', false);
                    $pesanan->status = $pickup ? 'siap_diambil' : 'diproses';
                    $pesanan->save();
                }
            }

            \Log::info('Midtrans notification processed', ['order_id' => $orderId, 'tx_status' => $txStatus, 'mapped' => $status]);
            return response()->json(['message' => 'OK']);
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) $this->logException($e, ['action' => 'pembayaran.midtransNotification']);
            return response()->json(['message' => 'Gagal memproses notifikasi'], 500);
        }
    }

    // Midtrans redirect Finish (user browser)
    public function midtransFinish(Request $request)
    {
        $orderId = (string) $request->query('order_id', '');
        $txStatus = (string) $request->query('transaction_status', '');
        $pay = Pembayaran::where('order_id', $orderId)->first();
        if (!$pay) $pay = Pembayaran::where('reference', $orderId)->first();
        if ($pay) {
            $pesanan = $pay->pesanan()->first();
            if ($pesanan) {
                $msg = $txStatus === 'settlement' || $txStatus === 'capture' ? 'Pembayaran berhasil.' : 'Transaksi diproses.';
                return redirect()->route('pesanan.show', $pesanan->pesanan_id)->with('success', $msg);
            }
        }
        return redirect()->route('pesanan.history')->with('info', 'Transaksi selesai diproses.');
    }

    // Midtrans redirect Unfinish (user closes or pending)
    public function midtransUnfinish(Request $request)
    {
        $orderId = (string) $request->query('order_id', '');
        $pay = Pembayaran::where('order_id', $orderId)->first();
        if (!$pay) $pay = Pembayaran::where('reference', $orderId)->first();
        if ($pay) {
            $pesanan = $pay->pesanan()->first();
            if ($pesanan) {
                return redirect()->route('pesanan.show', $pesanan->pesanan_id)->with('info', 'Transaksi belum selesai.');
            }
        }
        return redirect()->route('pesanan.history')->with('info', 'Transaksi belum selesai.');
    }

    // Midtrans redirect Error
    public function midtransError(Request $request)
    {
        $orderId = (string) $request->query('order_id', '');
        $pay = Pembayaran::where('order_id', $orderId)->first();
        if (!$pay) $pay = Pembayaran::where('reference', $orderId)->first();
        if ($pay) {
            $pesanan = $pay->pesanan()->first();
            if ($pesanan) {
                return redirect()->route('pesanan.show', $pesanan->pesanan_id)->with('error', 'Terjadi kesalahan pada pembayaran.');
            }
        }
        return redirect()->route('pesanan.history')->with('error', 'Terjadi kesalahan pada pembayaran.');
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
            if ($pay->status === 'paid') {
                return back()->with('info', 'Pembayaran sudah dikonfirmasi sebelumnya.');
            }
            if (in_array($pesanan->status, ['dibatalkan'], true)) {
                return back()->with('error', 'Pesanan sudah dibatalkan.');
            }
            $pay->status = 'paid';
            if (empty($pay->dibayar_pada)) $pay->dibayar_pada = now();
            // tandai gateway/channel bila belum
            if (empty($pay->gateway)) $pay->gateway = 'cod';
            if (empty($pay->channel)) $pay->channel = 'cod';
            $pay->paid_by_id = Auth::id();
            $pay->save();

            // Ubah status pesanan setelah lunas COD
            if (in_array($pesanan->status, ['menunggu_pembayaran','menunggu_konfirmasi'], true)) {
                $pickup = (bool) data_get($pesanan->alamat_snapshot, 'pickup', false);
                $pesanan->status = $pickup ? 'siap_diambil' : 'diproses';
                $pesanan->save();
            }
            return back()->with('success', 'Pembayaran COD dikonfirmasi.');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) $this->logException($e, ['action' => 'pembayaran.codConfirm', 'pesanan_id' => $pesanan->pesanan_id]);
            return back()->with('error', 'Gagal mengonfirmasi COD.');
        }
    }

    // Upload bukti transfer manual oleh user
    public function manualUpload(Request $request, Pesanan $pesanan)
    {
        $user = $request->user();
        abort_unless($pesanan->pengguna_id === $user->id, 403);
        if ($pesanan->metode_pembayaran !== 'manual') {
            return back()->with('error', 'Metode pembayaran bukan transfer manual.');
        }
        $request->validate([
            'bukti' => 'required|image|max:5120', // 5MB
        ]);
        $pay = Pembayaran::where('pesanan_id', $pesanan->pesanan_id)->first();
        if (!$pay) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }
        try {
            $path = $request->file('bukti')->store('bukti-transfer', 'public');
            $existing = is_array($pay->gateway_payload) ? $pay->gateway_payload : [];
            $pay->gateway_payload = array_merge($existing, [
                'manual_proof_path' => $path,
                'manual_proof_uploaded_at' => now()->toDateTimeString(),
            ]);
            $pay->save();
            return redirect()->route('pesanan.show', $pesanan->pesanan_id)
                ->with('success', 'Bukti transfer berhasil diunggah. Menunggu validasi admin.');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) $this->logException($e, ['action' => 'pembayaran.manualUpload']);
            return back()->with('error', 'Gagal mengunggah bukti transfer.');
        }
    }

    // Konfirmasi Transfer Manual oleh Admin
    public function manualConfirm(Request $request, Pesanan $pesanan)
    {
        $this->authorize('access-admin');
        try {
            if ($pesanan->metode_pembayaran !== 'manual') {
                return back()->with('error', 'Metode pembayaran bukan transfer manual.');
            }
            $pay = Pembayaran::where('pesanan_id', $pesanan->pesanan_id)->first();
            if (!$pay) {
                return back()->with('error', 'Data pembayaran tidak ditemukan.');
            }
            if ($pay->status === 'paid') {
                return back()->with('info', 'Pembayaran sudah dikonfirmasi.');
            }
            if (in_array($pesanan->status, ['dibatalkan'], true)) {
                return back()->with('error', 'Pesanan sudah dibatalkan.');
            }
            $pay->status = 'paid';
            if (empty($pay->dibayar_pada)) $pay->dibayar_pada = now();
            if (empty($pay->gateway)) $pay->gateway = 'manual';
            if (empty($pay->channel)) $pay->channel = 'transfer';
            $pay->paid_by_id = Auth::id();
            $pay->save();

            if (in_array($pesanan->status, ['menunggu_pembayaran','menunggu_konfirmasi'], true)) {
                $pickup = (bool) data_get($pesanan->alamat_snapshot, 'pickup', false);
                $pesanan->status = $pickup ? 'siap_diambil' : 'diproses';
                $pesanan->save();
            }
            return back()->with('success', 'Pembayaran transfer manual dikonfirmasi.');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) $this->logException($e, ['action' => 'pembayaran.manualConfirm', 'pesanan_id' => $pesanan->pesanan_id]);
            return back()->with('error', 'Gagal mengonfirmasi pembayaran.');
        }
    }

    // Tolak Transfer Manual oleh Admin (minta alasan)
    public function manualReject(Request $request, Pesanan $pesanan)
    {
        $this->authorize('access-admin');
        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);
        try {
            if ($pesanan->metode_pembayaran !== 'manual') {
                return back()->with('error', 'Metode pembayaran bukan transfer manual.');
            }
            $pay = Pembayaran::where('pesanan_id', $pesanan->pesanan_id)->first();
            if (!$pay) {
                return back()->with('error', 'Data pembayaran tidak ditemukan.');
            }
            // tandai rejected dan simpan alasan
            $existing = is_array($pay->gateway_payload) ? $pay->gateway_payload : [];
            $pay->gateway_payload = array_merge($existing, [
                'manual_reject_reason' => $data['reason'],
                'manual_rejected_at' => now()->toDateTimeString(),
            ]);
            $pay->status = 'rejected';
            $pay->save();
            // Pesanan tetap menunggu pembayaran
            return back()->with('success', 'Bukti pembayaran ditolak. User dapat upload ulang.');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) $this->logException($e, ['action' => 'pembayaran.manualReject', 'pesanan_id' => $pesanan->pesanan_id]);
            return back()->with('error', 'Gagal menolak pembayaran.');
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

    // Cetak struk sederhana untuk pesanan (admin kasir)
    public function receipt(Request $request, Pesanan $pesanan)
    {
        $this->authorize('access-admin');
        $pesanan->load(['user', 'items', 'pembayaran']);
        $pay = $pesanan->pembayaran->first();
        return view('admin.pesanan.receipt', compact('pesanan', 'pay'));
    }

    // Cetak struk untuk user (pemilik pesanan saja)
    public function receiptUser(Request $request, Pesanan $pesanan)
    {
        $user = $request->user();
        if (!$user || $pesanan->pengguna_id !== $user->id) {
            abort(403);
        }
        $pesanan->load(['user', 'items', 'pembayaran']);
        $pay = $pesanan->pembayaran->first();
        return view('admin.pesanan.receipt', compact('pesanan', 'pay'));
    }
}
