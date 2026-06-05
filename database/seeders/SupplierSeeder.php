<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'kode_supplier' => 'SUP-001',
                'nama_perusahaan' => 'PT. Jaya Mandiri',
                'nama_pic' => 'Budi Santoso',
                'no_telepon' => '081234567890',
                'alamat' => 'Jl. Kawasan Industri No. 45, Jakarta',
                'email' => 'sales@jayamandiri.com',
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'kode_supplier' => 'SUP-002',
                'nama_perusahaan' => 'CV. Pakan Makmur',
                'nama_pic' => 'Siti Aminah',
                'no_telepon' => '082345678901',
                'alamat' => 'Jl. Pertanian Raya No. 12, Bogor',
                'email' => 'info@pakanmakmur.com',
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('supplier')->insert($data);
    }
}
