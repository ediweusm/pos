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
        Schema::create('stok_saldo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gudang_id')->constrained('gudang');
            $table->foreignId('produk_id')->constrained('produk');
            $table->decimal('qty_sekarang', 10, 2)->default(0);
            $table->decimal('harga_pokok_rata_rata', 15, 2)->default(0); // Nilai Moving Average per satuan dasar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_saldo');
    }
};
