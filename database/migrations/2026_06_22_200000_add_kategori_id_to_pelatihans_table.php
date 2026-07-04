<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\KategoriPelatihan;

return new class extends Migration {
    public function up()
    {
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_id')->nullable()->after('gambar');
            $table->foreign('kategori_id')
                ->references('id')
                ->on('kategori_pelatihans')
                ->onDelete('set null');
        });

        // Migrate existing data from kategori string to kategori_id
        $pelatihans = \DB::table('pelatihans')->select('id', 'kategori')->get();
        foreach ($pelatihans as $pelatihan) {
            $kategori = KategoriPelatihan::where('nama', $pelatihan->kategori)->first();
            if ($kategori) {
                \DB::table('pelatihans')
                    ->where('id', $pelatihan->id)
                    ->update(['kategori_id' => $kategori->id]);
            }
        }

        // Drop old column
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }

    public function down()
    {
        // Re-add kategori string column
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->string('kategori')->nullable();
        });

        // Migrate back from kategori_id to kategori name
        $pelatihans = \DB::table('pelatihans')->select('id', 'kategori_id')->get();
        foreach ($pelatihans as $pelatihan) {
            $kategori = KategoriPelatihan::find($pelatihan->kategori_id);
            if ($kategori) {
                \DB::table('pelatihans')
                    ->where('id', $pelatihan->id)
                    ->update(['kategori' => $kategori->nama]);
            }
        }

        // Drop foreign key and kategori_id column
        Schema::table('pelatihans', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropColumn('kategori_id');
        });
    }
};
?>
