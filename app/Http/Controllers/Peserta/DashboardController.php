<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Pelatihan;
use App\Models\Presensi;
use App\Models\SesiPelatihan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $pendaftarans = Pendaftaran::with(['pelatihan.mentors', 'pelatihan.sesi'])
            ->where('user_id', $user->id)
            ->latest()->get();

        // Hitung progress kehadiran per pelatihan
        $pendaftarans = $pendaftarans->map(function ($daftar) use ($user) {
            $totalSesi = $daftar->pelatihan->sesi->count();
            $hadir     = Presensi::where('user_id', $user->id)
                ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $daftar->pelatihan_id))
                ->where('hadir', true)->count();
            $daftar->progress      = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100) : 0;
            $daftar->total_sesi    = $totalSesi;
            $daftar->hadir_count   = $hadir;
            return $daftar;
        });

        // Sesi berikutnya — hanya dari pelatihan yang masih aktif
        $pelatihanAktifIds = $pendaftarans
            ->filter(fn($d) => $d->pelatihan->status === 'aktif' && $d->status === 'terverifikasi')
            ->pluck('pelatihan_id');

        $sesiBerikutnya = $pelatihanAktifIds->isNotEmpty()
            ? SesiPelatihan::whereIn('pelatihan_id', $pelatihanAktifIds)
                ->where('tanggal', '>=', now()->toDateString())
                ->with('pelatihan.mentors')
                ->orderBy('tanggal')->orderBy('waktu_mulai')
                ->first()
            : null;

        // Lokasi pelatihan untuk peta
        $lokasiPeta = Pelatihan::whereIn('id', $pendaftarans->pluck('pelatihan_id'))
            ->whereNotNull('lat')->whereNotNull('lng')
            ->get(['id', 'nama', 'lat', 'lng', 'lokasi_nama']);

        return view('dashboard-peserta', compact('pendaftarans', 'sesiBerikutnya', 'lokasiPeta'));
    }
}
