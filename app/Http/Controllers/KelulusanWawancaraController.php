<?php

namespace App\Http\Controllers;

use App\Models\PenilaianWawancara;
use App\Models\User;
use Illuminate\Http\Request;

class KelulusanWawancaraController extends Controller
{
    public function index()
    {
        // Untuk halaman user sendiri
        $userId = auth()->id();

        $penilaian = PenilaianWawancara::whereHas('pendaftaran', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->first();

        return view('kelulusan-wawancara.index', compact('penilaian'));
    }

    public function show($id)
    {
        // Untuk melihat kelulusan user tertentu (by ID)
        $penilaian = PenilaianWawancara::whereHas('pendaftaran', function ($query) use ($id) {
            $query->where('user_id', $id);
        })->firstOrFail();

        return view('kelulusan-wawancara.show', compact('penilaian'));
    }
}