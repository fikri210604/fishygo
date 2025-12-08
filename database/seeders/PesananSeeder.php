<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pesanan;
use App\Models\PesananItem;
use App\Models\User;
use App\Models\Produk;

class PesananSeeder extends Seeder
{
    public function run(): void
    {
        // Hindari duplikasi jika sudah pernah dibuat
        if (Pesanan::where('kode_pesanan', 'INV-SEED-001')->exists()) {
            return;
        }

        $user = User::where('email', 'user@example.com')->first() ?? User::first();

        if (!$user) {
            $this->command?->warn('PesananSeeder: tidak ada pengguna, seeder dilewati.');
            return;
        }

        // Pastikan ada minimal 1 produk
        if (Produk::count() === 0) {
            $this->call(ProdukSeeder::class);
        }

        $produk = Produk::first();

        if (!$produk) {
            $this->command?->warn('PesananSeeder: tidak ada produk, seeder dilewati.');
            return;
        }

        // Pastikan pengguna punya alamat (atau buat contoh sederhana)
        $alamat = $user->alamats()->first();

        if (!$alamat) {
            $alamat = $user->alamats()->create([
                'penerima'        => $user->nama ?? 'User Contoh',
                'alamat_lengkap'  => 'Jl. Contoh No. 123, Kota Contoh',
                'province_id'     => '31',
                'province_name'   => 'DKI Jakarta',
                'regency_id'      => '3173',
                'regency_name'    => 'Jakarta Barat',
                'district_id'     => '3173030',
                'district_name'   => 'Kecamatan Contoh',
                'village_id'      => '3173030005',
                'village_name'    => 'Kelurahan Contoh',
                'rt'              => '001',
                'rw'              => '002',
                'kode_pos'        => '12345',
            ]);
        }

        $qty = 2;
        $harga = (float) $produk->harga;
        $subtotal = $harga * $qty;
        $ongkir = 10000;
        $diskon = 0;
        $total = $subtotal + $ongkir - $diskon;

        $pesanan = Pesanan::create([
            'kode_pesanan'      => 'INV-SEED-001',
            'pengguna_id'       => $user->id,
            'alamat_id'         => $alamat->id,
            'status'            => 'dikirim',
            'metode_pembayaran' => 'bank_transfer',
            'subtotal'          => $subtotal,
            'ongkir'            => $ongkir,
            'diskon'            => $diskon,
            'total'             => $total,
            'berat_total_gram'  => $produk->berat_gram ?? 1000,
            'payment_due'       => now()->addDay(),
            'catatan'           => 'Pesanan contoh dari seeder.',
            'alamat_snapshot'   => [
                'penerima'       => $alamat->penerima,
                'alamat_lengkap' => $alamat->alamat_lengkap,
                'province_id'    => $alamat->province_id,
                'province_name'  => $alamat->province_name,
                'regency_id'     => $alamat->regency_id,
                'regency_name'   => $alamat->regency_name,
                'district_id'    => $alamat->district_id,
                'district_name'  => $alamat->district_name,
                'village_id'     => $alamat->village_id,
                'village_name'   => $alamat->village_name,
                'rt'             => $alamat->rt,
                'rw'             => $alamat->rw,
                'kode_pos'       => $alamat->kode_pos,
            ],
        ]);

        PesananItem::create([
            'pesanan_id'            => $pesanan->pesanan_id,
            'produk_id'             => $produk->produk_id,
            'nama_produk_snapshot'  => $produk->nama_produk,
            'harga_satuan'          => $produk->harga,
            'qty'                   => $qty,
            'subtotal'              => $subtotal,
        ]);

        // Create dummy orders for the past 30 days with varied distribution
        $orderCounter = 1;
        $totalOrders = rand(45, 60); // Create between 45-60 orders total

        for ($i = 0; $i < $totalOrders; $i++) {
            // Random date within the past 30 days
            $daysBack = rand(0, 29);
            $date = now()->subDays($daysBack);

            $randomQty = rand(1, 5);
            $randomSubtotal = $harga * $randomQty;
            $randomTotal = $randomSubtotal + $ongkir - $diskon;

            $dummyPesanan = Pesanan::create([
                'kode_pesanan'      => 'INV-DUMMY-' . str_pad($orderCounter, 3, '0', STR_PAD_LEFT),
                'pengguna_id'       => $user->id,
                'alamat_id'         => $alamat->id,
                'status'            => 'selesai',
                'metode_pembayaran' => 'bank_transfer',
                'subtotal'          => $randomSubtotal,
                'ongkir'            => $ongkir,
                'diskon'            => $diskon,
                'total'             => $randomTotal,
                'berat_total_gram'  => $produk->berat_gram ?? 1000,
                'payment_due'       => $date->addDay(),
                'catatan'           => 'Pesanan dummy untuk grafik.',
                'alamat_snapshot'   => [
                    'penerima'       => $alamat->penerima,
                    'alamat_lengkap' => $alamat->alamat_lengkap,
                    'province_id'    => $alamat->province_id,
                    'province_name'  => $alamat->province_name,
                    'regency_id'     => $alamat->regency_id,
                    'regency_name'   => $alamat->regency_name,
                    'district_id'    => $alamat->district_id,
                    'district_name'  => $alamat->district_name,
                    'village_id'     => $alamat->village_id,
                    'village_name'   => $alamat->village_name,
                    'rt'             => $alamat->rt,
                    'rw'             => $alamat->rw,
                    'kode_pos'       => $alamat->kode_pos,
                ],
                'created_at'        => $date,
                'updated_at'        => $date,
            ]);

            PesananItem::create([
                'pesanan_id'            => $dummyPesanan->pesanan_id,
                'produk_id'             => $produk->produk_id,
                'nama_produk_snapshot'  => $produk->nama_produk,
                'harga_satuan'          => $produk->harga,
                'qty'                   => $randomQty,
                'subtotal'              => $randomSubtotal,
            ]);

            $orderCounter++;
        }
    }
}

