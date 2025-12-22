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
        Schema::create('pooja_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->json('includes');
            $table->json('benefits');
            $table->integer('duration')->default(60);
            $table->string('category')->default('temple');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pooja_services');
    }
};
