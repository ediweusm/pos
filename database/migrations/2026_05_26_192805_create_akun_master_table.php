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
        Schema::create('akun_master', function (Blueprint $table) {
            $table->id();
            $table->string('kode_akun')->unique(); // Contoh: 1-1001
            $table->string('nama_akun'); // Contoh: Kas Kasir, Persediaan Barang
            $table->enum('tipe_akun', ['ASET', 'KEWAJIBAN', 'EKUITAS', 'PENDAPATAN', 'BEBAN']);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akun_master');
    }
};
