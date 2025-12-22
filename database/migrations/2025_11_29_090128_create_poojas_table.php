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
        Schema::create('poojas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // temple, home, jaap_homam, special_occasion, pandit_booking
            $table->string('category')->nullable(); // health, marriage, wealth, education, peace
            $table->text('description');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->datetime('scheduled_at');
            $table->string('location')->nullable();
            $table->string('status')->default('booked'); // booked, confirmed, completed, cancelled
            $table->text('special_requirements')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poojas');
    }
};
