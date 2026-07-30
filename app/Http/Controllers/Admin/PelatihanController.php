<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelatihan;
use App\Models\SesiPelatihan;
use App\Models\User;
use App\Models\KategoriPelatihan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class PelatihanController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pelatihan::with(['mentors', 'sesi', 'kategoriPelatihan']);

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }
        if ($request->filled('tipe')) {
            if ($request->tipe === 'gratis')   $query->where('berbayar', false);
            if ($request->tipe === 'berbayar') $query->where('berbayar', true);
        }
        if ($request->filled('kategori_id') && $request->kategori_id !== 'semua') {
            $query->where('kategori_id', $request->kategori_id);
        }

        $pelatihans = $query->latest()->get();
        $mentors    = User::where('role', 'mentor')->orderBy('name')->get();
        $kategoris  = KategoriPelatihan::orderBy('nama')->pluck('nama', 'id')->toArray();

        return view('admin.pelatihan.index', compact('pelatihans', 'mentors', 'kategoris'));
    }

    public function create(): View
    {
        $mentors   = User::where('role', 'mentor')->orderBy('name')->get();
        $kategoris = KategoriPelatihan::orderBy('nama')->pluck('nama', 'id')->toArray();
        return view('admin.pelatihan.create', compact('mentors', 'kategoris'));
    }

    public function store(Request $request): RedirectResponse
    {
                $request->validate([
            'nama'   => 'required|string|max:255',
            'kuota'  => 'required|integer|min:1',
            'lat'    => 'nullable|numeric|between:-90,90',
            'lng'    => 'nullable|numeric|between:-180,180',
            'biaya'  => 'nullable|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'tenggat_pendaftaran' => 'nullable|date|after:now',
            'kategori_id' => 'required|exists:kategori_pelatihans,id',
            'sesi.*.tanggal' => 'nullable|date|after_or_equal:today',
        ], [
            'tenggat_pendaftaran.after' => 'Tenggat pendaftaran tidak boleh di masa lampau.',
            'sesi.*.tanggal.after_or_equal' => 'Tanggal pelatihan tidak boleh di masa lampau.',
        ]);

        $path = null;
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('pelatihan', 'public');
        }

        $pelatihan = Pelatihan::create([
            'nama'          => $request->nama,
            'deskripsi'     => $request->deskripsi,
            'gambar'        => $path,
                        'kategori_id'      => $request->kategori_id,
            'kuota'         => (int) $request->kuota,
            'berbayar'      => (bool) $request->berbayar,
            'biaya'         => $request->berbayar ? (int) $request->biaya : null,
            'nama_bank'     => $request->berbayar ? $request->nama_bank : null,
            'nomor_rekening'=> $request->berbayar ? $request->nomor_rekening : null,
            'atas_nama'     => $request->berbayar ? $request->atas_nama : null,
            'lokasi_nama'   => $request->lokasi_nama,
            'lokasi_alamat' => $request->lokasi_alamat,
            'lat'           => $request->filled('lat') ? (float) $request->lat : null,
            'lng'           => $request->filled('lng') ? (float) $request->lng : null,
            'status'        => $request->status ?? 'aktif',
            'catatan'       => $request->catatan,
            'tenggat_pendaftaran' => $request->tenggat_pendaftaran,
        ]);

        //memilih mentor 
        if ($request->filled('mentor_ids')) {
            $pelatihan->mentors()->sync($request->mentor_ids);
        }

        if ($request->has('sesi') && is_array($request->sesi)) {
            foreach ($request->sesi as $s) {
                if (!empty($s['tanggal']) || !empty($s['topik'])) {
                    SesiPelatihan::create([
                        'pelatihan_id'  => $pelatihan->id,
                        'topik'         => $s['topik'] ?? null,
                        'tanggal'       => $s['tanggal'] ?? null,
                        'waktu_mulai'   => $s['waktu_mulai'] ?? null,
                        'waktu_selesai' => $s['waktu_selesai'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.pelatihan.index')
            ->with('success', 'Pelatihan "' . $pelatihan->nama . '" berhasil ditambahkan.');
    }

    public function show(int $id): View
    {
        $pelatihan = Pelatihan::with([
            'sesi',
            'mentors',
            'pendaftarans.peserta',
            'kategoriPelatihan',
        ])->findOrFail($id);

        return view('admin.pelatihan.show', compact('pelatihan'));
    }

    public function edit(int $id): View
    {
        $pelatihan = Pelatihan::with(['sesi', 'mentors'])->findOrFail($id);
        $mentors   = User::where('role', 'mentor')->orderBy('name')->get();
        $kategoris = KategoriPelatihan::orderBy('nama')->pluck('nama', 'id')->toArray();
        return view('admin.pelatihan.edit', compact('pelatihan', 'mentors', 'kategoris'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'nama'   => 'required|string|max:255',
            'kuota'  => 'required|integer|min:1',
            'lat'    => 'nullable|numeric|between:-90,90',
            'lng'    => 'nullable|numeric|between:-180,180',
            'biaya'  => 'nullable|integer|min:0',
            'status' => 'nullable|in:aktif,draft,selesai',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'tenggat_pendaftaran' => 'nullable|date|after:now',
            'sesi.*.tanggal' => 'nullable|date|after_or_equal:today',
        ], [
            'tenggat_pendaftaran.after' => 'Tenggat pendaftaran tidak boleh di masa lampau.',
            'sesi.*.tanggal.after_or_equal' => 'Tanggal pelatihan tidak boleh di masa lampau.',
        ]);

        $pelatihan = Pelatihan::findOrFail($id);

        if ($request->hasFile('gambar')) {
            //Hapus gambar lama
            if ($pelatihan->gambar && Storage::disk('public')->exists($pelatihan->gambar)) {
                Storage::disk('public')->delete($pelatihan->gambar);
            }
            $path = $request->file('gambar')->store('pelatihan', 'public');
            $pelatihan->gambar = $path;
        }

        $pelatihan->update([
            'nama'          => $request->nama,
            'deskripsi'     => $request->deskripsi,
            'kategori_id'      => $request->kategori_id,
            'kuota'         => (int) $request->kuota,
            'berbayar'      => (bool) $request->berbayar,
            'biaya'         => $request->berbayar ? (int) $request->biaya : null,
            'nama_bank'     => $request->berbayar ? $request->nama_bank : null,
            'nomor_rekening'=> $request->berbayar ? $request->nomor_rekening : null,
            'atas_nama'     => $request->berbayar ? $request->atas_nama : null,
            'lokasi_nama'   => $request->lokasi_nama,
            'lokasi_alamat' => $request->lokasi_alamat,
            'lat'           => $request->filled('lat') ? (float) $request->lat : null,
            'lng'           => $request->filled('lng') ? (float) $request->lng : null,
            'status'        => $request->status ?? $pelatihan->status,
            'catatan'       => $request->catatan,
            'tenggat_pendaftaran' => $request->tenggat_pendaftaran,
        ]);

        //memilih mentor 
        $pelatihan->mentors()->sync($request->mentor_ids ?? []);

        //isi sesi
        if ($request->has('sesi') && is_array($request->sesi)) {
            $submittedIds = [];
            foreach ($request->sesi as $s) {
                if (!empty($s['id'])) {
                    SesiPelatihan::where('id', $s['id'])
                        ->where('pelatihan_id', $pelatihan->id)
                        ->update([
                            'topik'         => $s['topik'] ?? null,
                            'tanggal'       => $s['tanggal'] ?: null,
                            'waktu_mulai'   => $s['waktu_mulai'] ?: null,
                            'waktu_selesai' => $s['waktu_selesai'] ?: null,
                        ]);
                    $submittedIds[] = (int) $s['id'];
                } elseif (!empty($s['topik']) || !empty($s['tanggal'])) {
                    $newSesi = SesiPelatihan::create([
                        'pelatihan_id'  => $pelatihan->id,
                        'topik'         => $s['topik'] ?? null,
                        'tanggal'       => $s['tanggal'] ?: null,
                        'waktu_mulai'   => $s['waktu_mulai'] ?: null,
                        'waktu_selesai' => $s['waktu_selesai'] ?: null,
                    ]);
                    $submittedIds[] = $newSesi->id;
                }
            }
            SesiPelatihan::where('pelatihan_id', $pelatihan->id)
                ->whereNotIn('id', $submittedIds)->delete();
        } else {
            SesiPelatihan::where('pelatihan_id', $pelatihan->id)->delete();
        }

        return redirect()->route('admin.pelatihan.index')
            ->with('success', 'Pelatihan "' . $pelatihan->nama . '" berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $pelatihan = Pelatihan::findOrFail($id);
        $pelatihan->mentors()->detach();
        $pelatihan->delete();

        return redirect()->route('admin.pelatihan.index')
            ->with('success', 'Pelatihan berhasil dihapus.');
    }

    //Toggle status aktif ↔ selesai langsung dari tabel
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:aktif,selesai,draft',
        ]);

        $pelatihan = Pelatihan::findOrFail($id);
        $pelatihan->update(['status' => $request->status]);

        $label = match($request->status) {
            'aktif'   => 'Aktif',
            'selesai' => 'Selesai',
            default   => 'Draft',
        };

        return back()->with('success', '"' . $pelatihan->nama . '" diubah ke status ' . $label . '.');
    }
}
