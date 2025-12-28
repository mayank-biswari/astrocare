<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_page_id')->constrained()->onDelete('cascade');
            $table->string('language_code', 10);
            $table->string('title');
            $table->text('body');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->timestamps();
            
            $table->unique(['cms_page_id', 'language_code']);
            $table->foreign('language_code')->references('code')->on('languages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_page_translations');
    }
};