<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil semua pesanan delivered beserta item dan detail produknya
        $completedOrders = Order::where('status', 'delivered')->with('items.product')->get();

        if ($completedOrders->isEmpty()) {
            return;
        }

        foreach ($completedOrders as $order) {
            foreach ($order->items as $item) {
                
                // Mengambil nama produk secara dinamis untuk variasi ulasan
                $productName = $item->product ? $item->product->name : 'produk batik';

                $positivePhrases = [
                    "Bahan kain {$productName} ini beneran halus dan premium banget.",
                    "Motifnya rapi sekali, detail canting tulisnya sangat terasa eksklusif.",
                    "Warna kainnya pas sesuai ekspektasi, pas dipakai langsung kelihatan elegan.",
                    "Kualitas jahitan juara, potongan pas di badan.",
                    "Nyaman banget dipakai seharian, kainnya adem dan tidak kaku.",
                    "Pengiriman cepat sekali, packaging aman terlindungi.",
                    "Suka banget sama perpaduan warnanya, terlihat berkelas.",
                    "Sangat puas belanja di sini, bakalan jadi langganan tetap!"
                ];

                $neutralPhrases = [
                    "Kualitas kain {$productName} lumayan oke untuk harga segini.",
                    "Motif batiknya cantik banget, cuma proses pengirimannya aja yang agak lambat.",
                    "Bahan agak sedikit tipis tapi motif dan warnanya juara, secara keseluruhan memuaskan.",
                    "Ukuran agak sedikit ngepas di badan, untung modelnya fleksibel.",
                    "Respon adminnya agak slow respon waktu dichat, tapi produknya aman tidak mengecewakan."
                ];

                $negativePhrases = [
                    "Bahan kain {$productName} agak kaku pas pertama datang, jahitannya perlu lebih dirapikan.",
                    "Warna batiknya sedikit berbeda dengan foto katalognya, agak pudar sedikit.",
                    "Ukurannya agak kekecilan dibanding deskripsi panduan size chart.",
                    "Ada bagian benang yang lepas di jahitan samping, tolong QC-nya ditingkatkan lagi ya."
                ];

                // Penentuan Rating & Komparasi Teks Komentar
                $roll = rand(1, 100);
                if ($roll <= 70) {
                    $rating = rand(4, 5);
                    $comment = implode(' ', $faker->randomElements($positivePhrases, rand(1, 2)));
                } elseif ($roll <= 90) {
                    $rating = 3;
                    $comment = $faker->randomElement($neutralPhrases);
                } else {
                    $rating = rand(1, 2);
                    $comment = $faker->randomElement($negativePhrases);
                }

                // Menggunakan updateOrCreate untuk menghindari error duplicate entry pada unique key
                Review::updateOrCreate(
                    [
                        'user_id'     => $order->user_id,
                        'product_id'  => $item->product_id,
                        'order_id'    => $order->id,
                    ],
                    [
                        'rating'      => $rating,
                        'comment'     => $comment,
                        'is_approved' => $faker->randomElement([1, 1, 1, 0]), 
                        'created_at'  => $faker->dateTimeBetween($order->created_at, 'now'), 
                        'updated_at'  => now(),
                    ]
                );
            }
        }
    }
}