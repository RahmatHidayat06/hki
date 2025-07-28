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
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->string('kota_pertama_kali_diumumkan')->nullable()->after('tanggal_pertama_kali_diumumkan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->dropColumn('kota_pertama_kali_diumumkan');
        });
    }
}; 