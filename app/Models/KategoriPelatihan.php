<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPelatihan extends Model
{
    protected $table = 'kategori_pelatihans';

    protected $fillable = [
        'nama',
        'warna',
    ];
}
