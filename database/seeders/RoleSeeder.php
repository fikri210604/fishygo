<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['slug' => 'admin', 'nama' => 'Admin'],
            ['slug' => 'kurir', 'nama' => 'Kurir'],
            ['slug' => 'user',  'nama' => 'User'],
        ];

        foreach ($roles as $r) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $r['slug']],
                [
                    'nama' => $r['nama'],
                    'aktif' => '1',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}

