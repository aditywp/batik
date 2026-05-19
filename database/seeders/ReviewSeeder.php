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

        // Ambil semua pesanan yang statusnya sudah 'delivered' (selesai) beserta item produknya
        $completedOrders = Order::where('status', 'delivered')->with('items')->get();

        if ($completedOrders->isEmpty()) {
            return;
        }

        // Kumpulan template komentar ulasan batik yang realistis berdasarkan rating
        $positiveComments = [
            'Bahan batiknya halus banget, adem pas dipakai. Motifnya juga sangat detail dan premium!',
            'Kualitas jahitan rapi sekali, pengemasan sangat aman dan cepat sampai. Puas belanja disini.',
            'Warnanya pas banget di foto, ukurannya sesuai dengan panduan. Recomended seller!',
            'Produk sangat eksklusif, cocok untuk acara formal maupun kerja sehari-hari. Kainnya tidak luntur pas dicuci.',
            'Suka banget sama motif parangnya, terlihat gagah dan elegan. Pertahankan kualitasnya!'
        ];

        $neutralComments = [
            'Bahan lumayan bagus untuk harga segini, tapi proses pengemasan agak sedikit lama.',
            'Kain batiknya agak sedikit tipis tapi motifnya bagus banget, secara keseluruhan ok.',
            'Ukurannya agak ngepas di badan saya, untung motif batiknya cantik jadi ketutup kekurangannya.',
            'Respon adminnya agak slow respon, tapi produknya aman tidak mengecewakan.'
        ];

        $negativeComments = [
            'Ukurannya kekecilan dibanding deskripsi, kainnya agak kaku pas pertama kali dipegang.',
            'Warna batiknya agak sedikit berbeda dengan yang di foto katalog, pudar sedikit.',
            'Jahitannya ada beberapa bagian yang kurang rapi, tolong tingkatkan *quality control*-nya ya.'
        ];

        foreach ($completedOrders as $order) {
            // Kita ulas semua atau beberapa item produk di dalam transaksi sukses ini
            foreach ($order->items as $item) {
                
                // Acak rating (70% puas/sangat puas, 20% cukup, 10% kurang puas)
                $roll = rand(1, 100);
                if ($roll <= 70) {
                    $rating = rand(4, 5);
                    $comment = $faker->randomElement($positiveComments);
                } elseif ($roll <= 90) {
                    $rating = 3;
                    $comment = $faker->randomElement($neutralComments);
                } else {
                    $rating = rand(1, 2);
                    $comment = $faker->randomElement($negativeComments);
                }

                Review::create([
                    'user_id'     => $order->user_id,    // Sesuai user yang membeli barang
                    'product_id'  => $item->product_id,  // Sesuai ID produk yang dibeli
                    'order_id'    => $order->id,          // Link ke ID order terkait
                    'rating'      => $rating,             // Integer 1 - 5
                    'comment'     => $comment,            // Teks komentar ulasan lokal
                    'is_approved' => $faker->randomElement([1, 1, 1, 0]), // 75% otomatis disetujui (1), 25% butuh persetujuan admin (0)
                    'created_at'  => $faker->dateTimeBetween($order->created_at, 'now'), // Ulasan dibuat setelah tanggal order paid/delivered
                    'updated_at'  => now(),
                ]);
            }
        }
    }
}