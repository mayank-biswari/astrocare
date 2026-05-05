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
        Schema::create('campaign_leads', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 255);
            $table->date('date_of_birth');
            $table->string('place_of_birth', 255);
            $table->string('phone_number', 20);
            $table->string('email', 255);
            $table->text('message')->nullable();
            $table->string('source', 50)->default('tarot-reading-campaign');
            $table->timestamps();

            $table->index('email');
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_leads');
    }
};
