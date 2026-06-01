<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier', function (Blueprint $table) {
            $table->id();
            $table->string('kode_supplier')->unique(); // Contoh: SUP-001
            $table->string('nama_perusahaan');
            $table->string('nama_pic')->nullable();    // Person in Charge
            $table->string('no_telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier');
    }
};
