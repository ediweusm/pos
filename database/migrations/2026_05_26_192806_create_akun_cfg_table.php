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
        Schema::create('akun_cfg', function (Blueprint $table) {
            $table->id();
            $table->string('kode_event')->unique(); // Contoh: POS_SALE_TUNAI, POS_HPP
            $table->string('nama_event'); 
            $table->foreignId('akun_debit_id')->constrained('akun_master');
            $table->foreignId('akun_kredit_id')->constrained('akun_master');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akun_cfg');
    }
};
