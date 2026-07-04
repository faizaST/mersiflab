<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->cascadeOnDelete();
            $table->string('nomor_sertifikat')->unique();   // e.g. CERT-2026-0001
            $table->string('file_path')->nullable();        // storage path PDF
            $table->timestamp('diterbitkan_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikats');
    }
};
