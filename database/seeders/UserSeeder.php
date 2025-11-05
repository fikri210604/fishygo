<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'nama' => 'User',
                'username' => 'user',
                'role_slug' => User::ROLE_USER,
                'password' => Hash::make('user'),
                'email_verified_at' => now(),
            ]
        );

        if (method_exists($user, 'assignRole')) {
            $user->assignRole(User::ROLE_USER);
        }
    }
}
