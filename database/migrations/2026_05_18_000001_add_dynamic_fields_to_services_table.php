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
        // Add columns only if they don't already exist (idempotent for partial runs)
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('services', 'short_description')) {
                $table->text('short_description')->nullable()->after('type');
            }
            if (!Schema::hasColumn('services', 'image')) {
                $table->string('image')->nullable()->after('description');
            }
            if (!Schema::hasColumn('services', 'icon')) {
                $table->string('icon', 100)->nullable()->after('image');
            }
            if (!Schema::hasColumn('services', 'has_tiers')) {
                $table->boolean('has_tiers')->default(false)->after('currency');
            }
            if (!Schema::hasColumn('services', 'faq')) {
                $table->json('faq')->nullable()->after('features');
            }
            if (!Schema::hasColumn('services', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('faq');
            }
            if (!Schema::hasColumn('services', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('services', 'meta_keywords')) {
                $table->string('meta_keywords')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('services', 'requires_auth')) {
                $table->boolean('requires_auth')->default(true)->after('meta_keywords');
            }
            if (!Schema::hasColumn('services', 'requires_captcha')) {
                $table->boolean('requires_captcha')->default(true)->after('requires_auth');
            }
            if (!Schema::hasColumn('services', 'requires_shipping')) {
                $table->boolean('requires_shipping')->default(false)->after('requires_captcha');
            }
            if (!Schema::hasColumn('services', 'delivery_time')) {
                $table->string('delivery_time', 100)->nullable()->after('requires_shipping');
            }
            if (!Schema::hasColumn('services', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
        });

        // Rename price to base_price if not already done
        if (Schema::hasColumn('services', 'price') && !Schema::hasColumn('services', 'base_price')) {
            Schema::table('services', function (Blueprint $table) {
                $table->renameColumn('price', 'base_price');
            });
        }

        // Normalize existing type values before converting to enum
        \Illuminate\Support\Facades\DB::table('services')
            ->where('type', 'horoscope_matching')
            ->update(['type' => 'matching']);

        // Map any other non-standard values to 'custom'
        \Illuminate\Support\Facades\DB::table('services')
            ->whereNotIn('type', ['question', 'prediction', 'kundli', 'consultation', 'pooja', 'matching', 'custom'])
            ->update(['type' => 'custom']);

        // For MySQL: Change type column to enum (only if it's still VARCHAR)
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            $columnType = \Illuminate\Support\Facades\DB::selectOne("SHOW COLUMNS FROM services WHERE Field = 'type'")->Type ?? '';
            if (stripos($columnType, 'enum') === false) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE services MODIFY COLUMN type ENUM('question', 'prediction', 'kundli', 'consultation', 'pooja', 'matching', 'custom') NOT NULL");
            }
        }

        // Change description to longText and make nullable
        Schema::table('services', function (Blueprint $table) {
            $table->longText('description')->nullable()->change();
        });

        // Set default for base_price (only if column exists)
        if (Schema::hasColumn('services', 'base_price')) {
            Schema::table('services', function (Blueprint $table) {
                $table->decimal('base_price', 10, 2)->default(0)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For MySQL: Revert type column back to string
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE services MODIFY COLUMN type VARCHAR(255) NOT NULL");
        }

        Schema::table('services', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn([
                'slug',
                'short_description',
                'image',
                'icon',
                'has_tiers',
                'faq',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'requires_auth',
                'requires_captcha',
                'requires_shipping',
                'delivery_time',
                'sort_order',
            ]);

            // Rename base_price back to price
            $table->renameColumn('base_price', 'price');

            // Revert description to text not null
            $table->text('description')->nullable(false)->change();
        });
    }
};
