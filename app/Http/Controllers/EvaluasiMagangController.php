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

    if (!$user) {
        abort(403);
    }

    $pendaftar = Pendaftaran::where('dinas_diterima_id', $user->dinas_id)
        ->where('status_pendaftaran', 'lulus_wawancara')
        ->with('user')
        ->get();

    $penilaian = EvaluasiMagangModel::with('pendaftaran.user')->get();

    return view('penilaian.index', compact('pendaftar', 'penilaian'));
}

    public function storeOrUpdate(Request $request, $id = null)
    {
        $validated = $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftaran,id',
            'nilai_kedisiplinan' => 'required|numeric|min:0|max:100',
            'nilai_kerjasama' => 'required|numeric|min:0|max:100',
            'nilai_inisiatif' => 'required|numeric|min:0|max:100',
            'nilai_hasil_kerja' => 'required|numeric|min:0|max:100',
        ]);

        $user = Auth::user();
        $total = (
            $validated['nilai_kedisiplinan'] +
            $validated['nilai_kerjasama'] +
            $validated['nilai_inisiatif'] +
            $validated['nilai_hasil_kerja']
        ) / 4;

        $hasilEvaluasi = $total >= 70 ? 'lulus' : 'tidak_lulus';
        $statusPendaftaran = $total >= 70 ? 'lulus_magang' : 'tidak_lulus_magang';
        
        $pendaftaran = Pendaftaran::findOrFail($validated['pendaftaran_id']);

        // Data yang akan disimpan
        $data = [
            'pendaftaran_id' => $validated['pendaftaran_id'],
            'penilai_id' => $user->id,
            'nilai_kedisiplinan' => $validated['nilai_kedisiplinan'],
            'nilai_kerjasama' => $validated['nilai_kerjasama'],
            'nilai_inisiatif' => $validated['nilai_inisiatif'],
            'nilai_hasil_kerja' => $validated['nilai_hasil_kerja'],
            'nilai_total' => $total,
            'hasil_evaluasi' => $hasilEvaluasi,
            'template_sertifikat_id' => null, // ✅ SET NULL (karena nullable di migration)
            'nomor_sertifikat' => null,
            'file_sertifikat' => null,
        ];

        if ($id || $request->penilaian_id) {
            // UPDATE
            $evaluasiId = $id ?? $request->penilaian_id;
            $evaluasi = EvaluasiMagangModel::findOrFail($evaluasiId);
            $evaluasi->update($data);
            $message = 'Penilaian berhasil diperbarui!';
        } else {
            // CREATE - cek duplicate dulu
            $existing = EvaluasiMagangModel::where('pendaftaran_id', $validated['pendaftaran_id'])->first();
            
            if ($existing) {
                // Kalau sudah ada, jangan create lagi
                return redirect()->route('penilaian.index')
                    ->with('info', 'Penilaian untuk pendaftar ini sudah ada.');
            }
            
            EvaluasiMagangModel::create($data);
            $message = 'Penilaian berhasil disimpan!';
        }

        // Update status pendaftaran
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