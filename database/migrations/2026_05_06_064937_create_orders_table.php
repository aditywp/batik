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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Kolom Identitas & Midtrans
            $table->string('order_code')->unique(); // Contoh: BI-20250101-0001
            $table->string('snap_token')->nullable();
            $table->string('payment_url')->nullable();
            $table->string('midtrans_transaction_id')->nullable();

            // Kolom Biaya & Pengiriman
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->string('courier')->nullable();         // JNE, J&T, dll
            $table->string('courier_service')->nullable(); // REG, OKE, YES
            $table->integer('shipping_weight')->nullable(); // dalam gram
            $table->decimal('total', 12, 2);

            // Kolom Status
            $table->enum('status', [
                'pending',
                'processing',
                'shipped',
                'delivered',
                'cancelled',
            ])->default('pending');
            
            $table->text('shipping_address');
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            
            $table->timestamps();

            // Index untuk mempercepat query pencarian pesanan user atau filter status
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};