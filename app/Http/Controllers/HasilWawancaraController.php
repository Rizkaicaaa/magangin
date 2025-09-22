<?php

namespace App\Http\Controllers;

use App\Models\HasilSeleksi;

class HasilWawancaraController extends Controller
{
    public function index()
    {
        return view('hasilwawancara.index');
    }
}
