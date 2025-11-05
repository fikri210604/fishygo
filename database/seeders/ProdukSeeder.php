<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\JenisIkan;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan kategori & jenis ikan tersedia
        if (KategoriProduk::count() === 0) {
            $this->call(KategoriProdukSeeder::class);
        }
        if (JenisIkan::count() === 0) {
            $this->call(JenisIkanSeeder::class);
        }

        Produk::factory()->count(50)->create();
    }
}

