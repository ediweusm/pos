<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_stok', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nomor_bukti')->unique();
            $table->foreignId('gudang_asal_id')->constrained('gudang');
            $table->foreignId('gudang_tujuan_id')->constrained('gudang');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_stok');
    }
};
