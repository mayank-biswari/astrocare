<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_page_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->text('comment');
            $table->integer('rating')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_comments');
    }
};
