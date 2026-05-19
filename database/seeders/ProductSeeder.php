<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua kategori yang sudah kamu buat
        $categories = Category::all();

        // Data pola untuk variasi
        $motifs = ['Parang Kusumo', 'Mega Mendung', 'Kawung Modern', 'Truntum Garuda', 'Sekar Jagad', 'Sido Mukti', 'Lereng Sakti', 'Ceplok Mataram'];
        $colors = ['Royal Blue', 'Deep Maroon', 'Classic Soga', 'Emerald Green', 'Charcoal Grey', 'Golden Teracotta'];
        $collections = ['Women', 'Men', 'Kids', 'Craft', 'Family'];
        $sizes = ['S', 'M', 'L', 'XL', 'XXL', 'N/A'];

        foreach ($categories as $category) {
            // Buat 5 produk untuk setiap kategori (Total 50 Produk)
            for ($i = 1; $i <= 5; $i++) {
                $motifName = $motifs[array_rand($motifs)];
                $colorName = $colors[array_rand($colors)];
                $colName = $collections[array_rand($collections)];
                
                $productName = "Batik " . $category->name . " " . $motifName . " " . $colorName;
                $basePrice = rand(15, 85) * 10000; // Harga dasar 150rb - 850rb

                // 1. Simpan ke tabel Products
                $product = Product::create([
                    'category_id' => $category->id,
                    'collection'  => $colName,
                    'name'        => $productName,
                    'slug'        => Str::slug($productName) . '-' . Str::random(5),
                    'description' => "Koleksi $productName dari Batik Ifawati. Produk ini menggunakan bahan katun primisima premium dengan teknik pewarnaan tradisional yang tahan lama. Desain $motifName memberikan kesan elegan dan berwibawa bagi pemakainya.",
                    'price'       => $basePrice,
                    'stock'       => 0, // Akan dihitung dari total stok varian
                    'image'       => null,
                    'is_active'   => 1,
                ]);

                // 2. Simpan ke tabel ProductVariants (Motif & Ukuran)
                // Kita buat 2-3 motif per produk agar variasi terlihat banyak di UI
                $numMotifs = rand(1, 2);
                $totalStock = 0;

                for ($m = 1; $m <= $numMotifs; $m++) {
                    $currentMotif = ($m == 1) ? $motifName : $motifs[array_rand($motifs)] . " Alt";
                    
                    // Buat 3-4 ukuran untuk motif tersebut
                    $selectedSizes = (array) array_rand(array_flip($sizes), rand(2, 4));
                    
                    foreach ($selectedSizes as $size) {
                        $variantStock = rand(5, 25);
                        $totalStock += $variantStock;

                        ProductVariant::create([
                            'product_id' => $product->id,
                            'motif'      => $currentMotif,
                            'size'       => $size,
                            'price'      => $basePrice + (rand(0, 2) * 5000), // Kadang harga beda tiap ukuran
                            'stock'      => $variantStock,
                            'image_path' => null,
                        ]);
                    }
                }

                // Update total stok di produk utama
                $product->update(['stock' => $totalStock]);
            }
        }
    }
}