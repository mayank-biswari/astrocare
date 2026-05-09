<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add lead_code column as nullable initially for migration safety
        Schema::table('campaign_leads', function (Blueprint $table) {
            $table->string('lead_code', 11)->nullable()->after('id');
            $table->unique('lead_code');
        });

        // Step 2: Generate lead codes for all existing records
        $processed = 0;
        $leads = DB::table('campaign_leads')->whereNull('lead_code')->get(['id']);

        foreach ($leads as $lead) {
            $leadCode = $this->generateUniqueLeadCode();
            DB::table('campaign_leads')
                ->where('id', $lead->id)
                ->update(['lead_code' => $leadCode]);
            $processed++;
        }

        // Step 3: Alter column to non-nullable after backfill
        Schema::table('campaign_leads', function (Blueprint $table) {
            $table->string('lead_code', 11)->nullable(false)->change();
        });

        // Step 4: Log the count of records processed
        Log::info("Lead code migration completed: {$processed} records processed.");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign_leads', function (Blueprint $table) {
            $table->dropUnique(['lead_code']);
            $table->dropColumn('lead_code');
        });
    }

    /**
     * Generate a unique lead code with collision retry (max 5 attempts).
     *
     * Format: "LD-" + 8 uppercase alphanumeric characters
     */
    private function generateUniqueLeadCode(): string
    {
        $maxAttempts = 5;
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $randomPart = '';
            for ($i = 0; $i < 8; $i++) {
                $randomPart .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $code = 'LD-' . $randomPart;

            $exists = DB::table('campaign_leads')
                ->where('lead_code', $code)
                ->exists();

            if (!$exists) {
                return $code;
            }
        }

        throw new \RuntimeException(
            "Failed to generate a unique lead code after {$maxAttempts} attempts."
        );
    }
};
