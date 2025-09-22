<?php

namespace App\Http\Controllers;

use App\Models\PenilaianWawancara;
use App\Models\Pendaftaran;
use App\Models\JadwalSeleksi;
use Illuminate\Http\Request;

class PenilaianWawancaraController extends Controller
{
    public function index()
    {
        $data = PenilaianWawancara::with('pendaftaran.user', 'jadwal')->get();
        return view('penilaian-wawancara.index', compact('data'));
    }

    public function create()
    {
        $peserta = Pendaftaran::with('user')->get();
        $jadwalseleksi = JadwalSeleksi::all();
        return view('penilaian-wawancara.create', compact('peserta', 'jadwalseleksi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftaran,id',
            'jadwal_seleksi_id' => 'required|exists:jadwal_seleksi,id',
            'nilai_komunikasi' => 'nullable|numeric|min:0|max:100',
            'nilai_motivasi' => 'nullable|numeric|min:0|max:100',
            'nilai_kemampuan' => 'nullable|numeric|min:0|max:100',
            'hasil' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $jadwal = JadwalSeleksi::find($request->jadwal_seleksi_id);

        // Ambil nilai, jika null dianggap 0
        $komunikasi = $request->nilai_komunikasi ?? 0;
        $motivasi = $request->nilai_motivasi ?? 0;
        $kemampuan = $request->nilai_kemampuan ?? 0;

        // Hitung rata-rata
        $nilai_total = $request->nilai_komunikasi + $request->nilai_motivasi + $request->nilai_kemampuan;
        $nilai_rata_rata = $nilai_total / 3;

        PenilaianWawancara::create([
            'pendaftaran_id' => $request->pendaftaran_id,
            'penilai_id' => 1,
            'jadwal_seleksi_id' => $request->jadwal_seleksi_id,
            'nilai_komunikasi' => $request->nilai_komunikasi,
            'nilai_motivasi' => $request->nilai_motivasi,
            'nilai_kemampuan' => $request->nilai_kemampuan,
            'nilai_total' => $nilai_total,
            'nilai_rata_rata' => $nilai_rata_rata,
            'status' => $request->status,
        ]);

        $existing = PenilaianWawancara::where('pendaftaran_id', $request->pendaftaran_id)->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Peserta ini sudah memiliki penilaian.');
        }

        if ($request->status === 'belum_dinilai') {
            return redirect()->route('penilaian-wawancara.index');
        }

        return redirect()->route('penilaian-wawancara.index')->with('success', 'Penilaian berhasil ditambahkan.');
    }

    public function edit(PenilaianWawancara $penilaianWawancara)
    {
        $peserta = Pendaftaran::with('user')->get();
        $jadwalseleksi = JadwalSeleksi::all();
        return view('penilaian-wawancara.edit', compact('penilaianWawancara', 'peserta', 'jadwalseleksi'));
    }

    public function update(Request $request, PenilaianWawancara $penilaianWawancara)
    {
        $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftaran,id',
            'jadwal_seleksi_id' => 'required|exists:jadwal_seleksi,id',
            'nilai_komunikasi' => 'nullable|numeric|min:0|max:100',
            'nilai_motivasi' => 'nullable|numeric|min:0|max:100',
            'nilai_kemampuan' => 'nullable|numeric|min:0|max:100',
            'hasil' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $jadwal = JadwalSeleksi::find($request->jadwal_seleksi_id);

        $komunikasi = $request->nilai_komunikasi ?? 0;
        $motivasi = $request->nilai_motivasi ?? 0;
        $kemampuan = $request->nilai_kemampuan ?? 0;
        $nilai_total = ($komunikasi + $motivasi + $kemampuan) / 3;

        $penilaianWawancara->update([
            'pendaftaran_id' => $request->pendaftaran_id,
            'penilai_id' => 1,
            'pewawancara' => $jadwal->pewawancara,
            'nilai_komunikasi' => $komunikasi,
            'nilai_motivasi' => $motivasi,
            'nilai_kemampuan' => $kemampuan,
            'nilai_rata_rata' => round($nilai_total, 2), 
            'status' => 'sudah_dinilai',
        ]);

        return redirect()->route('penilaian-wawancara.index')->with('success', 'Penilaian berhasil diperbarui.');
    }

    public function destroy(PenilaianWawancara $penilaianWawancara)
    {
        $penilaianWawancara->delete();
        return redirect()->route('penilaian-wawancara.index')->with('success', 'Penilaian berhasil dihapus.');
    }

    public function show($id)
    {
        $penilaian = PenilaianWawancara::with(['pendaftaran.user', 'jadwal'])->findOrFail($id);
        return view('penilaian-wawancara.show', compact('penilaian'));
    }
 
}
