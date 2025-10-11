<?php

namespace App\Http\Controllers;

use App\Models\JadwalSeleksi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SeleksiWawancaraController extends Controller
{
    public function index()
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Ambil tanggal hari ini
        $today = Carbon::today();

        // Ambil jadwal seleksi yang sesuai dengan pendaftar milik user ini
        $jadwals = JadwalSeleksi::with(['infoOr', 'pendaftaran.user'])
            ->whereHas('pendaftaran', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereDate('tanggal_seleksi', '>=', $today)
            ->orderBy('tanggal_seleksi', 'asc')
            ->get();

        return view('seleksi-wawancara.index', compact('jadwals'));
    }
}