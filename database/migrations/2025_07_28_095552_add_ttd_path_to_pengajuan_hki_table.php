<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

public function up()
{
    Schema::table('pengajuan_hkis', function (Blueprint $table) {
        $table->string('ttd_path')->nullable()->after('file_dokumen_pendukung');
    });
}

public function down()
{
    Schema::table('pengajuan_hkis', function (Blueprint $table) {
        $table->dropColumn('ttd_path');
    });
}
};
