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
        // Tabel permissions (master hak akses)
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug', 32)->unique();
            $table->string('modul')->nullable();
            $table->text('deskripsi')->nullable();
            $table->char('aktif', 1)->default('1');
            $table->timestamps();
        });

        // Pivot many-to-many: roles <-> permissions
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->unique(['role_id', 'permission_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
    }
};
