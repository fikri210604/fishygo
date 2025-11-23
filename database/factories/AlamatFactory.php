<?php

namespace Database\Factories;

use App\Models\Alamat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Alamat>
 */
class AlamatFactory extends Factory
{
    protected $model = Alamat::class;

    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'id' => (string) Str::uuid(),
            'pengguna_id' => $user->id,
            'penerima' => $this->faker->name(),
            'alamat_lengkap' => $this->faker->streetAddress(),
            'province_id' => (string) $this->faker->numberBetween(1, 34),
            'province_name' => 'Provinsi '.$this->faker->word(),
            'regency_id' => (string) $this->faker->numberBetween(1, 500),
            'regency_name' => 'Kab/Kota '.$this->faker->word(),
            'district_id' => (string) $this->faker->numberBetween(1, 5000),
            'district_name' => 'Kecamatan '.$this->faker->word(),
            'village_id' => (string) $this->faker->numberBetween(1, 50000),
            'village_name' => 'Desa '.$this->faker->word(),
            'rt' => (string) $this->faker->numberBetween(1, 10),
            'rw' => (string) $this->faker->numberBetween(1, 10),
            'kode_pos' => (string) $this->faker->numberBetween(11111, 99999),
        ];
    }
}

