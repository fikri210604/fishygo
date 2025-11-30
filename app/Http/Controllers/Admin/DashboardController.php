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
        $totalOrder = Pembayaran::count();
        $totalPenjualan = Pembayaran::where('status', 'paid')->sum('amount');
        $totalPembatalan = Pembayaran::where('status', 'cancelled')->count();

        return view('admin.dashboard', compact('totalUser', 'totalOrder', 'totalPenjualan', 'totalPembatalan'));
    }
}
