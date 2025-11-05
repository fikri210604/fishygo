<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Seed a primary admin account
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nama' => 'Admin',
                'username' => 'admin',
                'role_slug' => User::ROLE_ADMIN,
                'password' => Hash::make('admin'),
                'email_verified_at' => now(),
            ]
        );

        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole(User::ROLE_ADMIN);
        }
    }
}
