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
        Schema::create('jurnal', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_jurnal')->unique();
            $table->date('tanggal');
            $table->string('keterangan')->nullable();
            $table->string('referensi_tipe')->nullable(); // Menghubungkan jurnal ke modul POS/Pembelian
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal');
    }
};
