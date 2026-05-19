<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shipping_rates')->insert([

            [
                'city' => 'Jakarta',
                'cost' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'city' => 'Bandung',
                'cost' => 15000,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'city' => 'Surabaya',
                'cost' => 12000,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'city' => 'Yogyakarta',
                'cost' => 9000,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'city' => 'Semarang',
                'cost' => 11000,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'city' => 'Bali',
                'cost' => 25000,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'city' => 'Malang',
                'cost' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'city' => 'Mataram',
                'cost' => 18000,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}