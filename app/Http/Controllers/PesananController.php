<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutStoreRequest;
use App\Http\Requests\OrderCancelRequest;
use App\Models\Alamat;
use App\Models\Pesanan;
use App\Models\User;
use App\Services\CartService;
use App\Services\PesananService;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected PesananService $service,
    ) {}

    public function create(Request $request)
    {
        try {
            $user = $request->user();
            $cart = $this->cart->get();
            $summary = $this->cart->summary($cart);
            if (empty($summary['items'])) {
                return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
            }

            $alamats = $user->alamats()->get();
            $alamatTerpilih = $alamats->first();

            return view('checkout', [
                'items' => $summary['items'],
                'total' => $summary['total'],
                'qty_total' => $summary['qty_total'],
                'berat_total_gram' => $summary['berat_total_gram'],
                'alamats' => $alamats,
                'alamatTerpilih' => $alamatTerpilih,
            ]);
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'PesananController@create']);
            return back()->with('error', $this->errorMessage($e, 'Gagal memuat checkout.'));
        }
    }

    public function store(CheckoutStoreRequest $request)
    {
        $user = $request->user();
        try {
            if (method_exists($user, 'isProfileComplete') && !$user->isProfileComplete()) {
                return redirect()->route('profile.edit')
                    ->with('error', 'Lengkapi profil dan alamat sebelum melakukan checkout.');
            }
            $cart = $this->cart->get();
            $summary = $this->cart->summary($cart);
            if (empty($summary['items'])) {
                return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
            }
            $alamat = null;
            if ($request->filled('alamat_id')) {
                $alamat = Alamat::where('id', $request->input('alamat_id'))
                    ->where('pengguna_id', $user->id)->first();
            }
            if (!$alamat) {
                $alamat = $user->alamats()->first();
            }
            if (!$alamat) {
                return back()->with('error', 'Alamat belum diatur.');
            }
            $pesanan = $this->service->createFromCart($user, $alamat, $summary, [
                'metode_pembayaran' => $request->input('metode_pembayaran', 'manual'),
                'catatan' => $request->input('catatan'),
                'pickup' => $request->boolean('pickup'),
                // transfer manual metadata
                'manual_bank' => $request->input('manual_bank'),
            ]);
            $this->cart->clear();
            // If the client expects JSON (AJAX flow), return JSON response
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'ok' => true,
                    'pesanan_id' => $pesanan->pesanan_id,
                    'metode' => $pesanan->metode_pembayaran,
                    'redirect' => route('pesanan.show', ['pesanan' => $pesanan->pesanan_id]),
                ]);
            }
            // Default: redirect to order detail page
            return redirect()->route('pesanan.show', ['pesanan' => $pesanan->pesanan_id])
                ->with('success', 'Pesanan berhasil dibuat.');
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'PesananController@store']);
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => $this->errorMessage($e, 'Gagal membuat pesanan.'),
                ], 500);
            }
            return back()->with('error', $this->errorMessage($e, 'Gagal membuat pesanan.'));
        }
    }

    public function show(Request $request, Pesanan $pesanan)
    {
        $user = $request->user();
        abort_unless($pesanan->pengguna_id === $user->id, 403);
        $pesanan->load(['items.produk', 'pembayaran']);
        return view('pesanan.show', compact('pesanan'));
    }

    public function history(Request $request)
    {
        $user = $request->user();
        $status = $request->input('status');
        $q = trim((string) $request->input('q', ''));

        $query = Pesanan::query()->untukUser($user->id)->orderByDesc('created_at');
        if ($status) {
            $query->where('status', $status);
        }
        if ($q !== '') {
            $query->where('kode_pesanan', 'like', "%{$q}%");
        }
        $items = $query->paginate(10)->withQueryString();

        return view('pesanan.history', compact('items','status','q'));
    }

    public function cancel(OrderCancelRequest $request, Pesanan $pesanan)
    {
        $user = $request->user();
        abort_unless($pesanan->pengguna_id === $user->id, 403);
        try {
            $this->service->cancel($pesanan, $user, $request->input('reason'), $request->input('note'));
            return redirect()->route('pesanan.show', $pesanan->pesanan_id)
                ->with('success', 'Pesanan berhasil dibatalkan.');
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'PesananController@cancel', 'pesanan_id' => $pesanan->pesanan_id]);
            return back()->with('error', $this->errorMessage($e, 'Gagal membatalkan pesanan.'));
        }
    }

}
