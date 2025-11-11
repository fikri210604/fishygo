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
        Schema::create('pesanan', function (Blueprint $table) {
            $table->uuid('pesanan_id')->primary();
            $table->string('kode_pesanan')->unique();
            $table->foreignUuid('pengguna_id')->constrained('pengguna', 'id')->cascadeOnDelete();
            $table->foreignUuid('alamat_id')->nullable()->constrained('alamat', 'id')->nullOnDelete();
            $table->string('status', 32)->default('draft')->index();
            $table->string('metode_pembayaran', 50)->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('ongkir', 12, 2)->default(0);
            $table->decimal('diskon', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->unsignedInteger('berat_total_gram')->default(0);
            $table->timestamp('payment_due')->nullable(); // batas waktu bayar dari Midtrans
            $table->text('catatan')->nullable();
            $table->json('alamat_snapshot')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignUuid('cancelled_by_id')->nullable()->constrained('pengguna', 'id')->nullOnDelete();
            $table->string('cancel_reason', 100)->nullable();
            $table->text('cancel_note')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('pengguna_id');
            $table->index('cancelled_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};

