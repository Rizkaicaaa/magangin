<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiMagang;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluasiMagangController extends Controller
{
    // INDEX: tampilkan daftar penilaian + peserta yang boleh dinilai
    public function index()
    {
        $user = Auth::user();

        // Peserta yang boleh dinilai user ini (dinas sama & sudah lulus wawancara)
        $pendaftar = Pendaftaran::where('dinas_diterima_id', $user->dinas_id)
            ->where('status_pendaftaran', 'lulus_wawancara')
            ->with('user') 
            ->get();

        // Semua penilaian peserta yang termasuk di atas
        $penilaian = EvaluasiMagang::whereIn('pendaftaran_id', $pendaftar->pluck('id'))
            ->with('pendaftaran')
            ->get();

        return view('penilaian.index', compact('pendaftar', 'penilaian'));
    }

    // SIMPAN atau UPDATE
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftaran,id',
            'nilai_kedisiplinan' => 'required|numeric|min:0|max:100',
            'nilai_kerjasama' => 'required|numeric|min:0|max:100',
            'nilai_inisiatif' => 'required|numeric|min:0|max:100',
            'nilai_hasil_kerja' => 'required|numeric|min:0|max:100',
        ]);

        $user = Auth::user();

        $total = (
            $request->nilai_kedisiplinan +
            $request->nilai_kerjasama +
            $request->nilai_inisiatif +
            $request->nilai_hasil_kerja
        ) / 4;

        if ($request->penilaian_id) {
            // UPDATE
            $evaluasi = EvaluasiMagang::findOrFail($request->penilaian_id);
            $evaluasi->update([
                'pendaftaran_id' => $request->pendaftaran_id,
                'penilai_id' => $user->id,
                'nilai_kedisiplinan' => $request->nilai_kedisiplinan,
                'nilai_kerjasama' => $request->nilai_kerjasama,
                'nilai_inisiatif' => $request->nilai_inisiatif,
                'nilai_hasil_kerja' => $request->nilai_hasil_kerja,
                'nilai_total' => $total,
                'hasil_evaluasi' => $total >= 70 ? 'Lulus' : 'Tidak Lulus',
            ]);

            $message = 'Penilaian berhasil diperbarui!';
        } else {
            // SIMPAN BARU
            EvaluasiMagang::updateOrCreate(
                ['pendaftaran_id' => $request->pendaftaran_id],
                [
                    'penilai_id' => $user->id,
                    'nilai_kedisiplinan' => $request->nilai_kedisiplinan,
                    'nilai_kerjasama' => $request->nilai_kerjasama,
                    'nilai_inisiatif' => $request->nilai_inisiatif,
                    'nilai_hasil_kerja' => $request->nilai_hasil_kerja,
                    'nilai_total' => $total,
                    'hasil_evaluasi' => $total >= 70 ? 'Lulus' : 'Tidak Lulus',
                ]
            );

            $message = 'Penilaian berhasil disimpan!';
        }

        return redirect()->route('penilaian.index')->with('success', $message);
    }

    // HAPUS
    public function destroy($id)
    {
        $evaluasi = EvaluasiMagang::findOrFail($id);
        $evaluasi->delete();

        return redirect()->route('penilaian.index')->with('success', 'Penilaian berhasil dihapus!');
    }
}
