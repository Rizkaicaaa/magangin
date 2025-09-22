<?php

namespace App\Http\Controllers;

use App\Models\JadwalSeleksi;
use App\Models\InfoOr;
use Illuminate\Http\Request;

class JadwalSeleksiController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalSeleksi::query();

        if ($request->filled('tanggal')) {
            $tanggal = $request->input('tanggal');
            $query->whereDate('tanggal_seleksi', $tanggal);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('pewawancara', 'like', '%' . $search . '%')
              ->orWhere('tempat', 'like', '%' . $search . '%');
        }

        $jadwals = $query->paginate(10); 
        return view('jadwal-seleksi.index', compact('jadwals'));
    }

    public function create()
    {
        $infos = InfoOr::all();
        return view('jadwal-seleksi.create', compact('infos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'info_or_id'      => 'required|exists:info_or,id',
            'tanggal_seleksi' => 'required|date',
            'waktu_mulai'     => 'required|date_format:H:i',
            'waktu_selesai'   => 'required|date_format:H:i|after:waktu_mulai',
            'tempat'          => 'required|string|max:255',
            'pewawancara'     => 'required|string|max:255',
        ]);

        JadwalSeleksi::create([
            'info_or_id'      => $request->info_or_id,
            'tanggal_seleksi' => $request->tanggal_seleksi,
            'waktu_mulai'     => $request->waktu_mulai,
            'waktu_selesai'   => $request->waktu_selesai,
            'tempat'          => $request->tempat,
            'pewawancara'     => $request->pewawancara,
        ]);

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
            'info_or_id'      => 'required|exists:info_or,id',
            'tanggal_seleksi' => 'required|date',
            'waktu_mulai'     => 'required|date_format:H:i',
            'waktu_selesai'   => 'required|date_format:H:i|after:waktu_mulai',
            'tempat'          => 'required|string|max:255',
            'pewawancara'     => 'required|string|max:255',
        ]);

        $jadwalSeleksi->update([
            'info_or_id'      => $request->info_or_id,
            'tanggal_seleksi' => $request->tanggal_seleksi,
            'waktu_mulai'     => $request->waktu_mulai,
            'waktu_selesai'   => $request->waktu_selesai,
            'tempat'          => $request->tempat,
            'pewawancara'     => $request->pewawancara,
        ]);

        return redirect()->route('jadwal-seleksi.index')->with('success', 'Jadwal seleksi berhasil diperbarui.');
    }

    public function destroy(JadwalSeleksi $jadwalSeleksi)
    {
        $jadwalSeleksi->delete();
        return redirect()->route('jadwal-seleksi.index')->with('success', 'Jadwal berhasil dihapus!');
    }
    
    public function show($id)
    {
        $jadwal = JadwalSeleksi::findOrFail($id);
        return view('jadwal-seleksi.show', compact('jadwal'));
    }
}