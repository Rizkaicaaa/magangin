<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman edit profil pengguna.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update informasi profil pengguna.
     */
    public function update(Request $request): RedirectResponse
    {
        // Validasi input
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($request->user()->id),
            ],
        ]);

        //Ambil user yang sedang login
        $user = $request->user();

        // Update field sesuai input
        $user->nama_lengkap = $validated['nama_lengkap'];
        $user->email = $validated['email'];

      
        // Simpan perubahan
        $user->save();

        // Redirect dengan notifikasi sukses
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function editPassword(): View
{
    return view('profile.edit-password', [
        'user' => auth()->user(),
    ]);
}

public function updatePassword(Request $request): RedirectResponse
{
    $request->validate([
        'current_password' => ['required', 'current_password'],
        'password' => ['required', 'confirmed', 'min:8'],
    ]);

    $user = $request->user();
    $user->update([
        'password' => bcrypt($request->password),
    ]);

    return Redirect::route('profile.password.edit')->with('status', 'password-updated');
}

}