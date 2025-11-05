<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artikel', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->longText('isi');
            $table->uuid('penulis_id');
            $table->timestamp('diterbitkan_pada')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // referensi ke penggunas
            $table->foreign('penulis_id')->references('id')->on('pengguna')->cascadeOnDelete();
            $table->index('penulis_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artikel');
    }
};
