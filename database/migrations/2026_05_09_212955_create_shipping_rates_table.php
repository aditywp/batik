<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_rates', function (Blueprint $table) {

            $table->id();

            $table->string('city');

            $table->integer('cost');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};