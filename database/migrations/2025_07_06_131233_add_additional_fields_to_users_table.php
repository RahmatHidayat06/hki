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
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_ktp')->nullable()->after('email');
            $table->date('tanggal_lahir')->nullable()->after('no_ktp');
            $table->enum('gender', ['L', 'P'])->nullable()->after('tanggal_lahir');
            $table->string('nationality')->default('Indonesia')->after('gender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_ktp', 'tanggal_lahir', 'gender', 'nationality']);
        });
    }
};
