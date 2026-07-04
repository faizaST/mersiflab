<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriPelatihan;
use App\Models\Pelatihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriPelatihanController extends Controller
{
    public function index(): View
    {
        $kategoris = KategoriPelatihan::orderBy('nama')->get();
        return view('admin.kategori.index', compact('kategoris'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama'  => 'required|string|max:255|unique:kategori_pelatihans,nama',
            'warna' => 'required|string|size:7|regex:/^#[a-fA-F0-9]{6}$/',
        ], [
            'nama.required'  => 'Nama kategori wajib diisi.',
            'nama.unique'    => 'Nama kategori sudah terdaftar.',
            'nama.max'       => 'Nama kategori maksimal 255 karakter.',
            'warna.required' => 'Warna kategori wajib diisi.',
            'warna.size'     => 'Warna harus berupa kode HEX lengkap (contoh: #6366F1).',
            'warna.regex'    => 'Format warna tidak valid (harus kode HEX diawali dengan #).',
        ]);

        KategoriPelatihan::create([
            'nama'  => trim($request->nama),
            'warna' => $request->warna,
        ]);

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori Pelatihan "' . $request->nama . '" berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $kategori = KategoriPelatihan::findOrFail($id);

        $request->validate([
            'nama'  => 'required|string|max:255|unique:kategori_pelatihans,nama,' . $id,
            'warna' => 'required|string|size:7|regex:/^#[a-fA-F0-9]{6}$/',
        ], [
            'nama.required'  => 'Nama kategori wajib diisi.',
            'nama.unique'    => 'Nama kategori sudah terdaftar.',
            'nama.max'       => 'Nama kategori maksimal 255 karakter.',
            'warna.required' => 'Warna kategori wajib diisi.',
            'warna.size'     => 'Warna harus berupa kode HEX lengkap (contoh: #6366F1).',
            'warna.regex'    => 'Format warna tidak valid (harus kode HEX diawali dengan #).',
        ]);

        $oldName = $kategori->nama;
        $newName = trim($request->nama);

        $kategori->update([
            'nama'  => $newName,
            'warna' => $request->warna,
        ]);

        // Automatically update all training sessions using the old category name
        // No need to update trainings; they reference the category by ID.

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori Pelatihan berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $kategori = KategoriPelatihan::findOrFail($id);
        $name = $kategori->nama;
        
        // Nullify category on associated trainings
        Pelatihan::where('kategori_id', $id)->update([
            'kategori_id' => null,
        ]);

        $kategori->delete();

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori Pelatihan "' . $name . '" berhasil dihapus.');
    }

    /**
     * API endpoint to add category dynamically via AJAX in Pelatihan form
     */
    public function storeAjax(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:255|unique:kategori_pelatihans,nama',
            'warna' => 'nullable|string|size:7|regex:/^#[a-fA-F0-9]{6}$/',
        ]);

        $kategori = KategoriPelatihan::create([
            'nama'  => trim($request->nama),
            'warna' => $request->warna ?? '#6366f1',
        ]);

        return response()->json([
            'success' => true,
            'kategori' => $kategori,
        ]);
    }
}
