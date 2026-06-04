<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id' => 1, 'nama' => 'Pakan Unggas', 'deskripsi' => 'Segala jenis pakan untuk ayam, bebek, dan puyuh', 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 2, 'nama' => 'Pakan Ruminansia', 'deskripsi' => 'Pakan konsentrat dan hijauan untuk sapi, kambing, domba', 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 3, 'nama' => 'Obat & Vitamin', 'deskripsi' => 'Suplemen, vaksin, dan obat-obatan hewan', 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
            ['id' => 4, 'nama' => 'Peralatan Ternak', 'deskripsi' => 'Tempat pakan, minum, lampu pemanas, dan alat kandang', 'created_at' => '2026-06-04 10:00:00', 'updated_at' => '2026-06-04 10:00:00'],
        ];

        DB::table('kategori')->insert($data);
    }
}