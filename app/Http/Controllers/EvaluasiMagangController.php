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

        // Ambil pendaftar yang lulus wawancara dari dinas user
        $pendaftar = Pendaftaran::where('dinas_diterima_id', $user->dinas_id)
            ->whereIn('status_pendaftaran', ['lulus_wawancara'])
            ->with('user') 
            ->get();

        // Ambil penilaian dengan relationship
        $penilaian = EvaluasiMagangModel::with(['pendaftaran.user'])->get();

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

        $hasilEvaluasi = $total >= 70 ? 'Lulus' : 'Tidak Lulus';
        $statusPendaftaran = $total >= 70 ? 'lulus_magang' : 'tidak_lulus_magang';
        
        $pendaftaran = Pendaftaran::findOrFail($request->pendaftaran_id);

        // ✅ FIX: Ambil template pertama atau buat dummy jika tidak ada
$defaultTemplate = \App\Models\TemplateSertifikatModel::first();

// Jika tidak ada template sama sekali, buat dummy template
if (!$defaultTemplate) {
    $defaultTemplate = \App\Models\TemplateSertifikatModel::create([
        'nama_template' => 'Default Template',
        'file_template' => 'default.docx',
        'created_by' => $user->id
    ]);
}

// Data yang akan disimpan
$data = [
    'pendaftaran_id' => $request->pendaftaran_id,
    'penilai_id' => $user->id,
    'nilai_kedisiplinan' => $request->nilai_kedisiplinan,
    'nilai_kerjasama' => $request->nilai_kerjasama,
    'nilai_inisiatif' => $request->nilai_inisiatif,
    'nilai_hasil_kerja' => $request->nilai_hasil_kerja,
    'nilai_total' => $total,
    'hasil_evaluasi' => $hasilEvaluasi,
    'template_sertifikat_id' => $defaultTemplate->id, // ✅ ALWAYS SET ID, BUKAN NULL
    'nomor_sertifikat' => null,
    'file_sertifikat' => null,
];

        if ($request->penilaian_id) {
            // UPDATE penilaian
            $evaluasi = EvaluasiMagangModel::findOrFail($request->penilaian_id);
            $evaluasi->update($data);
            $message = 'Penilaian berhasil diperbarui!';
        } else {
            // CREATE penilaian baru
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