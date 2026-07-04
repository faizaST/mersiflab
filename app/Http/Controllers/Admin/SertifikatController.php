<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Pelatihan;
use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\SesiPelatihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SertifikatController extends Controller
{
    public function index(Request $request)
    {
        $pelatihans = Pelatihan::latest()->get();
        $selectedId = $request->pelatihan_id;

        $query = Pendaftaran::with(['peserta', 'pelatihan', 'sertifikat'])
            ->where('status', 'terverifikasi');

        if ($selectedId) {
            $query->where('pelatihan_id', $selectedId);
        }

        $pendaftarans = $query->latest()->get();

        $data = $pendaftarans->map(function ($daftar) {
            $totalSesi = SesiPelatihan::where('pelatihan_id', $daftar->pelatihan_id)->count();
            $hadir     = Presensi::where('user_id', $daftar->user_id)
                ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $daftar->pelatihan_id))
                ->where('hadir', true)->count();
            $valid     = Presensi::where('user_id', $daftar->user_id)
                ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $daftar->pelatihan_id))
                ->where('status_tugas', 'valid')->count();

            $persen = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100) : 0;
            $lulus  = $persen >= 80 && $valid >= 1 && $totalSesi > 0;

            return [
                'daftar'     => $daftar,
                'peserta'    => $daftar->peserta,
                'pelatihan'  => $daftar->pelatihan,
                'persen'     => $persen,
                'hadir'      => $hadir,
                'total_sesi' => $totalSesi,
                'lulus'      => $lulus,
                'sertifikat' => $daftar->sertifikat,
            ];
        });

        $stats = [
            'lulus'       => $data->where('lulus', true)->count(),
            'diterbitkan' => $data->filter(fn($d) => $d['sertifikat'])->count(),
            'menunggu'    => $data->filter(fn($d) => $d['lulus'] && !$d['sertifikat'])->count(),
        ];

        return view('admin.sertifikat.index', compact('data', 'stats', 'pelatihans', 'selectedId'));
    }

    /*Generate sertifikat PDF*/
    public function generate(int $pendaftaranId)
    {
        $daftar = Pendaftaran::with(['peserta', 'pelatihan.mentors', 'pelatihan.sesi'])->findOrFail($pendaftaranId);

        // Cek lulus
        $totalSesi = SesiPelatihan::where('pelatihan_id', $daftar->pelatihan_id)->count();
        $hadir     = Presensi::where('user_id', $daftar->user_id)
            ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $daftar->pelatihan_id))
            ->where('hadir', true)->count();
        $invalid   = Presensi::where('user_id', $daftar->user_id)
            ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $daftar->pelatihan_id))
            ->where('status_tugas', 'tidak_valid')->count();
        $valid     = Presensi::where('user_id', $daftar->user_id)
            ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $daftar->pelatihan_id))
            ->where('status_tugas', 'valid')->count();
        $persen    = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100) : 0;
        $lulus     = $persen >= 80 && $valid >= 1 && $totalSesi > 0;

        if (!$lulus) {
            return back()->with('error', 'Peserta belum memenuhi syarat kelulusan.');
        }

        // Buat atau ambil record sertifikat
        $sertifikat = Sertifikat::firstOrCreate(
            ['pendaftaran_id' => $daftar->id],
            [
                'nomor_sertifikat' => Sertifikat::generateNomor(),
                'diterbitkan_at'   => now(),
            ]
        );

        // Hitung rentang tanggal pelaksanaan pelatihan
        $sesiTerurut = $daftar->pelatihan->sesi->whereNotNull('tanggal')->sortBy('tanggal');
        $tglMulai    = $sesiTerurut->first()?->tanggal;
        $tglSelesai  = $sesiTerurut->last()?->tanggal;
        
        $periode = now()->translatedFormat('d F Y'); // Default fallback
        if ($tglMulai) {
            $tglMulaiCarbon = \Carbon\Carbon::parse($tglMulai);
            $tglSelesaiCarbon = $tglSelesai ? \Carbon\Carbon::parse($tglSelesai) : $tglMulaiCarbon;

            if ($tglMulaiCarbon->isSameDay($tglSelesaiCarbon)) {
                $periode = $tglMulaiCarbon->translatedFormat('d F Y');
            } elseif ($tglMulaiCarbon->isSameMonth($tglSelesaiCarbon)) {
                $periode = $tglMulaiCarbon->format('d') . ' - ' . $tglSelesaiCarbon->translatedFormat('d F Y');
            } elseif ($tglMulaiCarbon->isSameYear($tglSelesaiCarbon)) {
                $periode = $tglMulaiCarbon->translatedFormat('d F') . ' - ' . $tglSelesaiCarbon->translatedFormat('d F Y');
            } else {
                $periode = $tglMulaiCarbon->translatedFormat('d F Y') . ' - ' . $tglSelesaiCarbon->translatedFormat('d F Y');
            }
        }

        // Generate PDF
        $pdf = Pdf::loadView('pdf.sertifikat', [
            'peserta'    => $daftar->peserta,
            'pelatihan'  => $daftar->pelatihan,
            'sertifikat' => $sertifikat,
            'persen'     => $persen,
            'tanggal'    => $periode,
        ])->setPaper('a4', 'landscape');

        $filename = 'sertifikat/' . $sertifikat->nomor_sertifikat . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        $sertifikat->update(['file_path' => $filename]);

        return back()->with('success', 'Sertifikat berhasil digenerate untuk ' . $daftar->peserta->name);
    }

    /*Download sertifikat oleh admin*/
    public function download(int $sertifikatId)
    {
        $sertifikat = Sertifikat::with('pendaftaran.peserta')->findOrFail($sertifikatId);
        abort_if(!$sertifikat->file_path || !Storage::disk('public')->exists($sertifikat->file_path), 404);

        return response()->download(
            Storage::disk('public')->path($sertifikat->file_path),
            'Sertifikat-' . str_replace(['/', '\\'], '-', $sertifikat->nomor_sertifikat) . '.pdf'
        );
    }


    /** Download laporan peserta per pelatihan (PDF) */
    public function laporanPdf(Request $request)
    {
        $pelatihanId = $request->pelatihan_id;
        abort_if(!$pelatihanId, 400, 'Pilih pelatihan terlebih dahulu.');
        $pelatihan = Pelatihan::with('mentors')->findOrFail($pelatihanId);

        $pendaftarans = Pendaftaran::with(['peserta', 'sertifikat'])
            ->where('pelatihan_id', $pelatihanId)
            ->where('status', 'terverifikasi')
            ->get();

        $rows = $pendaftarans->map(function ($daftar, $i) use ($pelatihanId) {
            $totalSesi = SesiPelatihan::where('pelatihan_id', $pelatihanId)->count();
            $presensis = Presensi::where('user_id', $daftar->user_id)
                ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $pelatihanId))
                ->get();
            
            $hadir   = $presensis->where('hadir', true)->count();
            $valid   = $presensis->where('status_tugas', 'valid')->count();
            $invalid = $presensis->where('status_tugas', 'tidak_valid')->count();
            $belum   = $presensis->where('status_tugas', 'belum')->count();
            
            $persen    = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100) : 0;
            $lulus     = $persen >= 80 && $valid >= 1 && $totalSesi > 0;

            return [
                'no'          => $i + 1,
                'nama'        => $daftar->peserta->name,
                'hadir'       => $hadir,
                'total_sesi'  => $totalSesi,
                'persen'      => $persen,
                'tugas_valid' => $valid,
                'tugas_invalid' => $invalid,
                'tugas_belum' => $belum,
                'lulus'       => $lulus,
                'no_sert'     => $daftar->sertifikat?->nomor_sertifikat ?? '-',
                'tgl_terbit'  => $daftar->sertifikat?->diterbitkan_at?->format('d/m/Y') ?? '-',
            ];
        });

        // Hitung rentang tanggal pelatihan
        $sesiTerurut = $pelatihan->sesi->whereNotNull('tanggal')->sortBy('tanggal');
        $tglMulai    = $sesiTerurut->first()?->tanggal;
        $tglSelesai  = $sesiTerurut->last()?->tanggal;
        
        $periode = '—';
        if ($tglMulai) {
            $periode = \Carbon\Carbon::parse($tglMulai)->translatedFormat('d M Y');
            if ($tglSelesai && $tglSelesai !== $tglMulai) {
                $periode .= ' s/d ' . \Carbon\Carbon::parse($tglSelesai)->translatedFormat('d M Y');
            }
        }

        $pdf = Pdf::loadView('pdf.laporan', [
            'pelatihan'  => $pelatihan,
            'rows'       => $rows,
            'periode'    => $periode,
            'tanggal'    => now()->translatedFormat('d F Y'),
            'admin_name' => Auth::user()->name ?? 'Administrator',
        ])->setPaper('a4', 'portrait');

        $filename = 'Laporan-' . str_replace([' ', '/', '\\'], '_', $pelatihan->nama) . '-' . now()->format('Ymd') . '.pdf';
        if ($request->has('download')) {
            return $pdf->download($filename);
        }
        
        return $pdf->stream($filename);
    }
}
