<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategori_pelatihans', function (Blueprint $table) {
            $table->string('warna')->default('#6366f1')->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('kategori_pelatihans', function (Blueprint $table) {
            $table->dropColumn('warna');
        });
    }
};
