<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InfoOr;
use Illuminate\Support\Facades\Storage;

class InfoOrController extends Controller
{
    /**
     * Menampilkan daftar semua info OR.
     */
    public function index()
    {
        $infoOrs = InfoOr::orderBy('id', 'desc')->get();
        return view(view: 'info_or.index', data: compact('infoOrs'));
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
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:buka,tutup',
        ]);
        
        $data = $request->except('gambar'); 
        
        if ($request->hasFile('gambar')) {
            // Menggunakan disk 'gambar_public' untuk menyimpan file
            $fileName = $request->file('gambar')->hashName();
            $request->file('gambar')->storeAs('', $fileName, 'gambar_public');
            $data['gambar'] = 'images/' . $fileName; 
        }
        
        // if ($request->hasFile('gambar')) {
        //     dd($request->file('gambar')); // Ini akan menampilkan semua detail file
        // } else {
        //     dd('Tidak ada file gambar yang diunggah.');
        // }
        
        InfoOr::create($data);

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