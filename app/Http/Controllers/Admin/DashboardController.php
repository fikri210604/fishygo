<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pembayaran;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUser = User::count();
        $totalOrder = \App\Models\Pesanan::count();
        $totalPenjualan = \App\Models\Pesanan::where('status', 'selesai')->sum('total');
        $totalPembatalan = \App\Models\Pesanan::where('status', 'dibatalkan')->count();

        // Get order data for the past 30 days
        $orderData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $count = \App\Models\Pesanan::whereDate('created_at', $date)->count();
            $orderData[] = [
                'date' => $date,
                'count' => $count
            ];
        }

        return view('admin.dashboard', compact('totalUser', 'totalOrder', 'totalPenjualan', 'totalPembatalan', 'orderData'));
    }
}
