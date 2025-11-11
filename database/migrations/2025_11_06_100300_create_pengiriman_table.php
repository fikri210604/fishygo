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
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->uuid('pengiriman_id')->primary();
            $table->foreignUuid('pesanan_id')->constrained('pesanan', 'pesanan_id')->cascadeOnDelete();

            $table->string('kurir_kode', 50)->nullable(); // jne, tiki, pos, jnt, etc
            $table->string('kurir_service', 50)->nullable(); // REG, YES, CTC, etc
            $table->string('resi', 100)->nullable()->index();
            $table->decimal('biaya', 12, 2)->nullable();
            $table->string('status', 32)->default('siap')->index();

            // Kolom tambahan dihilangkan untuk kesederhanaan

            $table->timestamp('dikemas_pada')->nullable();
            $table->timestamp('dikirim_pada')->nullable();
            $table->timestamp('diterima_pada')->nullable();

            $table->foreignUuid('assigned_kurir_id')->nullable()->constrained('pengguna', 'id')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengiriman');
    }
};
