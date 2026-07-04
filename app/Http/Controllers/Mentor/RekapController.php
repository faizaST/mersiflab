<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user       = Auth::user();
        $pelatihans = $user->pelatihans()->latest()->get();
        $selectedId = $request->pelatihan_id ?? $pelatihans->first()?->id;
        $pelatihan  = $pelatihans->find($selectedId);

        $rekap = collect();
        if ($pelatihan) {
            $totalSesi    = $pelatihan->sesi()->count();
            $pendaftarans = Pendaftaran::with('peserta')
                ->where('pelatihan_id', $pelatihan->id)
                ->where('status', 'terverifikasi')
                ->latest()->get();

            $rekap = $pendaftarans->map(function ($daftar) use ($pelatihan, $totalSesi) {
                $hadir   = Presensi::where('user_id', $daftar->user_id)
                    ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $pelatihan->id))
                    ->where('hadir', true)->count();
                $valid   = Presensi::where('user_id', $daftar->user_id)
                    ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $pelatihan->id))
                    ->where('status_tugas', 'valid')->count();
                $invalid = Presensi::where('user_id', $daftar->user_id)
                    ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $pelatihan->id))
                    ->where('status_tugas', 'tidak_valid')->count();
                $persen  = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100) : 0;

                return [
                    'peserta' => $daftar->peserta,
                    'hadir'   => $hadir,
                    'persen'  => $persen,
                    'valid'   => $valid,
                    'invalid' => $invalid,
                    'lulus'   => $persen >= 80 && $valid >= 1 && $totalSesi > 0,
                ];
            });
        }

        return view('mentor.rekap.index', compact('pelatihans', 'pelatihan', 'rekap', 'selectedId'));
    }
}
