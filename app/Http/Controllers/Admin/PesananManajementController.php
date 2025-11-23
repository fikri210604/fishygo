<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PesananManajementController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $status = $request->input('status');

        $query = Pesanan::query()
            ->with(['user'])
            ->withCount('items')
            ->orderByDesc('created_at');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('kode_pesanan', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('nama', 'like', "%{$q}%")
                          ->orWhere('email', 'like', "%{$q}%")
                          ->orWhere('username', 'like', "%{$q}%");
                    });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $items = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => Pesanan::count(),
            'waiting' => Pesanan::status(Pesanan::STATUS_MENUNGGU_PEMBAYARAN)->count(),
            'cancelled' => Pesanan::status(Pesanan::STATUS_DIBATALKAN)->count(),
        ];

        return view('admin.pesanan.index', compact('items', 'q', 'status', 'counts'));
    }

    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['user', 'alamat', 'items.produk', 'pembayaran', 'pengiriman']);
        return view('admin.pesanan.show', compact('pesanan'));
    }
}

