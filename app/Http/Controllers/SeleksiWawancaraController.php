<?php

namespace App\Http\Controllers;

use App\Models\JadwalSeleksi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SeleksiWawancaraController extends Controller
{
    public function index()
    {
        // Ambil tanggal hari ini
        $today = Carbon::today();

        // Ambil jadwal seleksi yang tanggalnya >= hari ini
        $jadwals = JadwalSeleksi::with('infoOr')
            ->whereDate('tanggal_seleksi', '>=', $today)
            ->orderBy('tanggal_seleksi', 'asc')
            ->get();

        return view('seleksi-wawancara.index', compact('jadwals'));
    }
}
