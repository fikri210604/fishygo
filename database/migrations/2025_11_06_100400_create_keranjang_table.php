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
        Schema::create('keranjang', function (Blueprint $table) {
            $table->uuid('keranjang_id')->primary();
            $table->foreignUuid('pengguna_id')->nullable()->constrained('pengguna', 'id')->nullOnDelete();
            $table->string('kode_sesi', 100)->nullable()->index();

            $table->timestamps();

            $table->index('pengguna_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keranjang');
    }
};

