<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_code', 8)->unique()->nullable()->after('pincode');
        });

        // Generate user_code for existing users
        $this->generateCodesForExistingUsers();
    }

    /**
     * Generate user codes for all existing users that don't have one.
     */
    private function generateCodesForExistingUsers(): void
    {
        $users = User::whereNull('user_code')->get();

        foreach ($users as $user) {
            $user->user_code = User::generateUserCode($user->role ?? 'user');
            $user->saveQuietly();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_code');
        });
    }
};
