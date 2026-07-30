<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\Sertifikat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RiwayatController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $pendaftarans = Pendaftaran::with(['pelatihan.mentors', 'pelatihan.sesi', 'pelatihan.kategoriPelatihan', 'sertifikat'])
            ->where('user_id', $user->id)
            ->latest()->get();

        //Hitung statistik per pendaftaran
        $riwayat = $pendaftarans->map(function ($daftar) use ($user) {
            $totalSesi = $daftar->pelatihan->sesi->count();
            $hadir     = Presensi::where('user_id', $user->id)
                ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $daftar->pelatihan_id))
                ->where('hadir', true)->count();
            $valid     = Presensi::where('user_id', $user->id)
                ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $daftar->pelatihan_id))
                ->where('status_tugas', 'valid')->count();

            $persen = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100) : 0;
            $lulus  = $persen >= 80 && $valid >= 1 && $totalSesi > 0;

            return [
                'daftar'     => $daftar,
                'pelatihan'  => $daftar->pelatihan,
                'persen'     => $persen,
                'hadir'      => $hadir,
                'total_sesi' => $totalSesi,
                'lulus'      => $lulus,
                'sertifikat' => $daftar->sertifikat,
            ];
        });

        $stats = [
            'total'      => $riwayat->count(),
            'lulus'      => $riwayat->where('lulus', true)->count(),
            'sertifikat' => $riwayat->filter(fn($r) => $r['sertifikat'])->count(),
        ];

        return view('peserta.riwayat.index', compact('riwayat', 'stats'));
    }

    public function downloadSertifikat(int $sertifikatId): BinaryFileResponse
    {
        $sertifikat = Sertifikat::with('pendaftaran.peserta')
            ->findOrFail($sertifikatId);

        //Pastikan hanya pemilik yang bisa download
        abort_if($sertifikat->pendaftaran->user_id !== Auth::id(), 403);
        abort_if(!$sertifikat->file_path || !Storage::disk('public')->exists($sertifikat->file_path), 404, 'File sertifikat tidak ditemukan.');

        return response()->download(
            Storage::disk('public')->path($sertifikat->file_path),
            'Sertifikat-' . str_replace(['/', '\\'], '-', $sertifikat->nomor_sertifikat) . '.pdf'
        );
    }
}
