<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_lists', function (Blueprint $table) {
            $table->boolean('create_page')->default(false);
            $table->string('page_title')->nullable();
            $table->string('page_slug')->nullable();
            $table->text('page_description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('admin_lists', function (Blueprint $table) {
            $table->dropColumn(['create_page', 'page_title', 'page_slug', 'page_description']);
        });
    }
};