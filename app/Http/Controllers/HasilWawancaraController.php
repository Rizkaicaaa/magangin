<?php

namespace App\Http\Controllers;

use App\Models\HasilSeleksi;
use App\Models\PenilaianWawancara;

class HasilWawancaraController extends Controller
{
    // public function index()
    // {
    //     // Ambil semua data wawancara beserta pendaftaran & user
    //     $penilaians = PenilaianWawancara::with('pendaftaran.user')->get();

    //     return view('hasilwawancara.index', compact('penilaians'));
    // }

    // public function show($id)
    // {
    //     $penilaian = PenilaianWawancara::with('pendaftaran.user')->findOrFail($id);
    //     return view('hasilwawancara.show', compact('penilaian'));
    // }
}
