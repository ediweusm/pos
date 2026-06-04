<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@posme.com'], // Ganti dengan email login Anda
            [
                'name' => 'Super Admin',
                'password' => Hash::make('adminposme123'), // Ganti dengan password Anda
            ]
        );
        
        // Berikan kunci master secara otomatis!
        $user->assignRole('super_admin');
    }
}