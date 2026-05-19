<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil semua customer dari database (Kecuali Admin)
        $customers = User::where('role', 'customer')->get();
        
        // Ambil semua produk beserta variannya untuk dibeli di dalam seeder
        $products = Product::with('variants')->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        // Daftar opsi kurir, layanan, dan status untuk variasi data transaksi
        $courierOptions = [
            ['name' => 'JNE', 'services' => ['REG', 'OKE', 'YES']],
            ['name' => 'J&T', 'services' => ['EZ', 'ECO']],
            ['name' => 'SICEPAT', 'services' => ['REG', 'BEST', 'SIUNTUNG']],
            ['name' => 'POS', 'services' => ['Kilat Khusus', 'Express']]
        ];

        $paymentMethods = ['Gopay', 'OVO', 'ShopeePay', 'BCA Virtual Account', 'Mandiri Virtual Account', 'Credit Card'];

        // Looping untuk membuat 50 data transaksi acak tersebar di antara para customer
        for ($i = 1; $i <= 50; $i++) {
            $customer = $customers->random();
            $courierPair = $faker->randomElement($courierOptions);
            $courierName = $courierPair['name'];
            $courierService = $faker->randomElement($courierPair['services']);
            
            // Logika acak penentuan status agar data bervariasi
            $status = $faker->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']);
            
            // Set status pembayaran berbanding lurus dengan status pesanan
            if (in_array($status, ['processing', 'shipped', 'delivered'])) {
                $paymentStatus = 'paid';
                $paidAt = $faker->dateTimeBetween('-5 months', 'now');
                $createdAt = $paidAt; 
            } else {
                // PERBAIKAN MUTLAK: Dikunci ke 'unpaid' agar lolos dari validasi ENUM MySQL
                $paymentStatus = 'unpaid'; 
                $paidAt = null;
                $createdAt = $faker->dateTimeBetween('-5 months', 'now');
            }

            // PERBAIKAN FORMAL ALAMAT INDONESIA MENGGUNAKAN FAKER LEGAL
            $province = $faker->state; 
            $city = $faker->city;
            $district = 'Kec. ' . $faker->firstNameMale; 
            $postal = $faker->postcode;
            $detailAddress = $faker->streetAddress;
            $phone = '08' . $faker->numerify('##########');
            $fullShippingAddress = "$detailAddress, $district, $city, Prov. $province ($postal) — Telp: $phone";

            // Membuat row data utama transaksi (tabel orders) terlebih dahulu
            $order = Order::create([
                'user_id' => $customer->id,
                'order_code' => 'BI-' . strtoupper(Str::random(4)) . $faker->numerify('#####'),
                'snap_token' => $paymentStatus == 'unpaid' ? Str::random(36) : null,
                'payment_url' => $paymentStatus == 'unpaid' ? 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . Str::random(10) : null,
                'midtrans_transaction_id' => $paymentStatus == 'paid' ? Str::uuid()->toString() : null,
                'subtotal' => 0, // Akan di-update setelah kalkulasi loop item di bawah
                'shipping_cost' => $faker->randomElement([12000, 15000, 22000, 28000, 35000]),
                'courier' => $courierName,
                'courier_service' => $courierService,
                'tracking_number' => in_array($status, ['shipped', 'delivered']) ? 'RESI' . strtoupper($faker->bothify('??#########??')) : null,
                'shipping_weight' => 0, // Akan di-update setelah loop item
                'total' => 0, // Akan di-update setelah loop item
                'status' => $status,
                'shipping_address' => $fullShippingAddress,
                'payment_method' => $paymentStatus == 'paid' ? $faker->randomElement($paymentMethods) : null,
                'payment_status' => $paymentStatus,
                'paid_at' => $paidAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Tentukan berapa banyak jenis barang batik yang dibeli dalam 1 nota (1 - 3 jenis produk)
            $itemsCount = rand(1, 3);
            $orderSubtotal = 0;
            $orderTotalWeight = 0;
            
            // Ambil produk acak untuk transaksi ini
            $randomProducts = $products->random($itemsCount);

            foreach ($randomProducts as $product) {
                $quantity = rand(1, 2);
                
                // Ambil varian acak dari produk jika produk memiliki varian motif/ukuran
                $variant = $product->variants->isNotEmpty() ? $product->variants->random() : null;
                
                // Gunakan harga khusus varian jika ada, jika tidak ada fallback ke harga utama produk
                $itemPrice = ($variant && $variant->price) ? $variant->price : $product->price;
                $itemSubtotal = $itemPrice * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant_id' => $variant ? $variant->id : null,
                    'quantity' => $quantity,
                    'price' => $itemPrice,
                    'subtotal' => $itemSubtotal,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $orderSubtotal += $itemSubtotal;
                $orderTotalWeight += (500 * $quantity); // Asumsi berat per kain/baju batik = 500 gram
            }

            // Update balik data akumulasi item ke dalam row tabel orders utama
            $order->update([
                'subtotal' => $orderSubtotal,
                'shipping_weight' => $orderTotalWeight,
                'total' => $orderSubtotal + $order->shipping_cost
            ]);
        }
    }
}