<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('cabang_id')->nullable()->constrained('cabang')->nullOnDelete();
            $table->foreignId('gudang_id')->nullable()->constrained('gudang')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropForeign(['gudang_id']);
            $table->dropColumn(['cabang_id', 'gudang_id']);
        });
    }
};
