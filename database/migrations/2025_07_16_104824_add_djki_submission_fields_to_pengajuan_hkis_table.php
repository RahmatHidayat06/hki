<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->string('nomor_submisi_djki')->nullable()->after('status');
            $table->date('tanggal_submisi_djki')->nullable()->after('nomor_submisi_djki');
        });
        
        // Update existing invalid status values first
        DB::table('pengajuan_hkis')
            ->whereNotIn('status', [
                'draft', 'disubmit', 'sedang_diproses', 'menunggu_tanda_tangan',
                'menunggu_persetujuan_direktur', 'disetujui_direktur', 'selesai', 'ditolak'
            ])
            ->update(['status' => 'sedang_diproses']);
            
        // Now safely update the enum
        DB::statement("ALTER TABLE pengajuan_hkis MODIFY COLUMN status ENUM(
            'draft', 'disubmit', 'sedang_diproses', 'menunggu_tanda_tangan',
            'menunggu_persetujuan_direktur', 'disetujui_direktur', 
            'siap_serah_djki', 'diserahkan_djki', 'selesai', 'ditolak'
        ) NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_hkis', function (Blueprint $table) {
            $table->dropColumn(['nomor_submisi_djki', 'tanggal_submisi_djki']);
        });
        
        // Revert status enum to original
        DB::statement("ALTER TABLE pengajuan_hkis MODIFY COLUMN status ENUM(
            'draft', 'disubmit', 'sedang_diproses', 'menunggu_tanda_tangan',
            'menunggu_persetujuan_direktur', 'disetujui_direktur', 'selesai', 'ditolak'
        ) NOT NULL DEFAULT 'draft'");
    }
};
