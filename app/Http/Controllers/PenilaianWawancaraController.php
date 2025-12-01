<?php

namespace App\Http\Controllers;

use App\Models\PenilaianWawancara;
use App\Models\Pendaftaran;
use App\Models\JadwalSeleksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaianWawancaraController extends Controller
{
    public function index()
    {
        $data = PenilaianWawancara::with('pendaftaran.user', 'jadwal')->get();

        // Ambil KKM terakhir untuk semua penilaian
        $kkm = DB::table('penilaian_wawancara')->max('kkm'); // atau ambil sesuai logika kamu

        return view('penilaian-wawancara.index', compact('data', 'kkm'));
    }

   public function create()
    {
        // Ambil semua jadwal seleksi beserta peserta yang terdaftar
        $jadwalseleksi = JadwalSeleksi::with('pendaftaran.user')->get();

        // Ambil daftar pendaftaran_id yang sudah dinilai
        $penilaianExist = PenilaianWawancara::pluck('pendaftaran_id')->toArray();

        // Di blade nanti kita bisa pakai $jadwalseleksi untuk populate peserta sesuai pewawancara
        return view('penilaian-wawancara.create', compact('jadwalseleksi', 'penilaianExist'));
    }

   public function store(Request $request)
    {
        $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftaran,id',
            'jadwal_seleksi_id' => 'required|exists:jadwal_seleksi,id',
            'nilai_komunikasi' => 'nullable|numeric|min:0|max:100',
            'nilai_motivasi' => 'nullable|numeric|min:0|max:100',
            'nilai_kemampuan' => 'nullable|numeric|min:0|max:100',
            'kkm' => 'nullable|numeric|min:0|max:100',
        ]);

        // Cegah duplikasi penilaian untuk peserta yang sama
        if (PenilaianWawancara::where('pendaftaran_id', $request->pendaftaran_id)->exists()) {
            return redirect()->back()->with('error', 'Peserta ini sudah memiliki penilaian.');
        }

        // Ambil nilai
        $komunikasi = $request->nilai_komunikasi;
        $motivasi = $request->nilai_motivasi;
        $kemampuan = $request->nilai_kemampuan;

        // Hitung total & rata-rata
        $nilai_total = ($komunikasi ?? 0) + ($motivasi ?? 0) + ($kemampuan ?? 0);
        $nilai_rata_rata = $nilai_total / 3;

        // Tentukan status otomatis
        $status = ($komunikasi || $motivasi || $kemampuan) ? 'sudah_dinilai' : 'belum_dinilai';

        PenilaianWawancara::create([
            'pendaftaran_id' => $request->pendaftaran_id,
            'penilai_id' => 1, // ganti dengan auth()->user()->id jika login
            'jadwal_seleksi_id' => $request->jadwal_seleksi_id,
            'nilai_komunikasi' => $komunikasi,
            'nilai_motivasi' => $motivasi,
            'nilai_kemampuan' => $kemampuan,
            'nilai_total' => $nilai_total,
            'nilai_rata_rata' => round($nilai_rata_rata, 2),
            'kkm' => $request->kkm, // simpan KKM di DB
            'status' => $status,
        ]);

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
            'kkm' => 'nullable|numeric|min:0|max:100',
        ]);

        // Ambil nilai
        $komunikasi = $request->nilai_komunikasi;
        $motivasi = $request->nilai_motivasi;
        $kemampuan = $request->nilai_kemampuan;

        // Hitung total & rata-rata
        $nilai_total = ($komunikasi ?? 0) + ($motivasi ?? 0) + ($kemampuan ?? 0);
        $nilai_rata_rata = $nilai_total / 3;

        // Status otomatis
        $status = ($komunikasi || $motivasi || $kemampuan) ? 'sudah_dinilai' : 'belum_dinilai';

        $penilaianWawancara->update([
            'pendaftaran_id' => $request->pendaftaran_id,
            'penilai_id' => 1, // nanti ganti auth()->user()->id
            'jadwal_seleksi_id' => $request->jadwal_seleksi_id,
            'nilai_komunikasi' => $komunikasi,
            'nilai_motivasi' => $motivasi,
            'nilai_kemampuan' => $kemampuan,
            'nilai_total' => $nilai_total,
            'nilai_rata_rata' => round($nilai_rata_rata, 2),
            'kkm' => $request->kkm ?? $penilaianWawancara->kkm, // tetap simpan KKM lama kalau tidak diganti
            'status' => $status,
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
        $penilaian = PenilaianWawancara::with([
        'pendaftaran.user', 
        'pendaftaran.dinasPilihan1', 
        'pendaftaran.dinasPilihan2', 
        'jadwal'
        ])->findOrFail($id);

        return view('penilaian-wawancara.show', compact('penilaian'));
    }

   public function updateStatus(Request $request)
    {
        $kkm = $request->kkm;

        if (!$kkm || $kkm <= 0) {
            return response()->json(['message' => 'KKM tidak valid!'], 400);
        }

        $penilaian = PenilaianWawancara::with('pendaftaran')->get();

        foreach ($penilaian as $item) {
            // Update nilai KKM di tabel penilaian_wawancara
            $item->update(['kkm' => $kkm]);

            $nilaiAkhir = $item->nilai_rata_rata;

            if (!is_null($nilaiAkhir)) {
                if ($nilaiAkhir >= $kkm) {
                    Pendaftaran::where('id', $item->pendaftaran_id)
                        ->update(['status_pendaftaran' => 'lulus_wawancara']);
                } else {
                    Pendaftaran::where('id', $item->pendaftaran_id)
                        ->update(['status_pendaftaran' => 'tidak_lulus_wawancara']);
                }   
            }
        }

        return response()->json(['message' => 'Status pendaftaran dan KKM berhasil diperbarui!']);
    }
}
