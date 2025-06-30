<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any users who might have empty nama_lengkap but have data in name field
        // This handles cases where users were created before the proper field structure
        DB::statement("UPDATE users SET nama_lengkap = name WHERE nama_lengkap IS NULL OR nama_lengkap = ''");
        
        // Remove the old 'name' column if it exists (it shouldn't based on our current schema)
        if (Schema::hasColumn('users', 'name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the name column and copy data from nama_lengkap
        if (!Schema::hasColumn('users', 'name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('name')->after('id');
            });
            
            DB::statement("UPDATE users SET name = nama_lengkap");
        }
    }
};
