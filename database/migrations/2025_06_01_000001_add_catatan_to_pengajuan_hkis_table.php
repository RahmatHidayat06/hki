<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->text('catatan_validasi')->nullable()->after('status');
            $table->text('catatan_persetujuan')->nullable()->after('catatan_validasi');
        });
    }

    public function down()
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->dropColumn(['catatan_validasi', 'catatan_persetujuan']);
        });
    }
}; 