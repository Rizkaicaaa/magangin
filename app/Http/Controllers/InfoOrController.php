<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InfoOr;

class InfoOrController extends Controller
{
    /**
     * Menampilkan daftar semua info OR.
     */
    public function index()
    {
        $infoOrs = InfoOr::all();
        return view('info_or.index', compact('infoOrs'));
    }

    /**
     * Menyimpan data info OR yang baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'persyaratan_umum' => 'nullable|string',
            'tanggal_buka' => 'required|date',
            'tanggal_tutup' => 'required|date',
            'periode' => 'nullable|string|max:50',
            'gambar' => 'nullable|string|max:100',
            'status' => 'required|in:buka,tutup',
        ]);

        InfoOr::create($request->all());

        return redirect()->route('info-or.index')->with('success', 'Info OR berhasil ditambahkan!');
    }

    /**
     * Mengubah status info OR menjadi 'tutup'.
     */
    public function updateStatus($id)
    {
        $infoOr = InfoOr::findOrFail($id);
        $infoOr->status = 'tutup';
        $infoOr->save();

        return redirect()->route('info-or.index')->with('success', 'Info OR berhasil ditutup!');
    }
}