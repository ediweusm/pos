<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_transaksi', function (Blueprint $table) {
            // Metode penyerahan barang: langsung diambil atau dikirim
            $table->enum('tipe_order', ['TAKE_AWAY', 'DELIVERY'])->default('TAKE_AWAY')->after('metode_bayar');

            // Status pelunasan: dibayar tunai/transfer vs dicatat sebagai piutang
            $table->enum('status_pembayaran', ['LUNAS', 'TEMPO'])->default('LUNAS')->after('tipe_order');

            // Isian alamat hanya relevan jika tipe_order = DELIVERY
            $table->text('alamat_pengiriman')->nullable()->after('status_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('pos_transaksi', function (Blueprint $table) {
            $table->dropColumn(['tipe_order', 'status_pembayaran', 'alamat_pengiriman']);
        });
    }
};
