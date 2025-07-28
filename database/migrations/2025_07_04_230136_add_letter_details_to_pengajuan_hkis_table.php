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
            $table->date('tanggal_surat')->nullable()->after('tahun_usulan');
            $table->json('alamat_pencipta')->nullable()->after('tanggal_surat');
            $table->json('signature_pencipta')->nullable()->after('alamat_pencipta');
            $table->boolean('gunakan_materai')->default(false)->after('signature_pencipta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->dropColumn(['tanggal_surat', 'alamat_pencipta', 'signature_pencipta', 'gunakan_materai']);
        });
    }
};
