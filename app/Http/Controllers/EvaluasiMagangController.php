<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiMagangModel;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluasiMagangController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $pendaftar = Pendaftaran::where('dinas_diterima_id', $user->dinas_id)
    ->whereIn('status_pendaftaran', ['lulus_wawancara'])
    ->with('user') 
    ->get();

        $penilaian = EvaluasiMagangModel::get();

        return view('penilaian.index', compact('pendaftar', 'penilaian'));
    }

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

    // Tentukan hasil evaluasi dan status pendaftaran
    $hasilEvaluasi = $total >= 70 ? 'Lulus' : 'Tidak Lulus';
    $statusPendaftaran = $total >= 70 ? 'lulus_magang' : 'tidak_lulus_magang';

    // Ambil data pendaftaran terkait
    $pendaftaran = Pendaftaran::findOrFail($request->pendaftaran_id);

    if ($request->penilaian_id) {
        // UPDATE penilaian
        $evaluasi = EvaluasiMagangModel::findOrFail($request->penilaian_id);
        $evaluasi->update([
            'pendaftaran_id' => $request->pendaftaran_id,
            'penilai_id' => $user->id,
            'nilai_kedisiplinan' => $request->nilai_kedisiplinan,
            'nilai_kerjasama' => $request->nilai_kerjasama,
            'nilai_inisiatif' => $request->nilai_inisiatif,
            'nilai_hasil_kerja' => $request->nilai_hasil_kerja,
            'nilai_total' => $total,
            'hasil_evaluasi' => $hasilEvaluasi,
        ]);

        $message = 'Penilaian berhasil diperbarui!';
    } else {
        // CREATE penilaian baru
        EvaluasiMagangModel::create([
            'pendaftaran_id' => $request->pendaftaran_id,
            'penilai_id' => $user->id,
            'nilai_kedisiplinan' => $request->nilai_kedisiplinan,
            'nilai_kerjasama' => $request->nilai_kerjasama,
            'nilai_inisiatif' => $request->nilai_inisiatif,
            'nilai_hasil_kerja' => $request->nilai_hasil_kerja,
            'nilai_total' => $total,
            'hasil_evaluasi' => $hasilEvaluasi,
        ]);

        $message = 'Penilaian berhasil disimpan!';
    }

    // Update status pendaftaran berdasarkan hasil evaluasi
    $pendaftaran->update([
        'status_pendaftaran' => $statusPendaftaran,
    ]);

    return redirect()->route('penilaian.index')->with('success', $message);
}

    public function destroy($id)
    {
        $evaluasi = EvaluasiMagangModel::findOrFail($id);
        $evaluasi->delete();

        return redirect()->route('penilaian.index')->with('success', 'Penilaian berhasil dihapus!');
    }
}