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
        Schema::table('cms_page_products', function (Blueprint $table) {
            $table->integer('min_quantity')->default(1)->after('stock_quantity');
            $table->integer('quantity_step')->default(1)->after('min_quantity');
            $table->string('quantity_unit')->default('item')->after('quantity_step');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_page_products', function (Blueprint $table) {
            $table->dropColumn(['min_quantity', 'quantity_step', 'quantity_unit']);
        });
    }
};
