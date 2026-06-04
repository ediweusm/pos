<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HargaProdukSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // 1. Pakan Broiler Starter BR-1 (Satuan: Sak)
            ['id' => 1, 'produk_id' => 1, 'satuan_id' => 1, 'tipe_harga' => 'ECERAN', 'minimal_qty' => 1.00, 'harga' => 375000.00, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 2, 'produk_id' => 1, 'satuan_id' => 1, 'tipe_harga' => 'GROSIR', 'minimal_qty' => 10.00, 'harga' => 365000.00, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],

            // 2. Konsentrat Sapi Perah (Satuan: Sak)
            ['id' => 3, 'produk_id' => 2, 'satuan_id' => 1, 'tipe_harga' => 'ECERAN', 'minimal_qty' => 1.00, 'harga' => 200000.00, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 4, 'produk_id' => 2, 'satuan_id' => 1, 'tipe_harga' => 'GROSIR', 'minimal_qty' => 5.00, 'harga' => 190000.00, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],

            // 3. Dedak Halus / Bekatul (Satuan: Kilogram)
            ['id' => 5, 'produk_id' => 3, 'satuan_id' => 2, 'tipe_harga' => 'ECERAN', 'minimal_qty' => 1.00, 'harga' => 4500.00, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 6, 'produk_id' => 3, 'satuan_id' => 2, 'tipe_harga' => 'GROSIR', 'minimal_qty' => 50.00, 'harga' => 4000.00, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],

            // 4. Vitachick 50gr (Satuan: Pieces)
            ['id' => 7, 'produk_id' => 4, 'satuan_id' => 5, 'tipe_harga' => 'ECERAN', 'minimal_qty' => 1.00, 'harga' => 15000.00, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 8, 'produk_id' => 4, 'satuan_id' => 5, 'tipe_harga' => 'GROSIR', 'minimal_qty' => 10.00, 'harga' => 13500.00, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],

            // 5. Vaksin ND Clone (Satuan: Botol)
            ['id' => 9, 'produk_id' => 5, 'satuan_id' => 4, 'tipe_harga' => 'ECERAN', 'minimal_qty' => 1.00, 'harga' => 55000.00, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],

            // 6. Tempat Minum Ayam 1 Galon (Satuan: Pieces)
            ['id' => 10, 'produk_id' => 6, 'satuan_id' => 5, 'tipe_harga' => 'ECERAN', 'minimal_qty' => 1.00, 'harga' => 32000.00, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 11, 'produk_id' => 6, 'satuan_id' => 5, 'tipe_harga' => 'GROSIR', 'minimal_qty' => 12.00, 'harga' => 29000.00, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],

            // 7. Lampu Pemanas 100W (Satuan: Pieces)
            ['id' => 12, 'produk_id' => 7, 'satuan_id' => 5, 'tipe_harga' => 'ECERAN', 'minimal_qty' => 1.00, 'harga' => 80000.00, 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
        ];

        DB::table('harga_produk')->insert($data);
    }
}