<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing records with 'divalidasi' status to 'divalidasi_sedang_diproses'
        DB::table('pengajuan_hkis')
            ->where('status', 'divalidasi')
            ->update(['status' => 'divalidasi_sedang_diproses']);
            
        // Update existing records with 'sedang_di_proses' status to 'divalidasi_sedang_diproses'
        DB::table('pengajuan_hkis')
            ->where('status', 'sedang_di_proses')
            ->update(['status' => 'divalidasi_sedang_diproses']);

        // Note: Laravel migrations handle string values, no need to modify enum constraints
    }

    public function down(): void
    {
        // Rollback: Convert 'divalidasi_sedang_diproses' back to 'divalidasi'
        // Note: This will lose the distinction between original 'divalidasi' and 'sedang_di_proses' records
        DB::table('pengajuan_hkis')
            ->where('status', 'divalidasi_sedang_diproses')
            ->update(['status' => 'divalidasi']);
    }
}; 