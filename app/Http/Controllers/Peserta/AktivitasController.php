<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\SesiPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AktivitasController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Hanya pendaftaran yang sudah terverifikasi yang muncul di aktivitas
        $pendaftarans = Pendaftaran::with(['pelatihan.sesi', 'pelatihan.mentors'])
            ->where('user_id', $user->id)
            ->where('status', 'terverifikasi')
            ->latest()->get();

        $selectedPelatihanId = $request->pelatihan_id ?? $pendaftarans->first()?->pelatihan_id;
        $selectedDaftar      = $pendaftarans->firstWhere('pelatihan_id', $selectedPelatihanId);

        // Data presensi untuk pelatihan terpilih
        $presensiData = collect();
        $persenHadir  = 0;
        $totalSesi    = 0;
        $hadirCount   = 0;

        if ($selectedDaftar) {
            $sesiList = SesiPelatihan::where('pelatihan_id', $selectedPelatihanId)
                ->orderBy('tanggal', 'desc')->orderBy('waktu_mulai', 'desc')->get();
            $totalSesi = $sesiList->count();

            $presensiData = $sesiList->map(function ($sesi, $i) use ($user) {
                $presensi = Presensi::where('sesi_pelatihan_id', $sesi->id)
                    ->where('user_id', $user->id)->first();
                return [
                    'no'           => $i + 1,
                    'sesi'         => $sesi,
                    'hadir'        => $presensi?->hadir ?? false,
                    'status_tugas' => $presensi?->status_tugas ?? 'belum',
                    'catatan'      => $presensi?->catatan_mentor,
                ];
            });

            $hadirCount  = $presensiData->where('hadir', true)->count();
            $persenHadir = $totalSesi > 0 ? round(($hadirCount / $totalSesi) * 100) : 0;
        }

        return view('peserta.aktivitas.index', compact(
            'pendaftarans', 'selectedDaftar', 'selectedPelatihanId',
            'presensiData', 'persenHadir', 'totalSesi', 'hadirCount'
        ));
    }

    /** Upload bukti pembayaran */
    public function uploadBukti(Request $request, int $pendaftaranId)
    {
        $request->validate([
            'bukti_bayar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $daftar = Pendaftaran::where('id', $pendaftaranId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Hapus file lama jika ada
        if ($daftar->bukti_bayar && Storage::disk('public')->exists($daftar->bukti_bayar)) {
            Storage::disk('public')->delete($daftar->bukti_bayar);
        }

        $path = $request->file('bukti_bayar')->store('bukti_bayar', 'public');

        $daftar->update([
            'bukti_bayar' => $path,
            'status'      => 'pending',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
    }
}
