<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_lists', function (Blueprint $table) {
            $table->integer('items_per_page')->default(12);
        });
    }

    public function down(): void
    {
        Schema::table('admin_lists', function (Blueprint $table) {
            $table->dropColumn('items_per_page');
        });
    }
};