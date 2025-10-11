<?php

namespace App\Http\Controllers;

use App\Models\PenilaianWawancara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KelulusanWawancaraController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $penilaian = PenilaianWawancara::whereHas('pendaftaran', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->first();

        return view('kelulusan-wawancara.index', compact('penilaian'));
    }
}