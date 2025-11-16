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
        Schema::create('keranjang_item', function (Blueprint $table) {
            $table->id('keranjang_item_id');
            $table->foreignUuid('keranjang_id')->constrained('keranjang', 'keranjang_id')->cascadeOnDelete();
            $table->foreignUuid('produk_id')->constrained('produk', 'produk_id')->restrictOnDelete();
            $table->unsignedInteger('qty')->default(1);
            $table->timestamps();
            $table->unique(['keranjang_id', 'produk_id']);
            $table->index('keranjang_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keranjang_item');
    }
};
