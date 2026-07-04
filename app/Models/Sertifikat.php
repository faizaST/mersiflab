<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertifikat extends Model
{
    protected $fillable = ['pendaftaran_id', 'nomor_sertifikat', 'file_path', 'diterbitkan_at'];

    protected $casts = ['diterbitkan_at' => 'datetime'];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    /* cek peserta lulus berdasarkan presensi */
    public static function cekLulus(int $userId, int $pelatihanId): bool
    {
        $totalSesi = SesiPelatihan::where('pelatihan_id', $pelatihanId)->count();
        if ($totalSesi === 0) return false;

        $hadir   = Presensi::where('user_id', $userId)
            ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $pelatihanId))
            ->where('hadir', true)->count();
        $invalid = Presensi::where('user_id', $userId)
            ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $pelatihanId))
            ->where('status_tugas', 'tidak_valid')->count();

        $persen = round(($hadir / $totalSesi) * 100);
        return $persen >= 80 && $invalid === 0;
    }

    /*Generate nomor sertifikat unik */
    public static function generateNomor(): string
    {
        $now   = now();
        $tahun = $now->year;
        $bulan = $now->month;
        $count = static::whereYear('created_at', $tahun)->count() + 1;
        
        return 'CERT-' . str_pad($count, 4, '0', STR_PAD_LEFT) . '/' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '/' . $tahun;
    }
}
