<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // 1. Hapus aturan Foreign Key lama (yang mengunci produk agar tidak bisa dihapus)
            $table->dropForeign(['product_id']);
            $table->dropForeign(['variant_id']);

            // 2. Buat aturan Foreign Key baru (Jika produk dihapus permanen, ID di sini jadi NULL saja)
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('set null'); // <--- INI KUNCINYA

            $table->foreign('variant_id')
                  ->references('id')
                  ->on('product_variants')
                  ->onDelete('set null'); // <--- INI KUNCINYA
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Mengembalikan ke aturan semula (RESTRICT) jika migration di-rollback
            $table->dropForeign(['product_id']);
            $table->dropForeign(['variant_id']);

            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('restrict');

            $table->foreign('variant_id')
                  ->references('id')
                  ->on('product_variants')
                  ->onDelete('restrict');
        });
    }
};