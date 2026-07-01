<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ==========================================
        // 1. PERBAIKAN TABEL ORDER_ITEMS (RIWAYAT PESANAN)
        // ==========================================
        Schema::table('order_items', function (Blueprint $table) {
            // Hapus ikatan lama
            $table->dropForeign(['product_id']);
            $table->dropForeign(['variant_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            // Ubah struktur kolom menjadi Boleh Kosong (Nullable)
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->unsignedBigInteger('variant_id')->nullable()->change();

            // Pasang ikatan baru: Jika produk/varian dihapus, jadikan NULL
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('set null');
        });

        // ==========================================
        // 2. PERBAIKAN TABEL CART_ITEMS (KERANJANG BELANJA)
        // ==========================================
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            // Jika produk dihapus permanen, otomatis hapus juga dari keranjang semua user
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // (Kosongkan saja down-nya karena kita ingin perubahan ini permanen)
    }
};