<?php

namespace Database\Factories;

use App\Models\KategoriProduk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KategoriProduk>
 */
class KategoriProdukFactory extends Factory
{
    protected $model = KategoriProduk::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        return [
            'nama_kategori' => ucwords($name),
            'gambar_kategori' => 'images/kategori/'. $this->faker->unique()->uuid .'.jpg',
        ];
    }
}

