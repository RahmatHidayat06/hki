<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->dropColumn('tahun_usulan');
        });
    }

    public function down()
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->string('tahun_usulan')->nullable()->after('tanggal_pengajuan');
        });
    }
};