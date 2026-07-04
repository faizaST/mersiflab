<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float|null $lat
 * @property float|null $lng
 */

class Pelatihan extends Model
{
    protected $fillable = [
        'nama',
        'deskripsi',
        'gambar',
        'kategori_id',
        'kuota',
        'berbayar',
        'biaya',
        'nama_bank',
        'nomor_rekening',
        'atas_nama',
        'lokasi_nama',
        'lokasi_alamat',
        'lat',
        'lng',
        'status',
        'catatan',
        'tenggat_pendaftaran',
        'sertifikat_template',
        'sertifikat_logo_kiri',
        'sertifikat_logo_kanan',
    ];

    protected $casts = [
        'berbayar' => 'boolean',
        'biaya'    => 'integer',
        'kuota'    => 'integer',
        'lat'      => 'float',
        'lng'      => 'float',
        'tenggat_pendaftaran' => 'datetime',
    ];

    /*mentor */
    public function mentors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'mentor_pelatihan')
                    ->withTimestamps();
    }

    /* kategori */
    public function kategoriPelatihan(): BelongsTo
    {
        return $this->belongsTo(KategoriPelatihan::class, 'kategori_id');
    }

    /*tampilan singkat mentor pertama*/
    public function getMentorAttribute(): ?User
    {
        return $this->mentors->first();
    }

    public function sesi(): HasMany
    {
        return $this->hasMany(SesiPelatihan::class);
    }

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function getPendaftarCountAttribute(): int
    {
        return $this->pendaftarans()->where('status', '!=', 'ditolak')->count();
    }

    public function isFull(): bool
    {
        return $this->pendaftar_count >= $this->kuota;
    }

    public function isDeadlinePassed(): bool
    {
        if (!$this->tenggat_pendaftaran) {
            return false;
        }
        return now()->isAfter($this->tenggat_pendaftaran);
    }

    public function getBiayaFormattedAttribute(): string
    {
        return $this->berbayar
            ? 'Rp ' . number_format($this->biaya, 0, ',', '.')
            : 'Gratis';
    }
}
