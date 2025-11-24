<?php

namespace App\Http\Controllers;

use App\Models\TemplateSertifikat;
use App\Models\InfoOr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateSertifikatController extends Controller
{
    public function index()
    {
        // Ambil semua InfoOr yang buka
        $infoOrList = InfoOr::where('status', 'buka')->get();
        return view('template-sertifikat.index', compact('infoOrList'));
    }

    public function store(Request $request)
    {
        // Validasi request
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'file_template' => 'required|file|max:2048',
            'info_or_id' => 'required|integer|exists:info_or,id',
        ]);

        // Ambil ekstensi file
        $ext = strtolower($request->file('file_template')->getClientOriginalExtension());
        if ($ext !== 'html') {
            return back()->withErrors(['file_template' => 'File template harus berformat .html'])->withInput();
        }

        // Nama file otomatis
        $fileName = str_replace(' ', '_', strtolower($request->nama_template)) . '.' . $ext;

        // Pastikan folder ada
        Storage::disk('public')->makeDirectory('templates_sertifikat');

        // Simpan file pakai Storage (penting untuk unit test)
        $path = $request->file('file_template')->storeAs('templates_sertifikat', $fileName, 'public');

        // Simpan ke DB
        TemplateSertifikat::create([
            'info_or_id' => $request->info_or_id,
            'nama_template' => $request->nama_template,
            'file_template' => $path,
            'status' => 'aktif',
        ]);

        return redirect()->back()->with('success', 'Template sertifikat berhasil diupload!');
    }
}
