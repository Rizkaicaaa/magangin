<?php

namespace App\Http\Controllers;

use App\Models\HasilSeleksi;

class HasilWawancaraController extends Controller
{
    public function index()
    {
        $hasilSeleksi = HasilSeleksi::all();
        return view('hasilwawancara.index', compact('hasilSeleksi'));
    }
}
