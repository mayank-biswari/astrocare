<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->string('language_code', 10)->default('en')->after('custom_fields');
            $table->foreign('language_code')->references('code')->on('languages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->dropForeign(['language_code']);
            $table->dropColumn('language_code');
        });
    }
};