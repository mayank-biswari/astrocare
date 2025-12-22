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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->string('status')->default('pending'); // pending, paid, shipped, delivered, cancelled
            $table->json('items'); // product details, quantities, prices
            $table->json('shipping_address');
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending');
            $table->datetime('shipped_at')->nullable();
            $table->datetime('delivered_at')->nullable();
            $table->timestamps();
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
