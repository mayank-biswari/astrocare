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
        Schema::table('cms_page_types', function (Blueprint $table) {
            $table->boolean('has_product_fields')->default(false)->after('template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_page_types', function (Blueprint $table) {
            $table->dropColumn('has_product_fields');
        });
    }
};
