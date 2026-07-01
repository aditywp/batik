<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dan buat kolom nama produk jika belum ada
        if (!Schema::hasColumn('order_items', 'product_name_snapshot')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->string('product_name_snapshot')->nullable();
            });
        }

        // Cek dan buat kolom harga jika belum ada
        if (!Schema::hasColumn('order_items', 'price_snapshot')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->decimal('price_snapshot', 12, 2)->nullable();
            });
        }

        // Cek dan buat kolom gambar jika belum ada
        if (!Schema::hasColumn('order_items', 'image_snapshot')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->string('image_snapshot')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['product_name_snapshot', 'price_snapshot', 'image_snapshot']);
        });
    }
};