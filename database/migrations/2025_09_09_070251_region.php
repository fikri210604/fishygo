<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->string('code')->primary(); // dari API wilayah.id
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('regencies', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('name');
            $table->string('province_code');
            $table->foreign('province_code')->references('code')->on('provinces')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('name');
            $table->string('regency_code');
            $table->foreign('regency_code')->references('code')->on('regencies')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('villages', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('name');
            $table->string('district_code');
            $table->foreign('district_code')->references('code')->on('districts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villages');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('provinces');
    }
};
