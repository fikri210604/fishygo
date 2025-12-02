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
        $allowedStatus = ['siap', 'diambil', 'diantar', 'diterima', 'dikembalikan'];

        $statusOrder = ['siap', 'diambil', 'diantar', 'diterima', 'dikembalikan'];

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
            ->orderByRaw("FIELD(status, '" . implode("','", $statusOrder) . "')")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('kurir.dashboard', compact('items', 'status'));
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
