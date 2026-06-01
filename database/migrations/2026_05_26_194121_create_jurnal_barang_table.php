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
        Schema::create('jurnal_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk');
            $table->foreignId('gudang_id')->constrained('gudang');
            $table->enum('tipe_mutasi', ['SALE', 'PURCHASE', 'RETURN', 'ADJUSTMENT', 'TRANSFER']);
            $table->string('referensi_tipe')->nullable(); // Polymorphic (misal: App\Models\Penjualan)
            $table->unsignedBigInteger('referensi_id')->nullable(); // ID dari transaksi terkait
            $table->decimal('qty_in', 10, 2)->default(0);
            $table->decimal('qty_out', 10, 2)->default(0);
            $table->decimal('harga_satuan', 15, 2)->default(0); // Harga beli/HPP saat mutasi terjadi
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_barang');
    }
};
