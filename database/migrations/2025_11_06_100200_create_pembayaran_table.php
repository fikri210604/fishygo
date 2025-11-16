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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->uuid('pembayaran_id')->primary();
            $table->foreignUuid('pesanan_id')->constrained('pesanan', 'pesanan_id')->cascadeOnDelete();


            $table->string('gateway', 50)->nullable();
            $table->string('channel', 50)->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('status', 32)->default('pending')->index();

            // Payload generik per-gateway + referensi
            $table->string('reference')->nullable()->index();
            // Optional: kolom cepat untuk lookup webhook Midtrans
            $table->string('order_id', 64)->nullable()->index();
            $table->string('transaction_id', 64)->nullable()->index();
            $table->json('gateway_payload')->nullable();
            $table->timestamp('expiry_time')->nullable();
            $table->timestamp('dibayar_pada')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
