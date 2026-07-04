<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Presensi per sesi per peserta
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_pelatihan_id')->constrained('sesi_pelatihans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();       // peserta
            $table->boolean('hadir')->default(false);
            $table->enum('status_tugas', ['belum', 'valid', 'tidak_valid'])->default('belum');
            $table->text('catatan_mentor')->nullable();
            $table->timestamps();

            $table->unique(['sesi_pelatihan_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
