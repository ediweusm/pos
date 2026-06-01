<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_kas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('akun_id')->constrained('akun_master')->cascadeOnDelete();
            
            $table->decimal('nominal_sistem', 15, 2)->default(0);
            $table->decimal('nominal_fisik', 15, 2)->default(0);
            $table->decimal('selisih', 15, 2)->default(0); // nominal_fisik - nominal_sistem
            
            $table->text('keterangan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_kas');
    }
};
