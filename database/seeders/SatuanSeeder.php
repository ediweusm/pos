<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id' => 1, 'nama' => 'Sak (50kg)', 'simbol' => 'Sak', 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 2, 'nama' => 'Kilogram', 'simbol' => 'Kg', 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 3, 'nama' => 'Liter', 'simbol' => 'L', 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 4, 'nama' => 'Botol', 'simbol' => 'Btl', 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 5, 'nama' => 'Pieces', 'simbol' => 'Pcs', 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
        ];

        DB::table('satuan')->insert($data);
    }
}