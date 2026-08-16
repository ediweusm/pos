<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AkunCfgSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id' => 1, 'kode_event' => 'POS_SALE_TUNAI', 'nama_event' => 'Penjualan POS Tunai', 'akun_debit_id' => 1, 'akun_kredit_id' => 10, 'created_at' => '2026-05-30 18:32:34', 'updated_at' => '2026-05-30 18:32:34'],
            ['id' => 2, 'kode_event' => 'POS_HPP', 'nama_event' => 'Pencatatan HPP Penjualan', 'akun_debit_id' => 12, 'akun_kredit_id' => 6, 'created_at' => '2026-05-30 18:32:34', 'updated_at' => '2026-05-30 18:32:34'],
            ['id' => 3, 'kode_event' => 'POS_SALE_TEMPO', 'nama_event' => 'Penjualan POS Tempo/Kredit', 'akun_debit_id' => 5, 'akun_kredit_id' => 10, 'created_at' => '2026-05-30 18:32:34', 'updated_at' => '2026-05-30 18:32:34'],
            ['id' => 4, 'kode_event' => 'STOK_DEFISIT', 'nama_event' => 'Stock Opname: Defisit/Hilang', 'akun_debit_id' => 13, 'akun_kredit_id' => 6, 'created_at' => '2026-05-30 18:32:34', 'updated_at' => '2026-05-30 18:32:34'],
            ['id' => 5, 'kode_event' => 'STOK_SURPLUS', 'nama_event' => 'Stock Opname: Surplus/Lebih', 'akun_debit_id' => 6, 'akun_kredit_id' => 11, 'created_at' => '2026-05-30 18:32:34', 'updated_at' => '2026-05-30 18:32:34'],
            ['id' => 6, 'kode_event' => 'KAS_DEFISIT', 'nama_event' => 'Audit Kas: Defisit/Hilang', 'akun_debit_id' => 16, 'akun_kredit_id' => 1, 'created_at' => '2026-05-30 18:32:34', 'updated_at' => '2026-05-30 18:32:34'],
            ['id' => 7, 'kode_event' => 'KAS_SURPLUS', 'nama_event' => 'Audit Kas: Surplus/Lebih', 'akun_debit_id' => 1, 'akun_kredit_id' => 11, 'created_at' => '2026-05-30 18:32:34', 'updated_at' => '2026-05-30 18:32:34'],
            ['id' => 9, 'kode_event' => 'POS_SALE_QRIS', 'nama_event' => 'Penjualan POS QRIS', 'akun_debit_id' => 4, 'akun_kredit_id' => 10, 'created_at' => '2026-05-30 18:32:34', 'updated_at' => '2026-05-30 18:32:34'],
            ['id' => 10, 'kode_event' => 'POS_SALE_BANK', 'nama_event' => 'Penjualan POS Transfer', 'akun_debit_id' => 3, 'akun_kredit_id' => 10, 'created_at' => '2026-05-30 18:32:34', 'updated_at' => '2026-05-30 18:32:34'],
            ['id' => 11, 'kode_event' => 'PURCHASE_TUNAI', 'nama_event' => 'Pembelian Stok Barang (Tunai)', 'akun_debit_id' => 6, 'akun_kredit_id' => 2, 'created_at' => '2026-05-30 18:32:34', 'updated_at' => '2026-05-30 18:32:34'],
            ['id' => 12, 'kode_event' => 'PURCHASE_BANK', 'nama_event' => 'Pembelian Stok Barang (Transfer/Bank)', 'akun_debit_id' => 6, 'akun_kredit_id' => 3, 'created_at' => '2026-05-30 18:32:34', 'updated_at' => '2026-05-30 18:32:34'],
            ['id' => 13, 'kode_event' => 'PURCHASE_TEMPO', 'nama_event' => 'Pembelian Stok Barang (Tempo/Kredit)', 'akun_debit_id' => 6, 'akun_kredit_id' => 7, 'created_at' => '2026-05-30 18:32:34', 'updated_at' => '2026-05-30 18:32:34'],
            ['id' => 14, 'kode_event' => 'SHIFT_KASIR', 'nama_event' => 'Modal Buka Shift Kasir', 'akun_debit_id' => 1, 'akun_kredit_id' => 2, 'created_at' => '2026-05-30 18:32:34', 'updated_at' => '2026-05-30 18:32:34'],
        ];

        DB::table('akun_cfg')->insert($data);
    }
}
