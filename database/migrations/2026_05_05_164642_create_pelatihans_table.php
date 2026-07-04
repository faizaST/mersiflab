<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelatihans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('kategori')->nullable();
            $table->unsignedInteger('kuota')->default(0);
            $table->boolean('berbayar')->default(false);
            $table->unsignedBigInteger('biaya')->nullable();
            $table->string('lokasi_nama')->nullable();
            $table->string('lokasi_alamat')->nullable();
            $table->decimal('lat', 11, 8)->nullable();
            $table->decimal('lng', 12, 8)->nullable();
            $table->enum('status', ['aktif', 'draft', 'selesai'])->default('aktif');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // mentor
            $table->timestamps();
        });

        Schema::create('sesi_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelatihan_id')->constrained('pelatihans')->cascadeOnDelete();
            $table->string('topik')->nullable();
            $table->date('tanggal')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_pelatihans');
        Schema::dropIfExists('pelatihans');
    }
};
