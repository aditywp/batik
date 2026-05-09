<?php
// database/seeders/AdminSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@batikifawati.com'],
            [
                'name'     => 'Admin Batik Ifawati',
                'password' => Hash::make('admin123456'),
                'role'     => 'admin',
            ]
        );

        $this->command->info('Admin berhasil dibuat: admin@batikifawati.com / admin123456');
    }
}