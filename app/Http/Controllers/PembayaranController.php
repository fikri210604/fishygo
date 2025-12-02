<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Pembayaran;
use App\Services\Pembayaran\MidtransService;
use App\Services\Pembayaran\PembayaranCodService;
use App\Services\Pembayaran\PembayaranManualService;
use App\Services\Pembayaran\StatusPembayaranService;
use App\Services\PesananService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PembayaranController extends Controller
{
    private function clientKey(): ?string
    {
        return config('midtrans.client_key');
    }

    // Snap token untuk Midtrans (user harus login)
    public function midtransSnap(Request $request, MidtransService $midtrans)
    {
        try {
            $user = $request->user();
            $pesananId = (string) $request->input('pesanan_id');
            if (!$pesananId)
                return response()->json(['message' => 'pesanan_id wajib diisi'], 422);

            $pesanan = Pesanan::where('pesanan_id', $pesananId)->first();
            if (!$pesanan)
                return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
            if ($pesanan->pengguna_id !== $user->id)
                return response()->json(['message' => 'Tidak berhak'], 403);
            if (!in_array($pesanan->status, ['menunggu_pembayaran', 'menunggu_konfirmasi'], true)) {
                return response()->json(['message' => 'Pesanan tidak dalam status menunggu pembayaran'], 422);
            }

            if (!config('midtrans.server_key')) {
                Log::warning('Midtrans config incomplete: missing server_key', ['action' => 'pembayaran.midtransSnap']);
                return response()->json(['message' => 'Konfigurasi Midtrans belum lengkap'], 500);
            }

            $result = $midtrans->createSnapToken($user, $pesanan, $request);
            return response()->json([
                'token' => $result['token'] ?? null,
                'redirect_url' => $result['redirect_url'] ?? null,
                'client_key' => $this->clientKey(),
            ]);
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException'))
                $this->logException($e, ['action' => 'pembayaran.midtransSnap']);
            return response()->json(['message' => $this->errorMessage($e, 'Gagal membuat Snap token')], 500);
        }
    }

    // Webhook Midtrans (notifikasi pembayaran)
    public function midtransNotification(Request $request, MidtransService $midtrans)
    {
        try {
            if (!config('midtrans.server_key')) {
                Log::warning('Midtrans config incomplete: missing server_key', ['action' => 'pembayaran.midtransNotification']);
                return response()->json(['message' => 'Konfigurasi Midtrans belum lengkap'], 500);
            }

            $midtrans->handleNotification();
            Log::info('Midtrans notification processed');
            return response()->json(['message' => 'OK']);
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException'))
                $this->logException($e, ['action' => 'pembayaran.midtransNotification']);
            return response()->json(['message' => 'Gagal memproses notifikasi'], 500);
        }
    }

    // Midtrans redirect Finish (user browser)
    public function midtransFinish(Request $request, MidtransService $midtrans)
    {
        $orderId = (string) $request->query('order_id', '');
        $pay = Pembayaran::where('order_id', $orderId)->first();
        if (!$pay)
            $pay = Pembayaran::where('reference', $orderId)->first();
        if ($pay) {
            $status = $midtrans->handleRedirect($pay);
            $msg = $status === 'paid' ? 'Pembayaran berhasil.' : 'Transaksi diproses.';
            $type = $status === 'paid' ? 'success' : 'info';
            $pesanan = $pay->pesanan()->first();
            if ($pesanan) {
                return redirect()->route('pesanan.show', $pesanan->pesanan_id)->with($type, $msg);
            }
        }
        return redirect()->route('pesanan.history')->with('info', 'Transaksi selesai diproses.');
    }

    // Midtrans redirect Unfinish (user closes or pending)
    public function midtransUnfinish(Request $request)
    {
        $orderId = (string) $request->query('order_id', '');
        $pay = Pembayaran::where('order_id', $orderId)->first();
        if (!$pay)
            $pay = Pembayaran::where('reference', $orderId)->first();
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
        if (!$pay)
            $pay = Pembayaran::where('reference', $orderId)->first();
        if ($pay) {
            $pesanan = $pay->pesanan()->first();
            if ($pesanan) {
                return redirect()->route('pesanan.show', $pesanan->pesanan_id)->with('error', 'Terjadi kesalahan pada pembayaran.');
            }
        }
        return redirect()->route('pesanan.history')->with('error', 'Terjadi kesalahan pada pembayaran.');
    }

    // Konfirmasi COD oleh Admin: tandai pembayaran COD sebagai 'paid'
    public function codConfirm(Request $request, Pesanan $pesanan, PembayaranCodService $cod)
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
            $cod->confirm($pesanan);
            return back()->with('success', 'Pembayaran COD dikonfirmasi.');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException'))
                $this->logException($e, ['action' => 'pembayaran.codConfirm', 'pesanan_id' => $pesanan->pesanan_id]);
            return back()->with('error', 'Gagal mengonfirmasi COD.');
        }
    }

    // Upload bukti transfer manual oleh user
    public function manualUpload(Request $request, Pesanan $pesanan, PembayaranManualService $manual)
    {
        $user = $request->user();
        abort_unless($pesanan->pengguna_id === $user->id, 403);
        if ($pesanan->metode_pembayaran !== 'manual') {
            return back()->with('error', 'Metode pembayaran bukan transfer manual.');
        }
        $request->validate([
            'bukti' => 'required|image|max:5120',
        ]);
        $pay = Pembayaran::where('pesanan_id', $pesanan->pesanan_id)->first();
        if (!$pay) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }
        $manual->upload($pay, $request->file('bukti'));
        return back()->with('success', 'Bukti transfer berhasil diunggah. Menunggu verifikasi admin.');
    }

    // Konfirmasi Transfer Manual oleh Admin
    public function manualConfirm(Request $request, Pesanan $pesanan, PembayaranManualService $manual, StatusPembayaranService $statusService)
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
            $manual->confirm($pay);
            $statusService->apply($pay, 'paid');
            return back()->with('success', 'Pembayaran transfer manual dikonfirmasi.');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException'))
                $this->logException($e, ['action' => 'pembayaran.manualConfirm', 'pesanan_id' => $pesanan->pesanan_id]);
            return back()->with('error', 'Gagal mengonfirmasi pembayaran.');
        }
    }

    // Tolak Transfer Manual oleh Admin (minta alasan)
    public function manualReject(Request $request, Pesanan $pesanan, PembayaranManualService $manual)
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
            $manual->reject($pay, $data['reason']);
            return back()->with('success', 'Bukti pembayaran ditolak. User dapat upload ulang.');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException'))
                $this->logException($e, ['action' => 'pembayaran.manualReject', 'pesanan_id' => $pesanan->pesanan_id]);
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
            if (method_exists($this, 'logException'))
                $this->logException($e, ['action' => 'pembayaran.codCancel', 'pesanan_id' => $pesanan->pesanan_id]);
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
