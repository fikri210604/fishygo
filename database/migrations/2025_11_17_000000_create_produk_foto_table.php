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
        // Drop kolom lama di tabel produk agar seluruh foto dikelola via produk_foto
        if (Schema::hasColumn('produk', 'gambar_produk')) {
            Schema::table('produk', function (Blueprint $table) {
                $table->dropColumn('gambar_produk');
            });
        }

        Schema::create('produk_foto', function (Blueprint $table) {
            $table->uuid('produk_foto_id')->primary();
            $table->uuid('produk_id');
            $table->string('path');
            $table->boolean('is_primary')->default(false);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('produk_id')->references('produk_id')->on('produk')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('pengguna')->cascadeOnDelete();
            $table->foreign('updated_by')->references('id')->on('pengguna')->cascadeOnDelete();

            $table->index(['produk_id', 'is_primary']);
            $table->index('urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tambahkan kembali kolom lama jika di-rollback
        if (! Schema::hasColumn('produk', 'gambar_produk')) {
            Schema::table('produk', function (Blueprint $table) {
                $table->string('gambar_produk')->default('');
            });
        }

        Schema::dropIfExists('produk_foto');
    }
};
