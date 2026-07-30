<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Pelatihan;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PelatihanController extends Controller
{
    public function index(Request $request)
    {
        //Pendaftaran user
        $pendaftarans = Pendaftaran::with('pelatihan')
            ->where('user_id', Auth::id())
            ->get()
            ->keyBy('pelatihan_id');

        $query = Pelatihan::with(['mentors', 'kategoriPelatihan'])
            ->withCount(['pendaftarans as peserta_count' => function($query) {
                $query->where('status', '!=', 'ditolak');
            }])
            ->whereIn('status', ['aktif', 'selesai']);

        //Filter hanya pelatihan yang sudah daftar
        if ($request->boolean('terdaftar')) {
            $query->whereIn('id', $pendaftarans->keys());
        }

        $pelatihans  = $query->latest()->get();
        $hanyaTerdaftar = $request->boolean('terdaftar');

        return view('peserta.pelatihan.index', compact('pelatihans', 'pendaftarans', 'hanyaTerdaftar'));
    }

    //Daftar pelatihan
    public function daftar(Request $request)
    {
        $request->validate(['pelatihan_id' => 'required|exists:pelatihans,id']);

        $pelatihan = Pelatihan::findOrFail($request->pelatihan_id);

        if ($pelatihan->status !== 'aktif') {
            return back()->with('error', 'Pelatihan ini sudah tidak menerima pendaftaran baru.');
        }

        if ($pelatihan->isFull()) {
            return back()->with('error', 'Maaf, kuota pelatihan ini sudah penuh.');
        }

        $sudah = Pendaftaran::where('user_id', Auth::id())
            ->where('pelatihan_id', $request->pelatihan_id)->exists();

        if ($sudah) {
            return back()->with('error', 'Anda sudah mendaftar ke pelatihan ini.');
        }

        Pendaftaran::create([
            'user_id'      => Auth::id(),
            'pelatihan_id' => $request->pelatihan_id,
            'status'       => 'pending',
        ]);

        return back()->with('success_daftar', $request->pelatihan_id);
    }

    //Upload bukti bayar
    public function uploadBukti(Request $request, int $pendaftaranId)
    {
        $request->validate([
            'bukti_bayar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $daftar = Pendaftaran::where('id', $pendaftaranId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

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
