<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sesi_pelatihans', function (Blueprint $table) {
            if (Schema::hasColumn('sesi_pelatihans', 'presensi_locked')) {
                $table->dropColumn('presensi_locked');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sesi_pelatihans', function (Blueprint $table) {
            $table->boolean('presensi_locked')->default(false)->after('waktu_selesai');
        });
    }
};
