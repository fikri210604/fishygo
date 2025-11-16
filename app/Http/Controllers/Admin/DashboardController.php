<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUser = User::count();
        $totalOrder = 0;
        $totalPenjualan = 0;
        $totalPembatalan = 0;

        return view('admin.dashboard', compact('totalUser', 'totalOrder', 'totalPenjualan', 'totalPembatalan'));
    }
}
