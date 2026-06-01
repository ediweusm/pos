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
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_faktur')->unique();
            $table->date('tanggal');
            $table->foreignId('supplier_id')->nullable()->constrained('supplier');
            $table->foreignId('gudang_id')->constrained('gudang');
            $table->enum('status_pembayaran', ['LUNAS', 'UTANG'])->default('LUNAS');
            $table->decimal('subtotal_barang', 15, 2)->default(0); // Murni total harga barang
            $table->decimal('ppn_persen', 5, 2)->default(0);       // Persentase (Bisa 0, 11, 12, dll)
            $table->decimal('ppn_nominal', 15, 2)->default(0);     // Hasil kali subtotal * ppn_persen
            $table->decimal('grand_total', 15, 2)->default(0);     // subtotal_barang + ppn_nominal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};
