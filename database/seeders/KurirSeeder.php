<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class KurirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(User::where('role_slug', 'kurir')->count() === 0) {
            $kurir = User::create([
                'email' => 'kurir@example.com',
                'nama' => 'Kurir',
                'username' => 'kurir',
                'role_slug' => User::ROLE_KURIR,
                'password' => Hash::make('kurir'),
                'nomor_telepon' => '081299092233',
                'email_verified_at' => now(),
            ]);
            if (method_exists($kurir, 'assignRole')) {
                $kurir->assignRole(User::ROLE_KURIR);
            }
        }
    }
}
