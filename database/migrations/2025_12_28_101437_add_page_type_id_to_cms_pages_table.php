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
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->foreignId('cms_page_type_id')->nullable()->constrained()->onDelete('set null');
            $table->json('custom_fields')->nullable(); // Store custom field values
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->dropForeign(['cms_page_type_id']);
            $table->dropColumn(['cms_page_type_id', 'custom_fields']);
        });
    }
};
