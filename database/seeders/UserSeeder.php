<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 2. Buat 30 Data Customer / Pelanggan secara acak
        for ($i = 1; $i <= 30; $i++) {
            // Membuat format nama lokal Indonesia yang realistis
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $fullName = $firstName . ' ' . $lastName;
            
            // Generate email unik berdasarkan nama fiktif yang dibuat
            $email = strtolower($firstName . '.' . $lastName . $i . '@example.com');

            User::create([
                'name' => $fullName,
                'email' => $email,
                'password' => Hash::make('password123'), // Semua customer menggunakan password default ini untuk mempermudah testing
                'role' => 'customer', // Sesuai enum 'customer' pada struktur tabel kamu
                'email_verified_at' => now(),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'), // Mengacak tanggal buat simulasi statistik dashboard
                'updated_at' => now(),
            ]);
        }
    }
}