<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_vouchers', function (Blueprint $table) {
            $table->string('code_snapshot')->nullable()->after('is_used');
            $table->decimal('discount_snapshot', 12, 2)->nullable()->after('code_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('user_vouchers', function (Blueprint $table) {
            $table->dropColumn(['code_snapshot', 'discount_snapshot']);
        });
    }
};