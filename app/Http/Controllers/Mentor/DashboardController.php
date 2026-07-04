<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
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

        // Pelatihan yang diampu mentor ini (via pivot)
        $pelatihans = $user->pelatihans()->with('sesi')->latest()->get();

        $sesiHariIni = SesiPelatihan::whereIn('pelatihan_id', $pelatihans->pluck('id'))
            ->where('tanggal', now()->toDateString())
            ->with('pelatihan')
            ->orderBy('waktu_mulai')
            ->get();

        $hadirHariIni = Presensi::whereIn('sesi_pelatihan_id', $sesiHariIni->pluck('id'))
            ->where('hadir', true)->count();

        return view('dashboard-mentor', compact('pelatihans', 'sesiHariIni', 'hadirHariIni'));
    }
}
