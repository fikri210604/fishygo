<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel status pembayaran
        Schema::create('payment_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // contoh: pending, success, failed
            $table->string('name'); // contoh: Pending, Success, Failed
            $table->timestamps();
        });

        // Tabel metode pembayaran
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // contoh: cash, transfer, midtrans
            $table->string('name'); // contoh: Cash, Bank Transfer, Midtrans
            $table->timestamps();
        });

        Schema::create('payment_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('restrict');
            $table->string('code', 20)->unique(); // contoh: BCA, BNI, BRI, OVO, GOPAY
            $table->string('name');               // contoh: Bank BCA, OVO, GoPay
            $table->string('type', 20);           // 'bank' atau 'ewallet'
            $table->timestamps();
        });
        
        // Tabel payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('restrict');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('restrict');
            $table->foreignId('payment_status_id')->constrained('payment_statuses')->onDelete('restrict');
            $table->string('gateway_transaction_id')->nullable();
            $table->text('gateway_response')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('payment_statuses');
    }
};
