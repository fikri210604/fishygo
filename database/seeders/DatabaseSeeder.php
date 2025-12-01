<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Jalankan seeder hanya jika tabel terkait masih kosong
        $this->seedOnce('roles', RoleSeeder::class);
        $this->seedOnce('pengguna', AdminSeeder::class);
        $this->seedOnce('pengguna', UserSeeder::class);
        $this->seedOnce('artikel', ArticleSeeder::class);
        $this->seedOnce('kategori_produk', KategoriProdukSeeder::class);
        $this->seedOnce('jenis_ikan', JenisIkanSeeder::class);
        $this->seedOnce('produk', ProdukSeeder::class);
        $this->seedOnce('review_produk', ReviewProdukSeeder::class);
        $this->seedOnce('pesanan', PesananSeeder::class);
        // Always run KurirSeeder (idempotent check inside)
        $this->call(KurirSeeder::class);
    }

    protected function seedOnce(string $table, string $seederClass, int $min = 1): void
    {
        try {
            $count = (int) DB::table($table)->count();
        } catch (\Throwable $e) {
            $this->command?->warn("Seeder $seederClass: tabel '$table' tidak ditemukan. Melewati.");
            return;
        }

        if ($count < $min) {
            $this->command?->info("Seeder $seederClass: menjalankan (tabel '$table' kosong).");
            $this->call($seederClass);
        } else {
            $this->command?->warn("Seeder $seederClass: sudah ada datanya (tabel '$table': $count).");
        }
    }
}
