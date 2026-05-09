<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_settings', function (Blueprint $table) {
            $table->id();
            $table->string('source', 50);
            $table->string('key', 50);
            $table->string('value', 255);
            $table->timestamps();

            $table->unique(['source', 'key']);
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_settings');
    }
};
