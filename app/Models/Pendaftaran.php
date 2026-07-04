<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pendaftaran extends Model
{
    protected $fillable = ['user_id', 'pelatihan_id', 'status', 'bukti_bayar'];

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(Pelatihan::class);
    }

    public function sertifikat(): HasOne
    {
        return $this->hasOne(Sertifikat::class);
    }
}
