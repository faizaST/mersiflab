<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelatihan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $mentors    = User::where('role', 'mentor')->with('pelatihans')->latest()->get();
        $peserta    = User::where('role', 'peserta')->latest()->get();
        $pelatihans = Pelatihan::latest()->with('mentors')->get();

        return view('admin.users.index', compact('mentors', 'peserta', 'pelatihans'));
    }

    public function storeMentor(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'nullable|string|max:20',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'phone'    => $request->phone,
            'role'     => 'mentor',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Mentor "' . $request->name . '" berhasil ditambahkan.');
    }

    //Assign mentor ke pelatihan
    public function assignMentor(Request $request): RedirectResponse
    {
        $request->validate([
            'mentor_id'    => 'required|exists:users,id',
            'pelatihan_id' => 'required|exists:pelatihans,id',
        ]);

        $mentor    = User::where('id', $request->mentor_id)->where('role', 'mentor')->firstOrFail();
        $pelatihan = Pelatihan::findOrFail($request->pelatihan_id);

       
        $mentor->pelatihans()->syncWithoutDetaching([$pelatihan->id]);

        return redirect()->route('admin.users.index')
            ->with('success', $mentor->name . ' berhasil diassign ke "' . $pelatihan->nama . '".');
    }

    //Lepas assignment mentor
    public function unassignMentor(Request $request): RedirectResponse
    {
        $request->validate([
            'mentor_id'    => 'required|exists:users,id',
            'pelatihan_id' => 'required|exists:pelatihans,id',
        ]);

        $mentor = User::findOrFail($request->mentor_id);
        $mentor->pelatihans()->detach($request->pelatihan_id);

        return redirect()->route('admin.users.index')
            ->with('success', 'Assignment berhasil dilepas.');
    }

    public function destroyMentor(int $id): RedirectResponse
    {
        $mentor = User::where('id', $id)->where('role', 'mentor')->firstOrFail();
        $name   = $mentor->name;
        $mentor->pelatihans()->detach();
        $mentor->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Mentor "' . $name . '" berhasil dihapus.');
    }

    //Reset password peserta oleh admin
    public function resetPasswordPeserta(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $peserta = User::where('id', $id)->where('role', 'peserta')->firstOrFail();
        $peserta->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Password peserta "' . $peserta->name . '" berhasil direset.');
    }
}
