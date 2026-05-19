<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Kemeja Batik Pria',
            'Busana Batik Wanita',
            'Batik Sarimbit (Couple)',
            'Kain Batik Tulis',
            'Kain Batik Cap',
            'Batik Anak',
            'Aksesoris Batik',
            'Koleksi Premium',
            'Sprei & Home Decor',
            'Outer & Cardigan',
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'name'      => $categoryName,
                'slug'      => Str::slug($categoryName),
                'is_active' => 1, // Default aktif sesuai metadata DefaultText=1
            ]);
        }
    }
}