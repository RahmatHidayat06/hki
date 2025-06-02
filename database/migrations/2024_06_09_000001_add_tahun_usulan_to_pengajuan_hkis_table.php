<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->string('tahun_usulan')->nullable()->after('tanggal_pertama_kali_diumumkan');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->dropColumn('tahun_usulan');
        });
    }
}; 