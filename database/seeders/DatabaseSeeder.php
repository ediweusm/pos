<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Cabang;
use App\Models\Gudang;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Cabang Pertama (Toko Pusat)
        $cabangPusat = Cabang::create([
            'kode_cabang' => 'CBG-001',
            'nama_cabang' => 'Toko Pusat Utama',
            'alamat'      => 'Jl. Raya Utama No. 1',
            'is_aktif'    => true,
        ]);

        // 2. Buat Gudang Penyimpanan & Etalase untuk Cabang Pusat
        $gudangBelakang = Gudang::create([
            'cabang_id' => $cabangPusat->id,
            'nama'      => 'Gudang Belakang (Penyimpanan)',
            'tipe'      => 'PENYIMPANAN',
        ]);

        $etalaseDepan = Gudang::create([
            'cabang_id' => $cabangPusat->id,
            'nama'      => 'Etalase Toko (Kasir)',
            'tipe'      => 'ETALASE',
        ]);

        // 3. Buat User Administrator
        User::create([
            'name'      => 'Administrator',
            'email'     => 'admin@admin.com',
            'password'  => Hash::make('password'),
            'cabang_id' => $cabangPusat->id, 
            'gudang_id' => $etalaseDepan->id, 
        ]);
    }
}
