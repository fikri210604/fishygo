<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisIkan;

class JenisIkanSeeder extends Seeder
{
    public function run(): void
    {
        JenisIkan::factory()->count(10)->create();
    }
}

