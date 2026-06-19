<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Voucher Diskon 20rb
            $table->string('code')->unique(); // Contoh: WELCOME20
            $table->decimal('discount_amount', 10, 2); // Jumlah potongan harga
            $table->integer('points_required')->default(0); // Poin yg dibutuhkan (0 = gratis/welcome voucher)
            $table->boolean('is_welcome_voucher')->default(false); // True jika ini voucher khusus pendaftar baru
            $table->boolean('is_active')->default(true);
            $table->date('valid_until')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};