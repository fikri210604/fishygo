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
        Schema::create('review_produk', function (Blueprint $table) {
            $table->uuid('review_id')->primary();
            $table->foreignUuid('produk_id')->constrained('produk', 'produk_id')->cascadeOnDelete();
            $table->foreignUuid('pengguna_id')->constrained('pengguna','id')->cascadeOnDelete();
            $table->text('review');
            $table->integer('rating');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_produk');
    }
};
