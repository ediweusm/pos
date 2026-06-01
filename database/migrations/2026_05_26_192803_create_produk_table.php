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
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori');
            $table->foreignId('satuan_dasar_id')->constrained('satuan'); // Satuan terkecil (Base Unit)
            $table->string('nama');
            $table->string('sku')->unique()->nullable();
            $table->string('barcode')->unique()->nullable();
            $table->decimal('stok_minimum', 10, 2)->default(0);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
