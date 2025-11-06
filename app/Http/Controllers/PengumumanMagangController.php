<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiMagang;
use App\Models\TemplateSertifikat;
use Barryvdh\DomPDF\Facade\Pdf; // BARIS INI SUDAH BENAR
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengumumanMagangController extends Controller
{
    public function index()
    {
        $evaluasiList = EvaluasiMagang::with('pendaftaran.user')->latest()->get();
        $templates = TemplateSertifikat::orderBy('created_at', 'desc')->get();

        return view('pengumuman-magang.index', compact('evaluasiList', 'templates'));
    }

    public function store(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nomor_sertifikat' => 'required|string|max:255',
            'template_id' => 'required|exists:template_sertifikat,id',
        ]);

        // Ambil data evaluasi & mahasiswa
        $evaluasi = EvaluasiMagang::with('pendaftaran.user')->findOrFail($id);
        $pendaftaran = $evaluasi->pendaftaran;
        $idPendaftaran = $pendaftaran->id;
        $namaMahasiswa = str_replace(' ', '_', strtolower($pendaftaran->user->nama_lengkap));

        // 💡 Gunakan langsung nilai total tanpa dibagi 4
        $rataRata = $evaluasi->nilai_total;

        // Tentukan status kelulusan
        $hasilEvaluasi = $rataRata >= 75 ? 'Lulus' : 'Tidak Lulus';
        $warnaStatus = $hasilEvaluasi === 'Lulus' ? 'text-green' : 'text-red';

        // Update status & hasil evaluasi
        $evaluasi->update(['hasil_evaluasi' => $hasilEvaluasi]);
        $pendaftaran->update([
            'status' => $hasilEvaluasi === 'Lulus' ? 'lulus_magang' : 'tidak_lulus',
        ]);

        // Ambil template
        $template = TemplateSertifikat::findOrFail($request->template_id);

        // Path absolut ke template HTML
        $templatePath = storage_path('app/public/' . ltrim($template->file_template, '/'));

        if (!file_exists($templatePath)) {
            return back()->with('error', "Template tidak ditemukan di: {$templatePath}");
        }

        // Baca isi HTML
        $templateContent = file_get_contents($templatePath);

        // 🔧 Path gambar tanda tangan
        $pathGubernur = public_path('images/ttd_gubernur.jpeg');
        $pathKetua = public_path('images/ttd_ketua.jpeg');

        $ttdGubernur = file_exists($pathGubernur)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathGubernur))
            : '';
        $ttdKetua = file_exists($pathKetua)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($pathKetua))
            : '';

        // Replace placeholder di template
        $replacedContent = str_replace(
            [
                '{{nama_mahasiswa}}',
                '{{nilai_total}}',
                '{{nomor_sertifikat}}',
                '{{hasil_evaluasi}}',
                '{{tanggal}}',
                '{{warna_status}}',
                '{{ttd_gubernur}}',
                '{{ttd_ketua}}',
            ],
            [
                $pendaftaran->user->nama_lengkap,
                number_format($rataRata, 2),
                $request->nomor_sertifikat . $idPendaftaran,
                $hasilEvaluasi,
                now()->translatedFormat('d F Y'),
                $warnaStatus,
                $ttdGubernur,
                $ttdKetua,
            ],
            $templateContent
        );

        // Generate PDF
        // $pdf = Pdf::loadHTML($replacedContent)
        //     ->setPaper('a4', 'landscape')
        //     ->setOptions([
        //         'isHtml5ParserEnabled' => true,
        //         'isRemoteEnabled' => true,
        //     ]);

        // $fileName = 'Sertifikat_' . $namaMahasiswa . '.pdf';
        // $path = 'sertifikat/' . $fileName;
        // Storage::disk('public')->put($path, $pdf->output());

        // // Update database
        // $evaluasi->update([
        //     'nomor_sertifikat' => $request->nomor_sertifikat . $idPendaftaran,
        //     'file_sertifikat' => $path,
        //     'template_sertifikat_id' => $template->id,
        // ]);

        return redirect()->route('pengumuman.kelulusan')
            ->with('success', 'Sertifikat PDF berhasil dibuat dan disimpan!');
    }
}