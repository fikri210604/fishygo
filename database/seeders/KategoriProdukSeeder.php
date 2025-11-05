<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriProduk;

class KategoriProdukSeeder extends Seeder
{
    public function run(): void
    {
        KategoriProduk::factory()->count(6)->create();
    }
}

