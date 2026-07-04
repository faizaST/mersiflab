<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sesi_pelatihans', function (Blueprint $table) {
            $table->boolean('presensi_locked')->default(false)->after('waktu_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('sesi_pelatihans', function (Blueprint $table) {
            $table->dropColumn('presensi_locked');
        });
    }
};
