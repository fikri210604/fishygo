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
        Schema::create('pesanan_item', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('pesanan_id')->constrained('pesanan', 'pesanan_id')->cascadeOnDelete();
            $table->foreignUuid('produk_id')->constrained('produk', 'produk_id')->restrictOnDelete();

            // Snapshot data produk saat checkout (ringkas)
            $table->string('nama_produk_snapshot');

            $table->decimal('harga_satuan', 12, 2);
            $table->unsignedInteger('qty');
            $table->decimal('subtotal', 12, 2);

            $table->timestamps();

            $table->index('pesanan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan_item');
    }
};
