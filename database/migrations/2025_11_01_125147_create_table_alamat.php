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
        Schema::create('alamat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pengguna_id');
            $table->string('penerima');
            $table->text('alamat_lengkap');

            // Snapshot wilayah
            $table->string('province_id', 32)->nullable();
            $table->string('province_name', 100)->nullable();
            $table->string('regency_id', 32)->nullable();
            $table->string('regency_name', 100)->nullable();
            $table->string('district_id', 32)->nullable();
            $table->string('district_name', 100)->nullable();
            $table->string('village_id', 32)->nullable();
            $table->string('village_name', 150)->nullable();
            $table->string('kode_pos', 10)->nullable();
            
            $table->timestamps();

            $table->foreign('pengguna_id')->references('id')->on('pengguna')->cascadeOnDelete();
            $table->index('pengguna_id');
            $table->index(['province_id','regency_id','district_id','village_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alamat');
    }
};
