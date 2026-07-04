<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();       // peserta
            $table->foreignId('pelatihan_id')->constrained('pelatihans')->cascadeOnDelete();
            $table->enum('status', ['pending', 'terverifikasi', 'ditolak'])->default('pending');
            $table->string('bukti_bayar')->nullable();   // path file
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'pelatihan_id']); // 1 peserta 1x daftar per pelatihan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};
