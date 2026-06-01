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
        Schema::create('pos_shift', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('gudang_id')->constrained('gudang'); // Lokasi kasir bertugas
            $table->timestamp('waktu_buka')->useCurrent();
            $table->timestamp('waktu_tutup')->nullable();
            $table->decimal('modal_awal', 15, 2)->default(0); // Petty cash laci
            $table->decimal('total_pemasukan_tunai', 15, 2)->default(0);
            $table->decimal('total_pemasukan_non_tunai', 15, 2)->default(0);
            $table->decimal('kas_fisik_akhir', 15, 2)->default(0); // Input manual kasir saat tutup shift
            $table->decimal('selisih_kas', 15, 2)->default(0);
            $table->enum('status', ['OPEN', 'CLOSED'])->default('OPEN');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_shift');
    }
};
