<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_saldo', function (Blueprint $table) {
            $table->unique(['gudang_id', 'produk_id'], 'stok_saldo_gudang_produk_unique');
        });

        Schema::table('pos_transaksi', function (Blueprint $table) {
            $table->foreignId('pos_shift_id')
                ->nullable()
                ->after('kasir_id')
                ->constrained('pos_shifts')
                ->nullOnDelete();
            $table->index(['pos_shift_id', 'metode_bayar', 'status_pembayaran'], 'pos_transaksi_shift_payment_index');
        });

        Schema::table('jurnal_barang', function (Blueprint $table) {
            $table->index(['gudang_id', 'produk_id', 'created_at'], 'jurnal_barang_stock_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::table('jurnal_barang', function (Blueprint $table) {
            $table->dropIndex('jurnal_barang_stock_lookup_index');
        });

        Schema::table('pos_transaksi', function (Blueprint $table) {
            $table->dropIndex('pos_transaksi_shift_payment_index');
            $table->dropConstrainedForeignId('pos_shift_id');
        });

        Schema::table('stok_saldo', function (Blueprint $table) {
            $table->dropUnique('stok_saldo_gudang_produk_unique');
        });

    }
};
