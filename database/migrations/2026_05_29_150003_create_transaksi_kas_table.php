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
        Schema::create('transaksi_kas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nomor_bukti')->unique();
            $table->foreignId('akun_pengirim_id')->constrained('akun_master');
            $table->foreignId('akun_penerima_id')->constrained('akun_master');
            $table->decimal('nominal', 15, 2);
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_kas');
    }
};
