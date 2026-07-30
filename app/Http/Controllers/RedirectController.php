<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    //Redirect ke dashboard sesuai role user yang login
    public function dashboard(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        return match($user->role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'mentor' => redirect()->route('mentor.dashboard'),
            default  => redirect()->route('peserta.dashboard'),
        };
    }
}
