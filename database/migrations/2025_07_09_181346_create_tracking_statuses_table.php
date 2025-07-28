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
        Schema::create('tracking_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_hki_id')->constrained()->onDelete('cascade');
            $table->string('status'); // status yang dicapai
            $table->string('title'); // judul untuk timeline
            $table->text('description')->nullable(); // deskripsi detail
            $table->string('icon')->default('fas fa-circle'); // icon untuk timeline
            $table->string('color')->default('secondary'); // warna untuk timeline
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // user yang melakukan aksi
            $table->text('notes')->nullable(); // catatan tambahan
            $table->timestamps();
            
            $table->index(['pengajuan_hki_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_statuses');
    }
};
