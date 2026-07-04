<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel pivot many-to-many: satu pelatihan bisa punya banyak mentor
        Schema::create('mentor_pelatihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('pelatihan_id')
                  ->constrained('pelatihans')
                  ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'pelatihan_id']);
        });

        // Migrasi data lama dari user_id di pelatihans ke tabel pivot
        $pelatihans = \DB::table('pelatihans')->whereNotNull('user_id')->get();
        foreach ($pelatihans as $p) {
            \DB::table('mentor_pelatihan')->insertOrIgnore([
                'user_id'      => $p->user_id,
                'pelatihan_id' => $p->id,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // Hapus kolom user_id lama dari pelatihans
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        });
        Schema::dropIfExists('mentor_pelatihan');
    }
};
