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
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_hki_id')->constrained()->onDelete('cascade');
            $table->integer('pencipta_ke'); // urutan pencipta (1, 2, 3, dst)
            $table->string('nama_pencipta'); // nama pencipta dari data pengajuan
            $table->string('email_pencipta')->nullable(); // email pencipta untuk notifikasi
            $table->string('nama_ttd'); // nama yang akan muncul di bawah tanda tangan
            $table->string('posisi')->default('kanan'); // posisi ttd (kanan/kiri)
            $table->string('signature_path')->nullable(); // path file tanda tangan
            $table->timestamp('signed_at')->nullable(); // kapan ditandatangani
            $table->foreignId('signed_by')->nullable()->constrained('users')->onDelete('set null'); // user yang tanda tangan
            $table->enum('status', ['pending', 'signed', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable(); // alasan jika ditolak
            $table->string('signature_token')->unique()->nullable(); // token untuk verifikasi tanda tangan
            $table->timestamps();
            
            $table->unique(['pengajuan_hki_id', 'pencipta_ke']);
            $table->index(['pengajuan_hki_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
