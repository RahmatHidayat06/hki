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
        // Rename no_hp ke no_telp di tabel users
        if (Schema::hasColumn('users', 'no_hp')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('no_telp')->nullable()->after('email');
                $table->dropColumn('no_hp');
            });
        }
        // Rename no_hp ke no_telp di tabel pengajuan_hkis
        if (Schema::hasColumn('pengajuan_hkis', 'no_hp')) {
            Schema::table('pengajuan_hkis', function (Blueprint $table) {
                $table->string('no_telp')->nullable()->after('nip_nidn');
                $table->dropColumn('no_hp');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan no_telp ke no_hp di tabel users
        if (Schema::hasColumn('users', 'no_telp')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('no_hp')->nullable()->after('email');
                $table->dropColumn('no_telp');
            });
        }
        // Kembalikan no_telp ke no_hp di tabel pengajuan_hkis
        if (Schema::hasColumn('pengajuan_hkis', 'no_telp')) {
            Schema::table('pengajuan_hkis', function (Blueprint $table) {
                $table->string('no_hp')->nullable()->after('nip_nidn');
                $table->dropColumn('no_telp');
            });
        }
    }
}; 