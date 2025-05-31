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
            $table->string('nama_pengusul')->after('nomor_pengajuan');
            $table->string('nip_nidn')->nullable()->after('nama_pengusul');
            $table->string('no_hp')->after('nip_nidn');
            $table->string('id_sinta')->nullable()->after('no_hp');
            $table->string('jumlah_pencipta')->after('id_sinta');
            $table->string('identitas_ciptaan')->after('jumlah_pencipta');
            $table->string('sub_jenis_ciptaan')->after('identitas_ciptaan');
            $table->date('tanggal_pertama_kali_diumumkan')->nullable()->after('sub_jenis_ciptaan');
            $table->string('role')->after('tanggal_pertama_kali_diumumkan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->dropColumn([
                'nama_pengusul',
                'nip_nidn',
                'no_hp',
                'id_sinta',
                'jumlah_pencipta',
                'identitas_ciptaan',
                'sub_jenis_ciptaan',
                'tanggal_pertama_kali_diumumkan',
                'role'
            ]);
        });
    }
};
