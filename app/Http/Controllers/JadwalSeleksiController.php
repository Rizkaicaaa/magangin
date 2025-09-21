<?php

namespace App\Http\Controllers;

use App\Models\JadwalSeleksi;
use App\Models\InfoOr;
use Illuminate\Http\Request;

class JadwalSeleksiController extends Controller
{
    public function index()
    {
        $jadwals = JadwalSeleksi::with('infoOr')->latest()->paginate(10);
         $infos   = InfoOr::all();
        return view('jadwal-seleksi.index', compact('jadwals', 'infos'));
    }

    public function create()
    {
        $infos = InfoOr::all();
        return view('jadwal-seleksi.create', compact('infos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'info_or_id'     => 'required|exists:info_or,id',
            'tanggal_seleksi'=> 'required|date',
            'waktu_mulai'    => 'required|date_format:H:i',
            'waktu_selesai'  => 'required|date_format:H:i|after:waktu_mulai',
            'tempat'         => 'required|string|max:255',
        ]);

        JadwalSeleksi::create($request->all());

        return redirect()->route('jadwal-seleksi.index')->with('success', 'Jadwal seleksi berhasil ditambahkan.');
    }

    public function edit(JadwalSeleksi $jadwalSeleksi)
    {
        $infos = InfoOr::all();
        return view('jadwal-seleksi.edit', compact('jadwalSeleksi','infos'));
    }

    public function update(Request $request, JadwalSeleksi $jadwalSeleksi)
    {
        $request->validate([
            'info_or_id'     => 'required|exists:info_or,id',
            'tanggal_seleksi'=> 'required|date',
            'waktu_mulai'    => 'required|date_format:H:i',
            'waktu_selesai'  => 'required|date_format:H:i|after:waktu_mulai',
            'tempat'         => 'required|string|max:255',
        ]);

        $jadwalSeleksi->update($request->all());

        return redirect()->route('jadwal-seleksi.index')->with('success', 'Jadwal seleksi berhasil diperbarui.');
    }

    public function destroy(JadwalSeleksi $jadwalSeleksi)
    {
        $jadwalSeleksi->delete();
        return redirect()->route('jadwal-seleksi.index')->with('success', 'Jadwal seleksi berhasil dihapus.');
    }
}
