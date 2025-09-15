<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => 'superadmin',
            'role_id' => 1,
            'email' => 'superadmin@example.com',
            'password' => Hash::make('superadmin'),
            'address' => 'Jl. Raya Kediri',
        ]);

        User::create([
            'username' => 'admin',
            'role_id' => 2,
            'email' => 'admin@example.com',
            'password' => Hash::make('admin'),
            'address' => 'Jl. Raya Kediri',
        ]);

        User::create([
            'username' => 'user',
            'role_id' => 3,
            'email' => 'user@example.com',
            'password' => Hash::make('user'),
            'address' => 'Jl. Raya Kediri',
        ]);
    }
}
