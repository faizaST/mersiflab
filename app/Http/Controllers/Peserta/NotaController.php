<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotaController extends Controller
{
    public function download(int $pendaftaranId)
    {
        $query = Pendaftaran::with(['pelatihan', 'peserta'])
            ->where('id', $pendaftaranId)
            ->where('status', 'terverifikasi');

        // Jika bukan admin, pastikan milik user yang login
        if (Auth::user()->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        $pendaftaran = $query->firstOrFail();

        if (!$pendaftaran->pelatihan->berbayar) {
            return back()->with('error', 'Pelatihan gratis tidak memerlukan nota pembayaran.');
        }

        $pdf = Pdf::loadView('pdf.nota', compact('pendaftaran'));
        
        $filename = 'Nota_' . str_replace([' ', '/', '\\'], '_', $pendaftaran->pelatihan->nama) . '_' . date('YmdHis') . '.pdf';
        
        return $pdf->download($filename);
    }
}
