<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Pelatihan;
use App\Models\Presensi;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $pelatihans = Pelatihan::latest()->get();
        $selectedId = $request->pelatihan_id;

        $query = Pendaftaran::with(['peserta', 'pelatihan'])
            ->where('status', 'terverifikasi');

        if ($selectedId) {
            $query->where('pelatihan_id', $selectedId);
        }

        if ($request->filled('search')) {
            $query->whereHas('peserta', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }
 
        $pendaftarans = $query->latest()->get();

        $monitoring = $pendaftarans->map(function ($daftar) {
            $totalSesi       = $daftar->pelatihan->sesi()->count();
            $hadir           = Presensi::where('user_id', $daftar->user_id)
                ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $daftar->pelatihan_id))
                ->where('hadir', true)->count();
            $tugasValid      = Presensi::where('user_id', $daftar->user_id)
                ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $daftar->pelatihan_id))
                ->where('status_tugas', 'valid')->count();
            $tugasTidakValid = Presensi::where('user_id', $daftar->user_id)
                ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $daftar->pelatihan_id))
                ->where('status_tugas', 'tidak_valid')->count();

            $persen = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100) : 0;

            return [
                'peserta'           => $daftar->peserta,
                'pelatihan'         => $daftar->pelatihan,
                'total_sesi'        => $totalSesi,
                'hadir'             => $hadir,
                'persen_hadir'      => $persen,
                'tugas_valid'       => $tugasValid,
                'tugas_tidak_valid' => $tugasTidakValid,
                'memenuhi_syarat'   => $persen >= 80,
                'lulus'             => $persen >= 80 && $tugasValid >= 1 && $totalSesi > 0,
            ];
        });

        return view('admin.monitoring.index', compact('monitoring', 'pelatihans', 'selectedId'));
    }
}
