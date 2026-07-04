<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelatihan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class SertifikatDesignController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pelatihan::with('kategoriPelatihan');

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $pelatihans = $query->latest()->get();

        return view('admin.sertifikat_design.index', compact('pelatihans'));
    }

    public function edit(int $id): View
    {
        $pelatihan = Pelatihan::findOrFail($id);
        return view('admin.sertifikat_design.edit', compact('pelatihan'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'sertifikat_template'   => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'sertifikat_logo_kiri'  => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'sertifikat_logo_kanan' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $pelatihan = Pelatihan::findOrFail($id);

        // 1. Sertifikat Template Background
        if ($request->hasFile('sertifikat_template')) {
            if ($pelatihan->sertifikat_template && Storage::disk('public')->exists($pelatihan->sertifikat_template)) {
                Storage::disk('public')->delete($pelatihan->sertifikat_template);
            }
            $pelatihan->sertifikat_template = $request->file('sertifikat_template')->store('sertifikat_templates', 'public');
        } elseif ($request->boolean('hapus_template')) {
            if ($pelatihan->sertifikat_template && Storage::disk('public')->exists($pelatihan->sertifikat_template)) {
                Storage::disk('public')->delete($pelatihan->sertifikat_template);
            }
            $pelatihan->sertifikat_template = null;
        }

        // 2. Logo Kiri
        if ($request->hasFile('sertifikat_logo_kiri')) {
            if ($pelatihan->sertifikat_logo_kiri && Storage::disk('public')->exists($pelatihan->sertifikat_logo_kiri)) {
                Storage::disk('public')->delete($pelatihan->sertifikat_logo_kiri);
            }
            $pelatihan->sertifikat_logo_kiri = $request->file('sertifikat_logo_kiri')->store('sertifikat_logos', 'public');
        } elseif ($request->boolean('hapus_logo_kiri')) {
            if ($pelatihan->sertifikat_logo_kiri && Storage::disk('public')->exists($pelatihan->sertifikat_logo_kiri)) {
                Storage::disk('public')->delete($pelatihan->sertifikat_logo_kiri);
            }
            $pelatihan->sertifikat_logo_kiri = null;
        }

        // 3. Logo Kanan
        if ($request->hasFile('sertifikat_logo_kanan')) {
            if ($pelatihan->sertifikat_logo_kanan && Storage::disk('public')->exists($pelatihan->sertifikat_logo_kanan)) {
                Storage::disk('public')->delete($pelatihan->sertifikat_logo_kanan);
            }
            $pelatihan->sertifikat_logo_kanan = $request->file('sertifikat_logo_kanan')->store('sertifikat_logos', 'public');
        } elseif ($request->boolean('hapus_logo_kanan')) {
            if ($pelatihan->sertifikat_logo_kanan && Storage::disk('public')->exists($pelatihan->sertifikat_logo_kanan)) {
                Storage::disk('public')->delete($pelatihan->sertifikat_logo_kanan);
            }
            $pelatihan->sertifikat_logo_kanan = null;
        }

        $pelatihan->save();

        return redirect()->route('admin.desain-sertifikat.index')
            ->with('success', 'Desain sertifikat untuk pelatihan "' . $pelatihan->nama . '" berhasil diperbarui.');
    }
}
