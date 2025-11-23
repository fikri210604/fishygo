<?php

namespace App\Services;

use App\Models\Produk;
use Illuminate\Support\Facades\Cookie;

class CartService
{
    public function get(): array
    {
        $cart = session('cart');
        if (!is_array($cart) || empty($cart)) {
            $raw = request()->cookie('cart_persist');
            if ($raw) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $cart = $decoded;
                    session(['cart' => $cart]);
                }
            }
        }
        return is_array($cart) ? $cart : [];
    }

    public function put(array $cart): void
    {
        session(['cart' => $cart]);
        Cookie::queue(cookie('cart_persist', json_encode($cart), 60 * 24 * 30)); // 30 hari
    }

    public function clear(): void
    {
        session()->forget('cart');
        Cookie::queue(Cookie::forget('cart_persist'));
    }

    public function summary(array $cart): array
    {
        if (empty($cart)) {
            return ['items' => [], 'total' => 0, 'qty_total' => 0, 'berat_total_gram' => 0];
    }
        $produkIds = array_keys($cart);
        $produks = Produk::whereIn('produk_id', $produkIds)->get()->keyBy('produk_id');

        $items = [];
        $total = 0;
        $qtyTotal = 0;
        $beratTotal = 0;
        foreach ($cart as $produkId => $row) {
            $produk = $produks->get($produkId);
            if (!$produk) { continue; }
            $qty = max(1, (int) ($row['qty'] ?? 1));
            $harga = (float) $produk->hargaEfektif();
            $subtotal = $harga * $qty;
            $items[] = compact('produk', 'qty', 'harga', 'subtotal');
            $total += $subtotal;
            $qtyTotal += $qty;
            $beratTotal += (int) ($produk->berat_gram ?? 0) * $qty;
        }
        return ['items' => $items, 'total' => $total, 'qty_total' => $qtyTotal, 'berat_total_gram' => $beratTotal];
    }
}
