<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add username column to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 255)->after('name');
        });

        // Generate usernames from existing names
        $users = DB::table('users')->get();
        $usernameCounts = [];
        
        foreach ($users as $user) {
            $baseUsername = strtolower(str_replace(' ', '.', trim($user->name)));
            $username = $baseUsername;
            $counter = 1;
            
            // Ensure uniqueness
            while (isset($usernameCounts[$username])) {
                $username = $baseUsername . '.' . $counter;
                $counter++;
            }
            
            $usernameCounts[$username] = true;
            
            DB::table('users')
                ->where('id', $user->id)
                ->update(['username' => $username]);
        }

        // Make username unique and not null
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 255)->unique()->change();
        });

        // Drop email-related columns
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
            $table->dropColumn(['email', 'email_verified_at']);
        });

        // Update password_reset_tokens table
        if (Schema::hasTable('password_reset_tokens')) {
            // Drop primary key
            DB::statement('ALTER TABLE password_reset_tokens DROP PRIMARY KEY');
            
            // Rename email column to username
            DB::statement('ALTER TABLE password_reset_tokens CHANGE email username VARCHAR(255) NOT NULL');
            
            // Add primary key on username
            DB::statement('ALTER TABLE password_reset_tokens ADD PRIMARY KEY (username)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert password_reset_tokens table
        if (Schema::hasTable('password_reset_tokens')) {
            // Drop primary key
            DB::statement('ALTER TABLE password_reset_tokens DROP PRIMARY KEY');
            
            // Rename username column back to email
            DB::statement('ALTER TABLE password_reset_tokens CHANGE username email VARCHAR(255) NOT NULL');
            
            // Add primary key on email
            DB::statement('ALTER TABLE password_reset_tokens ADD PRIMARY KEY (email)');
        }

        // Re-add email columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('email', 255)->after('name');
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });

        // Drop username column
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_username_unique');
            $table->dropColumn('username');
        });

        // Re-add unique constraint on email
        Schema::table('users', function (Blueprint $table) {
            $table->unique('email');
        });
    }
};
