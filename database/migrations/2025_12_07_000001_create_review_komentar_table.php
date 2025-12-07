<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_komentar', function (Blueprint $table) {
            $table->string('komentar_id', 36)->primary();
            $table->string('produk_id', 36);
            $table->string('review_id', 36)->nullable(); // refer ke review_produk (rating utama) bila ada
            $table->string('pengguna_id', 36);
            $table->text('komentar');
            $table->timestamps();
            $table->softDeletes();

            // Index untuk query umum
            $table->index(['produk_id']);
            $table->index(['review_id']);
            $table->index(['pengguna_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_komentar');
    }
};

