<?php

namespace App\Http\Controllers\Kurir;

use App\Http\Controllers\Controller;
use App\Models\Pengiriman;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $status = (string) $request->query('status', '');
        $doneRange = (string) $request->query('done_range', 'today');
        $returnRange = (string) $request->query('return_range', 'today');
        $allowed = ['siap','diambil','diantar','diterima','dikembalikan'];
        $rangeAllowed = ['today','7d','30d'];

        // Metrics cards
        $totalAssigned = Pengiriman::where('assigned_kurir_id', $user->id)->count();
        $countAvailable = Pengiriman::whereNull('assigned_kurir_id')->where('status','siap')->count();
        $countDiambil = Pengiriman::where('assigned_kurir_id', $user->id)->where('status','diambil')->count();
        $countDiantar = Pengiriman::where('assigned_kurir_id', $user->id)->where('status','diantar')->count();
        $countReturned = Pengiriman::where('assigned_kurir_id', $user->id)->where('status','dikembalikan')->count();
        $countDone = Pengiriman::where('assigned_kurir_id', $user->id)->where('status','diterima')->count();

        // Done today
        // Per-card range for Selesai
        $doneRangeLabel = 'Hari Ini';
        $doneCountSelected = Pengiriman::where('assigned_kurir_id', $user->id)
            ->where('status','diterima')
            ->whereDate('diterima_pada', now()->toDateString())
            ->count();
        if (in_array($doneRange, $rangeAllowed, true)) {
            if ($doneRange === '7d') {
                $doneRangeLabel = '7 Hari';
                $from = now()->subDays(7)->startOfDay();
                $doneCountSelected = Pengiriman::where('assigned_kurir_id', $user->id)
                    ->where('status','diterima')
                    ->where('diterima_pada', '>=', $from)
                    ->count();
            } elseif ($doneRange === '30d') {
                $doneRangeLabel = '30 Hari';
                $from = now()->subDays(30)->startOfDay();
                $doneCountSelected = Pengiriman::where('assigned_kurir_id', $user->id)
                    ->where('status','diterima')
                    ->where('diterima_pada', '>=', $from)
                    ->count();
            }
        }

        // Per-card range for Dikembalikan (pakai updated_at)
        $returnRangeLabel = 'Hari Ini';
        $returnCountSelected = Pengiriman::where('assigned_kurir_id', $user->id)
            ->where('status','dikembalikan')
            ->whereDate('updated_at', now()->toDateString())
            ->count();
        if (in_array($returnRange, $rangeAllowed, true)) {
            if ($returnRange === '7d') {
                $returnRangeLabel = '7 Hari';
                $from = now()->subDays(7)->startOfDay();
                $returnCountSelected = Pengiriman::where('assigned_kurir_id', $user->id)
                    ->where('status','dikembalikan')
                    ->where('updated_at', '>=', $from)
                    ->count();
            } elseif ($returnRange === '30d') {
                $returnRangeLabel = '30 Hari';
                $from = now()->subDays(30)->startOfDay();
                $returnCountSelected = Pengiriman::where('assigned_kurir_id', $user->id)
                    ->where('status','dikembalikan')
                    ->where('updated_at', '>=', $from)
                    ->count();
            }
        }

        $items = Pengiriman::query()
            ->with(['pesanan.user', 'pesanan.alamat'])
            ->where(function ($q) use ($user) {
                $q->whereNull('assigned_kurir_id')
                  ->orWhere('assigned_kurir_id', $user->id);
            })
            ->when(in_array($status, $allowed, true), function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $grouped = null;
        if ($status === '') {
            $grouped = Pengiriman::groupCollectionByStatus(collect($items->items()));
        }

        return view('kurir.dashboard', compact(
            'items','status','grouped',
            'totalAssigned','countAvailable','countDiambil','countDiantar',
            'countReturned','countDone',
            'doneRange','doneRangeLabel','doneCountSelected',
            'returnRange','returnRangeLabel','returnCountSelected'
        ));
    }
}
