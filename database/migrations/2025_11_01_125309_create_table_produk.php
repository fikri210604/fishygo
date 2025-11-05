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
            $table->foreignId('kategori_id')->constrained('kategori_produk')->cascadeOnDelete();
            $table->foreignId('jenis_ikan_id')->constrained('table_jenis_ikan')->cascadeOnDelete();
            $table->decimal('harga', 12, 2);
            $table->decimal('harga_promo', 12, 2)->nullable();
            $table->dateTime('promo_mulai')->nullable();
            $table->dateTime('promo_selesai')->nullable();

            $table->text('deskripsi');
            $table->string('satuan', 10);
            $table->unsignedInteger('stok');
            $table->unsignedInteger('berat_gram')->nullable();
            $table->date('expired_at')->nullable();

            // Cache rating (opsional)
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);

            // Status & audit
            $table->char('aktif', 1)->default('1');
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();

            // Soft delete & timestamps
            $table->softDeletes();
            $table->timestamps();

            // Index tambahan
            $table->index(['kategori_id', 'jenis_ikan_id']);
            $table->index('aktif');
            $table->index('created_by');

            // Relasi ke penggunas (pembuat & pengubah)
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
