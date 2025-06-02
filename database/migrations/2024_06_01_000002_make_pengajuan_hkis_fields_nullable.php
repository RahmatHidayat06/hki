<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->string('judul_karya')->nullable()->change();
            $table->string('kategori')->nullable()->change();
            $table->text('deskripsi')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->string('judul_karya')->nullable(false)->change();
            $table->string('kategori')->nullable(false)->change();
            $table->text('deskripsi')->nullable(false)->change();
        });
    }
}; 