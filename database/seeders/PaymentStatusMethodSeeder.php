<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentStatusMethodSeeder extends Seeder
{
    public function run(): void
    {
        // Payment Statuses
        DB::table('payment_statuses')->insert([
            ['id' => 1, 'code' => 'pending', 'name' => 'Pending', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'code' => 'success', 'name' => 'Success', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'code' => 'failed', 'name' => 'Failed', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Payment Methods
        DB::table('payment_methods')->insert([
            ['id' => 1, 'code' => 'cash', 'name' => 'Cash', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'code' => 'transfer', 'name' => 'Bank Transfer', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'code' => 'ewallet', 'name' => 'E Wallet', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Payment Channels
        DB::table('payment_channels')->insert([
            // Bank Transfer Channels
            ['payment_method_id' => 2, 'code' => 'BCA', 'name' => 'Bank BCA', 'type' => 'bank', 'created_at' => now(), 'updated_at' => now()],
            ['payment_method_id' => 2, 'code' => 'BNI', 'name' => 'Bank BNI', 'type' => 'bank', 'created_at' => now(), 'updated_at' => now()],
            ['payment_method_id' => 2, 'code' => 'BRI', 'name' => 'Bank BRI', 'type' => 'bank', 'created_at' => now(), 'updated_at' => now()],
            // E-Wallet Channels (misal via Midtrans)
            ['payment_method_id' => 3, 'code' => 'OVO', 'name' => 'OVO', 'type' => 'ewallet', 'created_at' => now(), 'updated_at' => now()],
            ['payment_method_id' => 3, 'code' => 'GOPAY', 'name' => 'GoPay', 'type' => 'ewallet', 'created_at' => now(), 'updated_at' => now()],
            ['payment_method_id' => 3, 'code' => 'SHOPEEPAY', 'name' => 'ShopeePay', 'type' => 'ewallet', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
