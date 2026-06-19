<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voucher;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat Welcome Voucher (Otomatis untuk pendaftar baru)
        Voucher::create([
            'name' => 'Diskon Pengguna Baru',
            'code' => 'WELCOMEIFWTI',
            'discount_amount' => 50000, // Diskon Rp 50.000
            'points_required' => 0,     // Gratis, tidak butuh poin
            'is_welcome_voucher' => true,
            'is_active' => true,
        ]);

        // Membuat Voucher yang bisa ditukar dengan poin
        Voucher::create([
            'name' => 'Potongan Spesial Pelanggan Setia',
            'code' => 'LOYAL50',
            'discount_amount' => 30000, // Diskon Rp 30.000
            'points_required' => 100,   // Butuh 100 Poin untuk ditukar
            'is_welcome_voucher' => false,
            'is_active' => true,
        ]);
    }
}