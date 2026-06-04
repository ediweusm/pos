<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AkunMasterSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id' => 1, 'kode_akun' => '1101', 'nama_akun' => 'Kasir POS', 'tipe_akun' => 'ASET', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
            ['id' => 2, 'kode_akun' => '1102', 'nama_akun' => 'Kas Besar / Brankas', 'tipe_akun' => 'ASET', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
            ['id' => 3, 'kode_akun' => '1103', 'nama_akun' => 'Kas Bank', 'tipe_akun' => 'ASET', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
            ['id' => 4, 'kode_akun' => '1104', 'nama_akun' => 'Saldo QRIS', 'tipe_akun' => 'ASET', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
            ['id' => 5, 'kode_akun' => '1105', 'nama_akun' => 'Piutang Usaha', 'tipe_akun' => 'ASET', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
            ['id' => 6, 'kode_akun' => '1106', 'nama_akun' => 'Persediaan Barang Dagang', 'tipe_akun' => 'ASET', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
            ['id' => 7, 'kode_akun' => '2101', 'nama_akun' => 'Hutang Usaha', 'tipe_akun' => 'KEWAJIBAN', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
            ['id' => 8, 'kode_akun' => '3101', 'nama_akun' => 'Modal Awal', 'tipe_akun' => 'EKUITAS', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
            ['id' => 9, 'kode_akun' => '3102', 'nama_akun' => 'Prive', 'tipe_akun' => 'EKUITAS', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
            ['id' => 10, 'kode_akun' => '4101', 'nama_akun' => 'Pendapatan Penjualan', 'tipe_akun' => 'PENDAPATAN', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
            ['id' => 11, 'kode_akun' => '4102', 'nama_akun' => 'Pendapatan Penyesuaian (Surplus)', 'tipe_akun' => 'PENDAPATAN', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
            ['id' => 12, 'kode_akun' => '5101', 'nama_akun' => 'Harga Pokok Penjualan (HPP)', 'tipe_akun' => 'BEBAN', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
            ['id' => 13, 'kode_akun' => '6101', 'nama_akun' => 'Beban Susut & Kehilangan Stok', 'tipe_akun' => 'BEBAN', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
            ['id' => 14, 'kode_akun' => '6102', 'nama_akun' => 'Beban Potongan/Admin QRIS (MDR)', 'tipe_akun' => 'BEBAN', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
            ['id' => 15, 'kode_akun' => '6103', 'nama_akun' => 'Beban Gaji Karyawan', 'tipe_akun' => 'BEBAN', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
            ['id' => 16, 'kode_akun' => '6104', 'nama_akun' => 'Beban Operasional Lainnya', 'tipe_akun' => 'BEBAN', 'is_aktif' => 1, 'created_at' => '2026-05-30 18:32:00', 'updated_at' => '2026-05-30 18:32:00'],
        ];

        DB::table('akun_master')->insert($data);
    }
}