<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_nota')->unique();
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggan')->nullOnDelete();
            $table->foreignId('gudang_id')->constrained('gudang');
            $table->foreignId('kasir_id')->constrained('users');
            $table->decimal('subtotal', 15, 2)->default(0);      // Total sebelum diskon
            $table->decimal('diskon_nominal', 15, 2)->default(0); // Diskon dalam Rp
            $table->decimal('grand_total', 15, 2)->default(0);   // Yang harus dibayar
            $table->decimal('tunai_diterima', 15, 2)->default(0);
            $table->decimal('kembalian', 15, 2)->default(0);
            $table->enum('metode_bayar', ['TUNAI', 'QRIS', 'TRANSFER'])->default('TUNAI');
            $table->enum('status', ['SELESAI', 'VOID'])->default('SELESAI');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_transaksi');
    }
};
