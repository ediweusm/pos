<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('pos_shift');
        Schema::dropIfExists('pos_shifts');

        Schema::create('pos_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cabang_id')->constrained('cabang')->cascadeOnDelete();
            $table->foreignId('gudang_id')->constrained('gudang')->cascadeOnDelete();
            $table->dateTime('waktu_buka');
            $table->dateTime('waktu_tutup')->nullable();
            $table->decimal('modal_awal', 15, 2);
            $table->decimal('total_penjualan', 15, 2)->default(0.00);
            $table->decimal('saldo_aktual', 15, 2)->nullable();
            $table->decimal('selisih', 15, 2)->nullable();
            $table->string('status', 10)->default('OPEN');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_shifts');
    }
};
