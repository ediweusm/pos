<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AkunMasterSeeder::class,
            AkunCfgSeeder::class,
            SatuanSeeder::class,
            KategoriSeeder::class,
            ProdukSeeder::class,       
            HargaProdukSeeder::class,  
            SupplierSeeder::class,
            GudangSeeder::class,
        ]);
    }
}