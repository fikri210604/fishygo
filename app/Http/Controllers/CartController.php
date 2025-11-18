<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Ambil data keranjang dari session.
     */
    protected function getCart(): array
    {
        $cart = session('cart', []);
        return is_array($cart) ? $cart : [];
    }

    /**
     * Simpan data keranjang ke session.
     */
    protected function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    /**
     * Tampilkan halaman keranjang.
     */
    public function index(Request $request)
    {
        try {
            $cart = $this->getCart();

            if (empty($cart)) {
                return view('keranjang', [
                    'items' => [],
                    'total' => 0,
                ]);
            }

            $produkIds = array_keys($cart);
            $produks = Produk::whereIn('produk_id', $produkIds)
                ->with('primaryPhoto')
                ->get()
                ->keyBy('produk_id');

            $items = [];
            $total = 0;

            foreach ($cart as $produkId => $row) {
                $produk = $produks->get($produkId);
                if (!$produk) {
                    continue;
                }

                $qty = max(1, (int) ($row['qty'] ?? 1));
                $harga = (float) $produk->hargaEfektif();
                $subtotal = $harga * $qty;
                $total += $subtotal;

                $items[] = [
                    'produk' => $produk,
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                ];
            }

            return view('keranjang', [
                'items' => $items,
                'total' => $total,
            ]);
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'CartController@index']);
            return back()->with('error', $this->errorMessage($e, 'Gagal memuat keranjang.'));
        }
    }

    /**
     * Tambah produk ke keranjang.
     */
    public function add(Request $request, Produk $produk)
    {
        try {
            $qty = max(1, (int) $request->input('qty', 1));

            $cart = $this->getCart();
            if (isset($cart[$produk->produk_id])) {
                $cart[$produk->produk_id]['qty'] += $qty;
            } else {
                $cart[$produk->produk_id] = ['qty' => $qty];
            }

            $this->saveCart($cart);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Produk ditambahkan ke keranjang.',
                ]);
            }

            return back()->with('success', 'Produk ditambahkan ke keranjang.');
        } catch (\Throwable $e) {
            $this->logException($e, [
                'action' => 'CartController@add',
                'produk_id' => $produk->produk_id ?? null,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $this->errorMessage($e, 'Gagal menambahkan ke keranjang.'),
                ], 500);
            }

            return back()->with('error', $this->errorMessage($e, 'Gagal menambahkan ke keranjang.'));
        }
    }

    /**
     * Update kuantitas produk di keranjang.
     */
    public function update(Request $request, Produk $produk)
    {
        try {
            $cart = $this->getCart();
            if (!isset($cart[$produk->produk_id])) {
                return back()->with('error', 'Produk tidak ditemukan di keranjang.');
            }

            $mode = $request->input('mode');
            $currentQty = max(1, (int) ($cart[$produk->produk_id]['qty'] ?? 1));

            if ($mode === 'inc') {
                $qty = $currentQty + 1;
            } elseif ($mode === 'dec') {
                $qty = max(1, $currentQty - 1);
            } else {
                $qty = max(1, (int) $request->input('qty', $currentQty));
            }

            $cart[$produk->produk_id]['qty'] = $qty;
            $this->saveCart($cart);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Kuantitas keranjang diperbarui.',
                ]);
            }

            return back()->with('success', 'Kuantitas keranjang diperbarui.');
        } catch (\Throwable $e) {
            $this->logException($e, [
                'action' => 'CartController@update',
                'produk_id' => $produk->produk_id ?? null,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $this->errorMessage($e, 'Gagal memperbarui keranjang.'),
                ], 500);
            }

            return back()->with('error', $this->errorMessage($e, 'Gagal memperbarui keranjang.'));
        }
    }

    /**
     * Hapus satu produk dari keranjang.
     */
    public function remove(Request $request, Produk $produk)
    {
        try {
            $cart = $this->getCart();
            unset($cart[$produk->produk_id]);
            $this->saveCart($cart);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Produk dihapus dari keranjang.',
                ]);
            }

            return back()->with('success', 'Produk dihapus dari keranjang.');
        } catch (\Throwable $e) {
            $this->logException($e, [
                'action' => 'CartController@remove',
                'produk_id' => $produk->produk_id ?? null,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $this->errorMessage($e, 'Gagal menghapus produk dari keranjang.'),
                ], 500);
            }

            return back()->with('error', $this->errorMessage($e, 'Gagal menghapus produk dari keranjang.'));
        }
    }

    /**
     * Kosongkan seluruh keranjang.
     */
    public function clear(Request $request)
    {
        try {
            session()->forget('cart');

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Keranjang dikosongkan.',
                ]);
            }

            return back()->with('success', 'Keranjang dikosongkan.');
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'CartController@clear']);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $this->errorMessage($e, 'Gagal mengosongkan keranjang.'),
                ], 500);
            }

            return back()->with('error', $this->errorMessage($e, 'Gagal mengosongkan keranjang.'));
        }
    }
}

