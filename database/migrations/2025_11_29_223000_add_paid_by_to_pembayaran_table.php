<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            if (!Schema::hasColumn('pembayaran', 'paid_by_id')) {
                $table->foreignUuid('paid_by_id')->nullable()->after('dibayar_pada')
                    ->constrained('pengguna', 'id')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            if (Schema::hasColumn('pembayaran', 'paid_by_id')) {
                $table->dropConstrainedForeignId('paid_by_id');
            }
        });
    }
};

