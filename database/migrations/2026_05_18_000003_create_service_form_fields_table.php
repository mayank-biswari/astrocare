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
        Schema::create('service_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('field_name', 100);
            $table->string('field_label', 255);
            $table->enum('field_type', [
                'text', 'email', 'tel', 'date', 'time', 'datetime',
                'select', 'textarea', 'radio', 'checkbox', 'hidden', 'file',
            ]);
            $table->string('placeholder', 255)->nullable();
            $table->json('options')->nullable();
            $table->string('validation_rules', 500)->nullable();
            $table->boolean('is_required')->default(false);
            $table->string('section', 100)->default('default');
            $table->string('section_label', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('help_text', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_form_fields');
    }
};
