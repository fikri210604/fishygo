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
        // Catatan: tabel master wilayah (provinces/regencies/districts/villages)
        // tidak lagi dibuat di sini karena data wilayah diambil langsung dari API.

        // Roles table with slug
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug', 32)->unique();
            $table->char('aktif', 1)->default('1');
            $table->timestamps();
        });

        // Users (penggunas)
        Schema::create('pengguna', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('google_id')->nullable()->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();

            // Legacy single-role column (kept for compatibility)
            $table->string('role_slug', 32)->nullable()->index();

            // Profile
            $table->string('nomor_telepon')->nullable();
            $table->string('avatar')->nullable();


            $table->char('aktif', 1)->default('1');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('role_slug')->references('slug')->on('roles')->cascadeOnUpdate();
        });

        // Many-to-many pivot for multi-role
        Schema::create('pengguna_role', function (Blueprint $table) {
            $table->id();
            $table->uuid('pengguna_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();

            $table->unique(['pengguna_id', 'role_id']);
            $table->foreign('pengguna_id')->references('id')->on('pengguna')->cascadeOnDelete();
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna_role');
        Schema::dropIfExists('penggunas');
        Schema::dropIfExists('roles');
    }
};
