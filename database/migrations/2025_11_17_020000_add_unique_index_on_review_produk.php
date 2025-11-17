<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('review_produk', function (Blueprint $table) {
            // Unique per pengguna per produk
            $table->unique(['produk_id','pengguna_id'], 'uniq_review_produk_user');
        });
    }

    public function down(): void
    {
        Schema::table('review_produk', function (Blueprint $table) {
            $table->dropUnique('uniq_review_produk_user');
        });
    }
};

