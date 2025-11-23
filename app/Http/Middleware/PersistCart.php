<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PersistCart
{
    public function handle(Request $request, Closure $next)
    {
        $cart = $request->session()->get('cart');
        if (!is_array($cart) || empty($cart)) {
            $raw = $request->cookie('cart_persist');
            if ($raw) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $request->session()->put('cart', $decoded);
                }
            }
        }
        return $next($request);
    }
}

