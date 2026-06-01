<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyesuaian_stok', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('gudang_id')->constrained('gudang')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            
            $table->decimal('qty_sistem', 10, 2)->default(0);
            $table->decimal('qty_fisik', 10, 2)->default(0);
            $table->decimal('selisih', 10, 2)->default(0); // Plus jika lebih, Minus jika hilang
            
            $table->text('keterangan')->nullable(); // Alasan: "Dimakan tikus", "Salah hitung", dll.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyesuaian_stok');
    }
};
