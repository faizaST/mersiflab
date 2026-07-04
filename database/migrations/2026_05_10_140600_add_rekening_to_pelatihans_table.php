<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->string('nama_bank')->nullable()->after('biaya');       // e.g. BCA
            $table->string('nomor_rekening')->nullable()->after('nama_bank'); // e.g. 1234567890
            $table->string('atas_nama')->nullable()->after('nomor_rekening'); // e.g. MersifLab
        });
    }

    public function down(): void
    {
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->dropColumn(['nama_bank', 'nomor_rekening', 'atas_nama']);
        });
    }
};
