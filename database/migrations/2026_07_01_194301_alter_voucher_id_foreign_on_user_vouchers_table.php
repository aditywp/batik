<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_vouchers', function (Blueprint $table) {
            // Hapus foreign key RESTRICT lama
            $table->dropForeign(['voucher_id']);
        });

        Schema::table('user_vouchers', function (Blueprint $table) {
            // Ubah kolom agar boleh kosong (nullable)
            $table->unsignedBigInteger('voucher_id')->nullable()->change();

            // Pasang foreign key baru dengan aturan SET NULL
            $table->foreign('voucher_id')
                  ->references('id')
                  ->on('vouchers')
                  ->onDelete('set null'); // Jika master dihapus, id di dompet jadi null, tapi data snapshot tetap aman
        });
    }

    public function down(): void
    {
        // Kosongkan untuk menghindari rollback error constraint
    }
};