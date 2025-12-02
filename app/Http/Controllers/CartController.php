<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }
    /**
     * Bangun ringkasan keranjang (items, total, dan count).
     */
    protected function buildCartSummary(array $cart): array
    {
        if (empty($cart)) {
            return [
                'items' => [],
                'total' => 0,
                'cart_count' => 0,
                'items_count' => 0,
            ];
        }

        $produkIds = array_keys($cart);
        $produks = Produk::whereIn('produk_id', $produkIds)
            ->with('primaryPhoto')
            ->get()
            ->keyBy('produk_id');

        $items = [];
        $total = 0;
        $cartCount = 0;

        foreach ($cart as $produkId => $row) {
            $produk = $produks->get($produkId);
            if (!$produk) {
                continue;
            }

            $qty = max(1, (int) ($row['qty'] ?? 1));
            $harga = (float) $produk->hargaEfektif();
            $subtotal = $harga * $qty;

            $total += $subtotal;
            $cartCount += $qty;

            $items[] = [
                'produk' => $produk,
                'qty' => $qty,
                'harga' => $harga,
                'subtotal' => $subtotal,
            ];
        }

        return [
            'items' => $items,
            'total' => $total,
            'cart_count' => $cartCount,
            'items_count' => count($items),
        ];
    }

    /**
     * Ambil data keranjang dari session.
     */
    protected function getCart(): array
    {
        return $this->cartService->get();
    }

    /**
     * Simpan data keranjang ke session.
     */
    protected function saveCart(array $cart): void
    {
        $this->cartService->put($cart);
    }

    /**
     * Tampilkan halaman keranjang.
     */
    public function index(Request $request)
    {
        try {
            $cart = $this->getCart();
            $summary = $this->buildCartSummary($cart);

            return view('keranjang', [
                'items' => $summary['items'],
                'total' => $summary['total'],
                'cart_count' => $summary['cart_count'],
                'items_count' => $summary['items_count'],
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
            $summary = $this->buildCartSummary($cart);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Produk ditambahkan ke keranjang.',
                    'cart_count' => $summary['cart_count'],
                    'items_count' => $summary['items_count'],
                    'total' => $summary['total'],
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
            $removed = false;

            if ($mode === 'inc') {
                $qty = $currentQty + 1;
                $cart[$produk->produk_id]['qty'] = $qty;
            } elseif ($mode === 'dec') {
                $qty = $currentQty - 1;
                if ($qty < 1) {
                    unset($cart[$produk->produk_id]);
                    $removed = true;
                } else {
                    $cart[$produk->produk_id]['qty'] = $qty;
                }
            } else {
                $qty = max(1, (int) $request->input('qty', $currentQty));
                $cart[$produk->produk_id]['qty'] = $qty;
            }

            $this->saveCart($cart);

            if ($request->expectsJson()) {
                $summary = $this->buildCartSummary($cart);
                $produkId = $produk->produk_id;
                $itemData = null;

                foreach ($summary['items'] as $item) {
                    if ($item['produk']->produk_id === $produkId) {
                        $itemData = [
                            'produk_id' => $produkId,
                            'qty' => $item['qty'],
                            'harga' => $item['harga'],
                            'subtotal' => $item['subtotal'],
                        ];
                        break;
                    }
                }
                $message = $removed ? 'Produk dihapus dari keranjang.' : 'Kuantitas keranjang diperbarui.';

                $response = [
                    'status' => 'success',
                    'message' => $message,
                    'cart_count' => $summary['cart_count'],
                    'items_count' => $summary['items_count'],
                    'total' => $summary['total'],
                    'item' => $removed ? null : $itemData,
                ];

                if ($removed) {
                    $response['removed_produk_id'] = $produkId;
                }

                return response()->json($response);
            }

            if ($removed) {
                return back()->with('success', 'Produk dihapus dari keranjang.');
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

    // Hapus satu produk dari keranjang.
    public function remove(Request $request, Produk $produk)
    {
        try {
            $cart = $this->getCart();
            $produkId = $produk->produk_id;
            unset($cart[$produkId]);
            $this->saveCart($cart);

            if ($request->expectsJson()) {
                $summary = $this->buildCartSummary($cart);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Produk dihapus dari keranjang.',
                    'cart_count' => $summary['cart_count'],
                    'items_count' => $summary['items_count'],
                    'total' => $summary['total'],
                    'removed_produk_id' => $produkId,
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

    public function clear(Request $request)
    {
        try {
            $this->cartService->clear();

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Keranjang dikosongkan.',
                    'cart_count' => 0,
                    'items_count' => 0,
                    'total' => 0,
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
