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
        Schema::create('log_pesanan', function (Blueprint $table) {
            $table->id('log_pesanan_id');
            $table->foreignUuid('pesanan_id')->constrained('pesanan', 'pesanan_id')->cascadeOnDelete();
            $table->string('status_dari', 32)->nullable();
            $table->string('status_ke', 32);
            $table->foreignUuid('oleh_pengguna_id')->nullable()->constrained('pengguna', 'id')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('pesanan_id');
            $table->index('status_ke');
            $table->index('oleh_pengguna_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_pesanan');
    }
};
