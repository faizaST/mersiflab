<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\SesiPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Pelatihan via pivot (menggunakan relasi yang sudah didefinisikan di User model)
        $pelatihans = $user->pelatihans()->with('sesi')->latest()->get();

        $pesertaPerPelatihan = $pelatihans->mapWithKeys(function ($p) {
            $peserta = Pendaftaran::with('peserta')
                ->where('pelatihan_id', $p->id)
                ->where('status', 'terverifikasi')
                ->get()
                ->map(fn($d) => ['id' => $d->user_id, 'nama' => $d->peserta->name])
                ->values()
                ->toArray();
            return [$p->id => $peserta];
        });

        $sesiPerPelatihan = $pelatihans->mapWithKeys(fn($p) => [
            $p->id => $p->sesi->sortByDesc('tanggal')->map(fn($s) => [
                'id'            => $s->id,
                'topik'         => $s->topik,
                'tanggal'       => $s->tanggal,
                'waktu_mulai'   => $s->waktu_mulai,
                'waktu_selesai' => $s->waktu_selesai,
            ])->values()->toArray()
        ]);

        $allSesiIds = $pelatihans->flatMap(fn($p) => $p->sesi->pluck('id'));

        $presensiTersimpan = Presensi::whereIn('sesi_pelatihan_id', $allSesiIds)
            ->get()
            ->groupBy('sesi_pelatihan_id')
            ->map(fn($rows) => $rows->keyBy('user_id')
                ->map(fn($r) => [
                    'hadir'        => (bool) $r->hadir,
                    'status_tugas' => $r->status_tugas,
                    'catatan'      => $r->catatan_mentor ?? '',
                ])
            );

        return view('mentor.presensi.index', compact(
            'pelatihans', 'pesertaPerPelatihan', 'sesiPerPelatihan', 'presensiTersimpan'
        ));
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'sesi_id'  => 'required|exists:sesi_pelatihans,id',
            'presensi' => 'required|array',
        ]);

        $sesi = SesiPelatihan::findOrFail($request->sesi_id);

        if ($sesi->tanggal !== date('Y-m-d')) {
            return back()->with('error', 'Presensi hanya dapat dilakukan pada tanggal jadwal yang ditentukan (' . $sesi->tanggal . ').');
        }

        foreach ($request->presensi as $userId => $data) {
            Presensi::updateOrCreate(
                ['sesi_pelatihan_id' => $request->sesi_id, 'user_id' => $userId],
                [
                    'hadir'          => filter_var($data['hadir'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'status_tugas'   => $data['status_tugas'] ?? 'belum',
                    'catatan_mentor' => $data['catatan'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Presensi berhasil disimpan.');
    }
}
