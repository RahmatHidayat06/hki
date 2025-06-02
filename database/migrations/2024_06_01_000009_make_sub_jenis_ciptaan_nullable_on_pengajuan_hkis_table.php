<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->string('sub_jenis_ciptaan')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->string('sub_jenis_ciptaan')->nullable(false)->change();
        });
    }
}; 