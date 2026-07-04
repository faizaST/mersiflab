<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SesiPelatihan extends Model
{
    protected $table = 'sesi_pelatihans';

    protected $fillable = [
        'pelatihan_id',
        'topik',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
    ];

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(Pelatihan::class);
    }
}
