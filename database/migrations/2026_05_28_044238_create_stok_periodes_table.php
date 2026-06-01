<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_periode', function (Blueprint $table) {
            $table->id();
            $table->string('periode', 7); // Format: YYYY-MM (Contoh: 2025-07)
            $table->foreignId('gudang_id')->constrained('gudang')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->decimal('qty_akhir', 10, 2)->default(0);
            $table->decimal('hpp_akhir', 15, 2)->default(0); // Snapshot Harga Pokok Rata-rata
            $table->timestamps();

            // KUNCI PERFORMA: Indexing agar query saat audit berkecepatan cahaya
            $table->unique(['periode', 'gudang_id', 'produk_id'], 'stok_periode_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_periode');
    }
};
