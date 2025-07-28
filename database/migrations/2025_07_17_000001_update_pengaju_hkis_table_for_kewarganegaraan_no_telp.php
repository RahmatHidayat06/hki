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
        Schema::table('pengaju_hkis', function (Blueprint $table) {
            if (Schema::hasColumn('pengaju_hkis', 'kecamatan')) {
                $table->dropColumn('kecamatan');
            }
            if (Schema::hasColumn('pengaju_hkis', 'no_hp')) {
                $table->dropColumn('no_hp');
            }
            if (!Schema::hasColumn('pengaju_hkis', 'kewarganegaraan')) {
                $table->string('kewarganegaraan')->nullable()->after('nip_nidn');
            }
            if (!Schema::hasColumn('pengaju_hkis', 'no_telp')) {
                $table->string('no_telp')->nullable()->after('kewarganegaraan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaju_hkis', function (Blueprint $table) {
            if (Schema::hasColumn('pengaju_hkis', 'kewarganegaraan')) {
                $table->dropColumn('kewarganegaraan');
            }
            if (Schema::hasColumn('pengaju_hkis', 'no_telp')) {
                $table->dropColumn('no_telp');
            }
            $table->string('kecamatan')->nullable()->after('alamat');
            $table->string('no_hp')->nullable()->after('nip_nidn');
        });
    }
}; 