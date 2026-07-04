<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        // Seed with default categories
        $defaultKategoris = [
            'Web Development',
            'Mobile Development',
            'Data Science',
            'Networking',
            'UI/UX Design',
            'Cybersecurity',
            'Cloud Computing',
            'Game Development',
        ];

        foreach ($defaultKategoris as $kategori) {
            DB::table('kategori_pelatihans')->insert([
                'nama' => $kategori,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_pelatihans');
    }
};
