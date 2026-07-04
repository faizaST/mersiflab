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
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->string('sertifikat_template')->nullable()->default(null)->change();
            $table->string('sertifikat_logo_kiri')->nullable()->default(null)->change();
            $table->string('sertifikat_logo_kanan')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelatihans', function (Blueprint $table) {
            // No reverse action needed as we just keep it nullable
        });
    }
};
