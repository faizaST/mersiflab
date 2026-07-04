<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    protected $fillable = ['sesi_pelatihan_id', 'user_id', 'hadir', 'status_tugas', 'catatan_mentor'];

    protected $casts = ['hadir' => 'boolean'];

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(SesiPelatihan::class, 'sesi_pelatihan_id');
    }

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
