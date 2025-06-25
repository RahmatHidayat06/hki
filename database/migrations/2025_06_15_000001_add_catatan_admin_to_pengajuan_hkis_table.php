<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            if (!Schema::hasColumn('pengajuan_hkis', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable()->after('status');
            }
            if (!Schema::hasColumn('pengajuan_hkis', 'tanggal_validasi')) {
                $table->timestamp('tanggal_validasi')->nullable()->after('catatan_admin');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            if (Schema::hasColumn('pengajuan_hkis', 'tanggal_validasi')) {
                $table->dropColumn('tanggal_validasi');
            }
            if (Schema::hasColumn('pengajuan_hkis', 'catatan_admin')) {
                $table->dropColumn('catatan_admin');
            }
        });
    }
}; 