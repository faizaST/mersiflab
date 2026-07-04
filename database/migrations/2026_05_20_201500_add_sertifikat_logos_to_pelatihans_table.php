<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->string('sertifikat_logo_kiri')->nullable()->after('sertifikat_template');
            $table->string('sertifikat_logo_kanan')->nullable()->after('sertifikat_logo_kiri');
        });
    }

    public function down(): void
    {
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->dropColumn(['sertifikat_logo_kiri', 'sertifikat_logo_kanan']);
        });
    }
};
