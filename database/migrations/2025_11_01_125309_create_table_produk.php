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
        Schema::create('produk', function (Blueprint $table) {
            $table->uuid('produk_id')->primary();
            $table->string('slug')->unique();
            $table->string('kode_produk')->unique();
            $table->string('gambar_produk');
            $table->string('nama_produk');
            $table->foreignId('kategori_produk_id')->constrained('kategori_produk', 'kategori_produk_id')->cascadeOnDelete();
            $table->foreignId('jenis_ikan_id')->constrained('jenis_ikan', 'jenis_ikan_id')->cascadeOnDelete();
            $table->decimal('harga', 12, 2);
            $table->decimal('harga_promo', 12, 2)->nullable();
            $table->dateTime('promo_mulai')->nullable();
            $table->dateTime('promo_selesai')->nullable();
            $table->text('deskripsi');
            $table->string('satuan', 10);
            $table->unsignedInteger('stok');
            $table->unsignedInteger('berat_gram')->nullable();
            $table->date('kadaluarsa')->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->char('aktif', 1)->default('1');
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['kategori_produk_id', 'jenis_ikan_id']);
            $table->index('aktif');
            $table->index('created_by');
            $table->foreign('created_by')->references('id')->on('pengguna')->cascadeOnDelete();
            $table->foreign('updated_by')->references('id')->on('pengguna')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
