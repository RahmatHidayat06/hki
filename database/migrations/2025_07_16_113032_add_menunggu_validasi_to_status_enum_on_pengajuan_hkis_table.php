<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pengajuan_hkis MODIFY COLUMN status ENUM(
            'draft', 'menunggu_validasi', 'disubmit', 'sedang_diproses',
            'menunggu_tanda_tangan', 'menunggu_persetujuan_direktur',
            'disetujui_direktur', 'siap_serah_djki', 'diserahkan_djki',
            'selesai', 'ditolak'
        ) NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pengajuan_hkis MODIFY COLUMN status ENUM(
            'draft', 'disubmit', 'sedang_diproses', 'menunggu_tanda_tangan',
            'menunggu_persetujuan_direktur', 'disetujui_direktur',
            'siap_serah_djki', 'diserahkan_djki', 'selesai', 'ditolak'
        ) NOT NULL DEFAULT 'draft'");
    }
};
