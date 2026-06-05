<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cabang;
use App\Models\Gudang;

class GudangSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Cabang Pertama (Toko Pusat)
        $cabangPusat = Cabang::updateOrCreate(
            ['id' => 1],
            [
                'kode_cabang' => 'CBG-001',
                'nama_cabang' => 'Toko Pusat Utama',
                'alamat'      => 'Jl. Raya Utama No. 1',
                'is_aktif'    => true,
            ]
        );

        // 2. Buat Gudang Penyimpanan & Etalase untuk Cabang Pusat
        Gudang::updateOrCreate(
            ['id' => 1],
            [
                'cabang_id' => $cabangPusat->id,
                'nama'      => 'Gudang Belakang (Penyimpanan)',
                'tipe'      => 'PENYIMPANAN',
            ]
        );

        Gudang::updateOrCreate(
            ['id' => 2],
            [
                'cabang_id' => $cabangPusat->id,
                'nama'      => 'Etalase Toko (Kasir)',
                'tipe'      => 'ETALASE',
            ]
        );
    }
}
