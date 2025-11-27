<?php

namespace App\Services;

use App\Models\Alamat;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\PesananItem;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PesananService
{
    public function createFromCart(User $user, Alamat $alamat, array $cartSummary, array $options = []): Pesanan
    {
        $items = $cartSummary['items'] ?? [];
        if (empty($items)) {
            throw new RuntimeException('Keranjang kosong.');
        }

        // Validasi stok
        foreach ($items as $it) {
            $produk = $it['produk'];
            if (!is_null($produk->stok) && $produk->stok < $it['qty']) {
                throw new RuntimeException('Stok produk tidak mencukupi: ' . $produk->nama_produk);
            }
        }

        return DB::transaction(function () use ($user, $alamat, $cartSummary, $options) {
            $kode = 'ORD-' . now()->format('ymd') . '-' . Str::upper(Str::random(6));
            $ongkir = (float)($options['ongkir'] ?? 0);
            $diskon = (float)($options['diskon'] ?? 0);
            $subtotal = (float)$cartSummary['total'];
            $total = $subtotal + $ongkir - $diskon;

            $metodePembayaran = (string) ($options['metode_pembayaran'] ?? 'manual');
            $statusAwal = $metodePembayaran === 'cod' ? 'menunggu_konfirmasi' : Pesanan::STATUS_MENUNGGU_PEMBAYARAN;

            $pesanan = Pesanan::create([
                'kode_pesanan' => $kode,
                'pengguna_id' => $user->id,
                'alamat_id' => $alamat->id,
                'status' => $statusAwal,
                'metode_pembayaran' => $metodePembayaran,
                'subtotal' => $subtotal,
                'ongkir' => $ongkir,
                'diskon' => $diskon,
                'total' => $total,
                'catatan' => $options['catatan'] ?? null,
                'alamat_snapshot' => [
                    'penerima' => $alamat->penerima,
                    'alamat_lengkap' => $alamat->alamat_lengkap,
                    'province' => [$alamat->province_id, $alamat->province_name],
                    'regency' => [$alamat->regency_id, $alamat->regency_name],
                    'district' => [$alamat->district_id, $alamat->district_name],
                    'village' => [$alamat->village_id, $alamat->village_name],
                    'rt' => $alamat->rt,
                    'rw' => $alamat->rw,
                    'kode_pos' => $alamat->kode_pos,
                    'pickup' => (bool) ($options['pickup'] ?? false),
                    'delivery_method' => ((bool) ($options['pickup'] ?? false)) ? 'pickup' : 'delivery',
                ],
                'berat_total_gram' => $cartSummary['berat_total_gram'] ?? 0,
            ]);

            foreach ($cartSummary['items'] as $it) {
                $produk = $it['produk'];
                PesananItem::create([
                    'pesanan_id' => $pesanan->pesanan_id,
                    'produk_id' => $produk->produk_id,
                    'nama_produk_snapshot' => $produk->nama_produk,
                    'harga_satuan' => $it['harga'],
                    'qty' => $it['qty'],
                    'subtotal' => $it['subtotal'],
                ]);
                if (!is_null($produk->stok)) {
                    $produk->decrement('stok', (int) $it['qty']);
                }
            }

            $metode = (string) ($options['metode_pembayaran'] ?? 'manual');
            $gateway = match ($metode) {
                'midtrans' => 'midtrans',
                'cod' => 'cod',
                default => 'manual',
            };
            $channel = match ($metode) {
                'midtrans' => 'snap',
                'cod' => 'cod',
                default => 'transfer',
            };
            Pembayaran::create([
                'pesanan_id' => $pesanan->pesanan_id,
                'gateway' => $options['gateway'] ?? $gateway,
                'channel' => $options['channel'] ?? $channel,
                'amount' => $pesanan->total,
                'status' => 'pending',
                'reference' => $pesanan->kode_pesanan,
                'order_id' => $pesanan->kode_pesanan,
            ]);

            return $pesanan;
        });
    }

    public function cancel(Pesanan $pesanan, User $by, ?string $reason = null, ?string $note = null): void
    {
        if (!$pesanan->canBeCancelled()) {
            throw new RuntimeException('Pesanan tidak bisa dibatalkan pada status saat ini.');
        }

        DB::transaction(function () use ($pesanan, $by, $reason, $note) {
            $pesanan->loadMissing('items', 'pembayaran');
            foreach ($pesanan->items as $it) {
                $produk = Produk::find($it->produk_id);
                if ($produk && !is_null($produk->stok)) {
                    $produk->increment('stok', (int) $it->qty);
                }
            }

            foreach ($pesanan->pembayaran as $pay) {
                if (in_array($pay->status, ['pending', 'unpaid'])) {
                    $pay->status = 'cancelled';
                    $pay->save();
                }
            }

            $pesanan->status = Pesanan::STATUS_DIBATALKAN;
            $pesanan->cancelled_at = now();
            $pesanan->cancelled_by_id = $by->id;
            $pesanan->cancel_reason = $reason;
            $pesanan->cancel_note = $note;
            $pesanan->save();
        });
    }
}
