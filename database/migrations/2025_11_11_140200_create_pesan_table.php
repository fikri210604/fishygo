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
        Schema::create('pesan', function (Blueprint $table) {
            $table->uuid('pesan_id')->primary();
            $table->uuid('percakapan_id')->index();
            $table->uuid('pengirim_id')->index(); 
            $table->text('konten')->nullable();
            $table->string('tipe', 16)->default('teks'); 
            $table->json('lampiran')->nullable();
            $table->uuid('reply_to_id')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('percakapan_id')
                ->references('percakapan_id')->on('percakapan')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('pengirim_id')
                ->references('id')->on('pengguna')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // Self-referencing FK ditambahkan setelah create untuk kompatibilitas PostgreSQL
        });

        // Tambah FK self-reference setelah tabel dibuat (PostgreSQL butuh PK sudah ada)
        Schema::table('pesan', function (Blueprint $table) {
            $table->foreign('reply_to_id')
                ->references('pesan_id')->on('pesan')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesan');
    }
};
