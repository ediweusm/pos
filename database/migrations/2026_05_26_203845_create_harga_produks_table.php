<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harga_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();

            // Satuan jual (harga berlaku untuk satuan tertentu, misal: Pcs vs Karung)
            $table->foreignId('satuan_id')->constrained('satuan');

            // Nama tingkatan: ECERAN, GROSIR, DISTRIBUTOR, MEMBER, dll.
            $table->string('tipe_harga')->default('ECERAN');

            // Batas kuantitas minimum untuk memperoleh harga ini
            $table->decimal('minimal_qty', 10, 2)->default(1);

            // Nominal harga jual
            $table->decimal('harga', 15, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harga_produk');
    }
};
