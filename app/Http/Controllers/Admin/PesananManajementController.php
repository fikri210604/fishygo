<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'waiting' => Pesanan::query()->whereIn('status', [Pesanan::STATUS_MENUNGGU_PEMBAYARAN, 'menunggu_konfirmasi'])->count(),
            'cancelled' => Pesanan::status(Pesanan::STATUS_DIBATALKAN)->count(),
        ];

        return view('admin.pesanan.index', compact('items', 'q', 'status', 'counts'));
    }

    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['user', 'alamat', 'items.produk', 'pembayaran', 'pengiriman']);
        return view('admin.pesanan.show', compact('pesanan'));
    }

    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $data = $request->validate([
            'status' => 'required|string|max:32',
        ]);

        $status = (string) $data['status'];

        $allowed = [
            Pesanan::STATUS_MENUNGGU_PEMBAYARAN,
            'menunggu_konfirmasi',
            'diproses',
            'siap_diambil',
            Pesanan::STATUS_DIKIRIM,
            Pesanan::STATUS_SELESAI,
        ];

        if (!in_array($status, $allowed, true)) {
            return back()->with('error', 'Status pesanan tidak valid.');
        }

        // Jangan ubah pesanan yang sudah dibatalkan lewat flow resmi
        if ($pesanan->status === Pesanan::STATUS_DIBATALKAN) {
            return back()->with('error', 'Pesanan yang sudah dibatalkan tidak dapat diubah statusnya.');
        }

        $pesanan->status = $status;
        $pesanan->save();

        // Pastikan ada data pengiriman untuk status yang membutuhkan kurir
        if (in_array($status, ['diproses', Pesanan::STATUS_DIKIRIM, Pesanan::STATUS_SELESAI], true)) {
            $pengiriman = $pesanan->ensurePengirimanForDelivery();
            if ($pengiriman) {
                if ($status === Pesanan::STATUS_DIKIRIM) {
                    $pengiriman->status = 'diantar';
                    if (empty($pengiriman->dikirim_pada)) {
                        $pengiriman->dikirim_pada = now();
                    }
                } elseif ($status === Pesanan::STATUS_SELESAI) {
                    $pengiriman->status = 'diterima';
                    if (empty($pengiriman->dikirim_pada)) {
                        $pengiriman->dikirim_pada = now();
                    }
                    if (empty($pengiriman->diterima_pada)) {
                        $pengiriman->diterima_pada = now();
                    }
                }
                $pengiriman->save();
            }
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function destroy(Pesanan $pesanan)
    {
        DB::transaction(function () use ($pesanan) {
            // Hapus relasi yang tidak memakai soft delete
            $pesanan->items()->delete();
            $pesanan->pembayaran()->delete();
            if ($pesanan->pengiriman) {
                $pesanan->pengiriman()->delete();
            }
            DB::table('log_pesanan')->where('pesanan_id', $pesanan->pesanan_id)->delete();

            // Soft delete pesanan (menggunakan SoftDeletes)
            $pesanan->delete();
        });

        return redirect()
            ->route('admin.pesanan.index')
            ->with('success', 'Pesanan berhasil dihapus.');
    }
}
