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
        Schema::create('percakapan_pengguna', function (Blueprint $table) {
            $table->id('percakapan_pengguna_id');
            $table->uuid('percakapan_id')->index();
            $table->uuid('pengguna_id')->index();
            $table->string('peran', 16)->default('anggota'); // anggota | admin
            $table->boolean('notifikasi')->default(true);
            $table->timestamp('terakhir_dibaca_at')->nullable();
            $table->timestamps();

            $table->unique(['percakapan_id', 'pengguna_id']);

            $table->foreign('percakapan_id')
                ->references('percakapan_id')->on('percakapan')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('pengguna_id')
                ->references('id')->on('pengguna')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('percakapan_pengguna');
    }
};
