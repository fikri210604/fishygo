<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        // No longer needed: wilayah data fetched live from API.
        $this->command?->warn('WilayahSeeder skipped: data now fetched directly from API.');
    }
}
