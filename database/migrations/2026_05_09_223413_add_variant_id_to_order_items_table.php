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
            // Menambahkan kolom variant_id setelah product_id
            // nullable() digunakan jika ada produk yang tidak punya varian
            // constrained() otomatis menghubungkan ke tabel product_variants
            $table->foreignId('variant_id')
                  ->nullable()
                  ->after('product_id')
                  ->constrained('product_variants')
                  ->onDelete('set null'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Hapus constraint foreign key dulu baru hapus kolomnya
            $table->dropForeign(['variant_id']);
            $table->dropColumn('variant_id');
        });
    }
};