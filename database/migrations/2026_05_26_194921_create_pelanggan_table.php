<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pelanggan')->unique(); // Contoh: CUST-001
            $table->string('nama');
            $table->string('no_telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->integer('poin_loyalitas')->default(0); // Untuk program diskon/member
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
