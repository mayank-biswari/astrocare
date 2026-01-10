<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admin_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['products', 'pages']);
            $table->enum('method', ['sql', 'manual', 'query_builder']);
            $table->longText('configuration'); // JSON data for query/selection
            $table->boolean('is_active')->default(true);
            $table->boolean('is_template')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_lists');
    }
};