<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Pelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendaftaran::with(['peserta', 'pelatihan'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('peserta', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('pelatihan_id')) {
            $query->where('pelatihan_id', $request->pelatihan_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pendaftarans = $query->get();
        $pelatihans   = Pelatihan::latest()->get();
        $stats = [
            'pending'       => Pendaftaran::where('status', 'pending')->count(),
            'terverifikasi' => Pendaftaran::where('status', 'terverifikasi')->count(),
            'ditolak'       => Pendaftaran::where('status', 'ditolak')->count(),
        ];
        return view('admin.pendaftaran.index', compact('pendaftarans', 'pelatihans', 'stats'));
    }

    public function verifikasi(int $id)
    {
        Pendaftaran::findOrFail($id)->update(['status' => 'terverifikasi']);
        return back()->with('success', 'Pendaftaran berhasil diverifikasi.');
    }

    public function tolak(int $id)
    {
        Pendaftaran::findOrFail($id)->update([
            'status'        => 'ditolak',
            'catatan_admin' => 'Pembayaran tidak valid',
        ]);
        return back()->with('success', 'Pendaftaran ditolak.');
    }

    public function batalVerifikasi(int $id)
    {
        $daftar = Pendaftaran::with('sertifikat')->findOrFail($id);

        // Hapus sertifikat jika ada
        if ($daftar->sertifikat) {
            if ($daftar->sertifikat->file_path && Storage::disk('public')->exists($daftar->sertifikat->file_path)) {
                Storage::disk('public')->delete($daftar->sertifikat->file_path);
            }
            $daftar->sertifikat->delete();
        }

        // Hapus presensi jika ada
        \App\Models\Presensi::where('user_id', $daftar->user_id)
            ->whereHas('sesi', fn($q) => $q->where('pelatihan_id', $daftar->pelatihan_id))
            ->delete();

        $daftar->update(['status' => 'pending']);

        return back()->with('success', 'Verifikasi pendaftaran berhasil dibatalkan.');
    }

    /* Tampilkan file bukti bayar */
    public function lihatBukti(int $id)
    {
        $daftar = Pendaftaran::findOrFail($id);

        abort_if(!$daftar->bukti_bayar, 404, 'Bukti pembayaran belum diupload.');
        abort_if(!Storage::disk('public')->exists($daftar->bukti_bayar), 404, 'File tidak ditemukan.');

        $path = Storage::disk('public')->path($daftar->bukti_bayar);
        $mime = mime_content_type($path) ?: 'application/octet-stream';
        $ext  = pathinfo($daftar->bukti_bayar, PATHINFO_EXTENSION);

        // Tampilkan untuk gambar, download untuk PDF
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return response(
                Storage::disk('public')->get($daftar->bukti_bayar),
                200,
                ['Content-Type' => $mime, 'Content-Disposition' => 'inline']
            );
        }

        return response()->download(
            Storage::disk('public')->path($daftar->bukti_bayar),
            'bukti-bayar-' . $daftar->id . '.' . $ext
        );
    }
}
