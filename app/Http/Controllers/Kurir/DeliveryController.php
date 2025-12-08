<?php

namespace App\Http\Controllers\Kurir;

use App\Http\Controllers\Controller;
use App\Models\Pengiriman;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('access-kurir');

        $user = $request->user();
        $status = $request->query('status');
        $doneRange = (string) $request->query('done_range', 'today');
        $returnRange = (string) $request->query('return_range', 'today');
        $allowedStatus = ['siap', 'diambil', 'diantar', 'diterima', 'dikembalikan'];
        $rangeAllowed = ['today','7d','30d'];

        $statusOrder = ['siap', 'diambil', 'diantar', 'diterima', 'dikembalikan'];

        // Metrics cards
        $totalAssigned = Pengiriman::where('assigned_kurir_id', $user->id)->count();
        $countDiantar = Pengiriman::where('assigned_kurir_id', $user->id)->where('status','diantar')->count();

        // Done today
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

        $items = Pengiriman::with(['pesanan.user', 'pesanan.alamat'])
            ->where(function ($q) use ($user) {
                $q->whereNull('assigned_kurir_id')
                    ->orWhere('assigned_kurir_id', $user->id);
            })
            ->when(
                in_array($status, $allowedStatus),
                fn($q) =>
                $q->where('status', $status)
            )
            ->orderByRaw("CASE status
                WHEN 'siap' THEN 1
                WHEN 'diambil' THEN 2
                WHEN 'diantar' THEN 3
                WHEN 'diterima' THEN 4
                WHEN 'dikembalikan' THEN 5
                ELSE 6 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('kurir.pengiriman.index', compact(
            'items', 'status',
            'totalAssigned', 'countDiantar',
            'doneRange', 'doneRangeLabel', 'doneCountSelected',
            'returnRange', 'returnRangeLabel', 'returnCountSelected'
        ));
    }

    public function updateStatus(Request $request, Pengiriman $pengiriman)
    {
        $this->authorize('access-kurir');
        $user = Auth::user();
        if ($pengiriman->assigned_kurir_id && $pengiriman->assigned_kurir_id !== $user->id) {
            return back()->with('error', 'Pengiriman ini sudah ditugaskan ke kurir lain.');
        }
        $action = (string) $request->input('action');
        // Claim task if unassigned
        if (empty($pengiriman->assigned_kurir_id)) {
            $pengiriman->assigned_kurir_id = $user->id;
        }
        if ($action === 'pickup') {
            $pengiriman->status = 'diambil';
            $pengiriman->dikemas_pada = now();
        } elseif ($action === 'deliver') {
            $pengiriman->status = 'diantar';
            if (empty($pengiriman->dikirim_pada))
                $pengiriman->dikirim_pada = now();
            // update status pesanan ke 'dikirim'
            if ($pengiriman->pesanan && $pengiriman->pesanan->status !== Pesanan::STATUS_DIKIRIM) {
                $p = $pengiriman->pesanan;
                $p->status = Pesanan::STATUS_DIKIRIM;
                $p->save();
            }
        } elseif ($action === 'complete') {
            $pengiriman->status = 'diterima';
            $pengiriman->diterima_pada = now();
            if ($pengiriman->pesanan && $pengiriman->pesanan->status !== Pesanan::STATUS_SELESAI) {
                $p = $pengiriman->pesanan;
                $p->status = Pesanan::STATUS_SELESAI;
                $p->save();
            }
        } elseif ($action === 'return') {
            $pengiriman->status = 'dikembalikan';
            // Kembalikan status pesanan ke 'diproses' untuk penanganan admin
            if ($pengiriman->pesanan) {
                $p = $pengiriman->pesanan;
                $p->status = 'diproses';
                $p->save();
            }
        }
        $pengiriman->save();
        return back()->with('success', 'Status pengiriman diperbarui.');
    }

    public function show(Request $request, Pengiriman $pengiriman)
    {
        $pengiriman->load(['pesanan.user', 'pesanan.alamat']);
        return view('kurir.pengiriman.show', compact('pengiriman'));
    }
}
