<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Pelatihan;
use App\Models\Sertifikat;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $total_pelatihan  = Pelatihan::count();
        $total_mentor     = User::where('role', 'mentor')->count();
        $total_peserta    = User::where('role', 'peserta')->count();
        $pelatihan_aktif  = Pelatihan::where('status', 'aktif')->with('mentors')->latest()->take(5)->get();
        $total_sertifikat = Sertifikat::count();

        //Chart: pendaftar per bulan (6 bulan terakhir)
        $chart_data = collect(range(5, 0))->map(function (int $i): int {
            return Pendaftaran::whereMonth('created_at', now()->subMonths($i)->month)
                ->whereYear('created_at', now()->subMonths($i)->year)
                ->count();
        })->values()->toArray();

        //Kategori Pelatihan Dinamis
        $kategoris = \App\Models\KategoriPelatihan::orderBy('nama')->get();
        
        $kategori_distribution = $kategoris->map(function ($kat) {
            $count = Pelatihan::where('kategori_id', $kat->id)->count();
            return [
                'nama'  => $kat->nama,
                'count' => $count,
                'color' => $kat->warna ?? '#6366f1',
            ];
        });

        return view('dashboard-admin', compact(
            'total_pelatihan', 'total_mentor', 'total_peserta',
            'pelatihan_aktif', 'total_sertifikat', 'chart_data',
            'kategori_distribution'
        ));
    }
}
