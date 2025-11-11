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
        Schema::create('percakapan', function (Blueprint $table) {
            $table->uuid('percakapan_id')->primary();
            $table->string('tipe', 16)->default('pribadi')->index(); // pribadi | grup
            $table->string('judul')->nullable(); // untuk grup
            $table->uuid('dibuat_oleh')->nullable()->index(); // pengguna.id
            $table->timestamp('pesan_terakhir_at')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('dibuat_oleh')
                ->references('id')->on('pengguna')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('percakapan');
    }
};

