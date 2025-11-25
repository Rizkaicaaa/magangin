<?php

namespace App\Http\Controllers;

use App\Models\InfoOr;
use App\Models\TemplateSertifikatModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateSertifikatController extends Controller
{
    public function index()
    {
        $infoOrList = InfoOr::get();
        return view('template-sertifikat.index', compact('infoOrList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'file_template' => 'required|file|max:2048',
            'info_or_id' => 'required|integer|exists:info_or,id',
        ]);

        // Ambil ekstensi asli file
        $ext = strtolower($request->file('file_template')->getClientOriginalExtension());
        if ($ext !== 'html') {
            return back()->withErrors(['file_template' => 'File template harus berformat .html'])->withInput();
        }

        // Buat nama file berdasarkan nama template
        $fileName = str_replace(' ', '_', strtolower($request->nama_template)) . '.' . $ext;

        // Pastikan folder ada
        $directory = 'templates_sertifikat';
        Storage::disk('public')->makeDirectory($directory);

        // Simpan file ke storage dengan nama sesuai
        $path = $request->file('file_template')->storeAs($directory, $fileName, 'public');

        // Simpan ke database
        TemplateSertifikatModel::create([
            'info_or_id' => $request->info_or_id,
            'nama_template' => $request->nama_template,
            'file_template' => $path,
            'status' => 'aktif',
        ]);

        return redirect()->back()->with('success', 'Template sertifikat berhasil diupload!');
    }
}
