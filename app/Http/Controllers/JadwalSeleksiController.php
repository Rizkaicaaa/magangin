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
        $query = JadwalSeleksi::with(['infoOr', 'pendaftaran']);

        if ($request->filled('tanggal')) {
            $tanggal = $request->input('tanggal');
            $query->whereDate('tanggal_seleksi', $tanggal);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pewawancara', 'like', '%' . $search . '%')
                  ->orWhere('tempat', 'like', '%' . $search . '%');
            });
        }

        $jadwals = $query->paginate(10);
        return view('jadwal-seleksi.index', compact('jadwals'));
    }

    public function create()
    {
        $infos = InfoOr::all();
        $pendaftarans = Pendaftaran::whereNull('jadwal_seleksi_id')->get(); // hanya pendaftaran yang belum dapat jadwal
        return view('jadwal-seleksi.create', compact('infos', 'pendaftarans'));
    }

    public function store(Request $request)
{
    $request->validate([
        'info_or_id' => 'required',
        'tanggal_seleksi' => 'required|date',
        'waktu_mulai' => 'required',
        'waktu_selesai' => 'required',
        'tempat' => 'required',
        'pewawancara' => 'required',
        'pendaftaran_id' => 'required|array',
    ]);

    $jadwal = JadwalSeleksi::create([
        'info_or_id' => $request->info_or_id,
        'tanggal_seleksi' => $request->tanggal_seleksi,
        'waktu_mulai' => $request->waktu_mulai,
        'waktu_selesai' => $request->waktu_selesai,
        'tempat' => $request->tempat,
        'pewawancara' => $request->pewawancara,
    ]);

    // simpan banyak peserta ke satu jadwal
    $jadwal->pendaftarans()->attach($request->pendaftaran_id);

    return redirect()->route('jadwal-seleksi.index')->with('success', 'Jadwal wawancara berhasil ditambahkan.');
}

    public function edit(JadwalSeleksi $jadwalSeleksi)
{
    $infos = InfoOr::all();
    // load relasi user + infoOr biar nama peserta muncul
    $pendaftarans = Pendaftaran::with(['user', 'infoOr'])->get();
    // load peserta yang sudah terdaftar di jadwal ini
    $jadwalSeleksi->load(['pendaftarans.user', 'pendaftarans.infoOr']);

    return view('jadwal-seleksi.edit', compact('jadwalSeleksi', 'infos', 'pendaftarans'));
}

    public function update(Request $request, JadwalSeleksi $jadwalSeleksi)
    {
        $request->validate([
            'info_or_id'      => 'required|exists:info_or,id',
            'pendaftaran_id'  => 'nullable|exists:pendaftaran,id',
            'tanggal_seleksi' => 'required|date',
            'waktu_mulai'     => 'required|date_format:H:i',
            'waktu_selesai'   => 'required|date_format:H:i|after:waktu_mulai',
            'tempat'          => 'required|string|max:255',
            'pewawancara'     => 'required|string|max:255',
        ]);

        $jadwalSeleksi->update([
            'info_or_id'      => $request->info_or_id,
            'pendaftaran_id'  => $request->pendaftaran_id,
            'tanggal_seleksi' => $request->tanggal_seleksi,
            'waktu_mulai'     => $request->waktu_mulai,
            'waktu_selesai'   => $request->waktu_selesai,
            'tempat'          => $request->tempat,
            'pewawancara'     => $request->pewawancara,
        ]);

        // update relasi di tabel pendaftaran
        if ($request->pendaftaran_id) {
            $pendaftaran = Pendaftaran::find($request->pendaftaran_id);
            $pendaftaran->update(['jadwal_seleksi_id' => $jadwalSeleksi->id]);
        }

        return redirect()->route('jadwal-seleksi.index')->with('success', 'Jadwal seleksi berhasil diperbarui.');
    }

    public function destroy(JadwalSeleksi $jadwalSeleksi)
    {
        // putuskan hubungan dengan pendaftaran (biar gak orphan)
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
    // load lengkap dengan relasi peserta dan user-nya
    $jadwal = JadwalSeleksi::with(['infoOr', 'pendaftarans.user', 'pendaftarans.infoOr'])->findOrFail($id);
    return view('jadwal-seleksi.show', compact('jadwal'));
}
}
