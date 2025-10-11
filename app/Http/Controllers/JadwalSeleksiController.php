<?php

namespace App\Http\Controllers;

use App\Models\JadwalSeleksi;
use App\Models\InfoOr;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class JadwalSeleksiController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalSeleksi::with(['infoOr', 'pendaftaran.user']);

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_seleksi', $request->tanggal);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pewawancara', 'like', "%$search%")
                  ->orWhere('tempat', 'like', "%$search%");
            });
        }

        $jadwals = $query->paginate(10);
        return view('jadwal-seleksi.index', compact('jadwals'));
    }

    public function create()
    {
        $infos = InfoOr::all();
        $pendaftarans = Pendaftaran::whereNull('jadwal_seleksi_id')->with(['user', 'infoOr'])->get();
        return view('jadwal-seleksi.create', compact('infos', 'pendaftarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'info_or_id' => 'required|exists:info_or,id',
            'tanggal_seleksi' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'tempat' => 'required|string|max:255',
            'pewawancara' => 'required|string|max:255',
            'pendaftaran_id' => 'required|integer|exists:pendaftaran,id',
        ]);

        $jadwal = JadwalSeleksi::create([
            'info_or_id' => $request->info_or_id,
            'tanggal_seleksi' => $request->tanggal_seleksi,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'tempat' => $request->tempat,
            'pewawancara' => $request->pewawancara,
            'pendaftaran_id' => $request->pendaftaran_id,
        ]);

        // update pendaftaran agar tahu jadwal mana dia ikut
        $pendaftaran = Pendaftaran::find($request->pendaftaran_id);
        $pendaftaran->update(['jadwal_seleksi_id' => $jadwal->id]);

        return redirect()->route('jadwal-seleksi.index')->with('success', 'Jadwal wawancara berhasil ditambahkan.');
    }

  public function edit(JadwalSeleksi $jadwalSeleksi)
{
    $infos = InfoOr::all();

    // Ambil semua pendaftar yang belum punya jadwal
    // ATAU pendaftar yang sudah terhubung ke jadwal ini
    $pendaftarans = Pendaftaran::whereNull('jadwal_seleksi_id')
        ->orWhere('jadwal_seleksi_id', $jadwalSeleksi->id)
        ->with(['user', 'infoOr'])
        ->get();

    return view('jadwal-seleksi.edit', compact('jadwalSeleksi', 'infos', 'pendaftarans'));
}


    public function update(Request $request, JadwalSeleksi $jadwalSeleksi)
    {
        $request->validate([
            'info_or_id' => 'required|exists:info_or,id',
            'tanggal_seleksi' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'tempat' => 'required|string|max:255',
            'pewawancara' => 'required|string|max:255',
            'pendaftaran_id' => 'required|integer|exists:pendaftaran,id',
        ]);

        $jadwalSeleksi->update([
            'info_or_id' => $request->info_or_id,
            'tanggal_seleksi' => $request->tanggal_seleksi,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'tempat' => $request->tempat,
            'pewawancara' => $request->pewawancara,
            'pendaftaran_id' => $request->pendaftaran_id,
        ]);

        // update pendaftaran
        $pendaftaran = Pendaftaran::find($request->pendaftaran_id);
        $pendaftaran->update(['jadwal_seleksi_id' => $jadwalSeleksi->id]);

        return redirect()->route('jadwal-seleksi.index')->with('success', 'Jadwal seleksi berhasil diperbarui.');
    }

    public function destroy(JadwalSeleksi $jadwalSeleksi)
    {
        if ($jadwalSeleksi->pendaftaran_id) {
            $pendaftaran = Pendaftaran::find($jadwalSeleksi->pendaftaran_id);
            if ($pendaftaran) {
                $pendaftaran->update(['jadwal_seleksi_id' => null]);
            }
        }

        $jadwalSeleksi->delete();
        return redirect()->route('jadwal-seleksi.index')->with('success', 'Jadwal seleksi berhasil dihapus.');
    }

    public function show($id)
    {
        $jadwal = JadwalSeleksi::with(['infoOr', 'pendaftaran.user', 'pendaftaran.infoOr'])->findOrFail($id);
        return view('jadwal-seleksi.show', compact('jadwal'));
    }
}