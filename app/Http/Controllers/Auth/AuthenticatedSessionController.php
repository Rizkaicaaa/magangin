<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Dinas;
use App\Models\InfoOr; // <--- 1. TAMBAHKAN IMPORT INI

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $allDinas = Dinas::all();
        
        // <--- 2. TAMBAHKAN LOGIKA INI
        // Ambil Info OR terbaru agar halaman login tahu ada pendaftaran buka/tutup
        $infoOr = InfoOr::orderBy('created_at', 'desc')->first(); 
        // ------------------------

        // <--- 3. MASUKKAN 'infoOr' KE DALAM COMPACT
        return view('auth.login', compact('allDinas', 'infoOr'));
    }

    // ... method lainnya biarkan tetap sama ...
    
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}