<?php

namespace Database\Factories;

use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\JenisIkan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Produk>
 */
class ProdukFactory extends Factory
{
    protected $model = Produk::class;

    public function definition(): array
    {
        // Pastikan ada kategori & jenis ikan
        $kategori = KategoriProduk::inRandomOrder()->first() ?? KategoriProduk::factory()->create();
        $jenis = JenisIkan::inRandomOrder()->first() ?? JenisIkan::factory()->create();

        // Pastikan ada user pembuat
        $user = User::inRandomOrder()->first();
        if (! $user) {
            $user = User::create([
                'nama' => 'Seeder User',
                'email' => 'seeder-'.Str::random(6).'@example.com',
                'username' => 'seed_'.Str::lower(Str::random(6)),
                'password' => bcrypt('password'),
                'role_slug' => 'user',
            ]);
        }

        $nama = ucwords($this->faker->unique()->words(3, true));
        $harga = $this->faker->randomFloat(2, 10000, 1000000);
        $isPromo = $this->faker->boolean(40);
        $promoMulai = $isPromo ? now()->subDays($this->faker->numberBetween(0, 10)) : null;
        $promoSelesai = $isPromo ? now()->addDays($this->faker->numberBetween(1, 20)) : null;

        return [
            'kode_produk' => 'SKU-'.strtoupper(Str::random(8)),
            'slug' => Str::slug($nama).'-'.Str::lower(Str::random(6)),
            'nama_produk' => $nama,
            'gambar_produk' => 'images/produk/'.Str::uuid().'.jpg',
            'kategori_id' => $kategori->id,
            'jenis_ikan_id' => $jenis->id,
            'harga' => $harga,
            'harga_promo' => $isPromo ? $harga * 0.9 : null,
            'promo_mulai' => $promoMulai,
            'promo_selesai' => $promoSelesai,
            'deskripsi' => $this->faker->paragraphs(3, true),
            'satuan' => $this->faker->randomElement(['pcs', 'kg', 'gram']),
            'stok' => $this->faker->numberBetween(0, 500),
            'berat_gram' => $this->faker->numberBetween(100, 2500),
            'expired_at' => $this->faker->optional(0.2)->dateTimeBetween('+10 days', '+12 months'),
            'rating_avg' => $this->faker->randomFloat(2, 0, 5),
            'rating_count' => $this->faker->numberBetween(0, 500),
            'aktif' => '1',
            'created_by' => $user->id,
            'updated_by' => $this->faker->boolean(30) ? $user->id : null,
        ];
    }
}

