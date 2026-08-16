<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('POS_SEED_ADMIN_EMAIL');
        $password = env('POS_SEED_ADMIN_PASSWORD');

        if (blank($email) || blank($password)) {
            $this->command?->warn('Admin seed skipped: set POS_SEED_ADMIN_EMAIL and POS_SEED_ADMIN_PASSWORD explicitly.');
            return;
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
            ]
        );
        
        // Berikan kunci master secara otomatis!
        $user->assignRole('super_admin');
    }
}
