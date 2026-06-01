<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_operasional', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nomor_bukti')->unique();
            $table->enum('jenis', ['PENGELUARAN', 'PEMASUKAN']);
            $table->foreignId('akun_kas_id')->constrained('akun_master');
            $table->foreignId('akun_lawan_id')->constrained('akun_master');
            $table->decimal('nominal', 15, 2);
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_operasional');
    }
};
