<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_transaksi_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_transaksi_id')->constrained('pos_transaksi')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk');
            $table->decimal('qty', 10, 2);
            $table->decimal('harga_jual_satuan', 15, 2); // Snapshot harga jual saat transaksi
            $table->decimal('harga_pokok_satuan', 15, 2)->default(0); // Snapshot HPP (Moving Average) saat itu
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_transaksi_detail');
    }
};
