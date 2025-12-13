<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiMagangModel;
use Illuminate\Support\Facades\Auth;


class KelulusanMagangController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil evaluasi berdasarkan pendaftaran milik user yang login
        $evaluasi = EvaluasiMagangModel::with('pendaftaran')
            ->whereHas('pendaftaran', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->first();

        return view('kelulusan-magang.index', compact('evaluasi'));
    }
}