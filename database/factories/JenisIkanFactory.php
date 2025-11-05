<?php

namespace Database\Factories;

use App\Models\JenisIkan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JenisIkan>
 */
class JenisIkanFactory extends Factory
{
    protected $model = JenisIkan::class;

    public function definition(): array
    {
        return [
            'jenis_ikan' => ucwords($this->faker->unique()->words(2, true)),
            'gambar_jenis_ikan' => 'images/ikan/'. $this->faker->unique()->uuid .'.jpg',
        ];
    }
}

