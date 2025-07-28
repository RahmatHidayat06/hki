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
        Schema::create('pengaju_hkis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_hki_id')->constrained('pengajuan_hkis')->onDelete('cascade');
            $table->string('nama');
            $table->string('nip_nidn')->nullable();
            $table->string('kewarganegaraan')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('email');
            $table->text('alamat')->nullable();
            $table->string('kodepos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaju_hkis');
    }
};
