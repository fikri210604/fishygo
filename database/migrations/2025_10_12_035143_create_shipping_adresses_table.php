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
        Schema::create('shipping_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->string('label'); // contoh: Rumah, Kantor
            $table->text('address'); // lebih fleksibel
            $table->string('postal_code', 10);

            $table->string('province_code');
            $table->string('regency_code');
            $table->string('district_code');
            $table->string('village_code');

            $table->foreign('province_code')->references('code')->on('provinces')->onDelete('restrict');
            $table->foreign('regency_code')->references('code')->on('regencies')->onDelete('restrict');
            $table->foreign('district_code')->references('code')->on('districts')->onDelete('restrict');
            $table->foreign('village_code')->references('code')->on('villages')->onDelete('restrict');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_addresses');
    }
};
