<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id' => 1, 'kategori_id' => 1, 'satuan_dasar_id' => 1, 'nama' => 'Pakan Broiler Starter BR-1', 'sku' => 'PRD-001', 'barcode' => '899123456001', 'stok_minimum' => 10.00, 'is_aktif' => 1, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 2, 'kategori_id' => 2, 'satuan_dasar_id' => 1, 'nama' => 'Konsentrat Sapi Perah', 'sku' => 'PRD-002', 'barcode' => '899123456002', 'stok_minimum' => 5.00, 'is_aktif' => 1, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 3, 'kategori_id' => 2, 'satuan_dasar_id' => 2, 'nama' => 'Dedak Halus / Bekatul', 'sku' => 'PRD-003', 'barcode' => '899123456003', 'stok_minimum' => 50.00, 'is_aktif' => 1, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 4, 'kategori_id' => 3, 'satuan_dasar_id' => 5, 'nama' => 'Vitachick 50gr', 'sku' => 'PRD-004', 'barcode' => '899123456004', 'stok_minimum' => 20.00, 'is_aktif' => 1, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 5, 'kategori_id' => 3, 'satuan_dasar_id' => 4, 'nama' => 'Vaksin ND Clone', 'sku' => 'PRD-005', 'barcode' => '899123456005', 'stok_minimum' => 5.00, 'is_aktif' => 1, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 6, 'kategori_id' => 4, 'satuan_dasar_id' => 5, 'nama' => 'Tempat Minum Ayam (TMA) 1 Galon', 'sku' => 'PRD-006', 'barcode' => '899123456006', 'stok_minimum' => 10.00, 'is_aktif' => 1, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 7, 'kategori_id' => 4, 'satuan_dasar_id' => 5, 'nama' => 'Lampu Pemanas (Infrared) 100W', 'sku' => 'PRD-007', 'barcode' => '899123456007', 'stok_minimum' => 5.00, 'is_aktif' => 1, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
        ];

        DB::table('produk')->insert($data);
    }
}