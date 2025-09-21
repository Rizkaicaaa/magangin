<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pendaftaran;
use App\Models\Dinas;
use App\Models\InfoOr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendaftarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua user dengan role mahasiswa yang sudah mendaftar magang
        $pendaftars = User::with([
            'pendaftaran.infoOr',
            'pendaftaran.dinasPilihan1',
            'pendaftaran.dinasPilihan2',
            'pendaftaran.dinasDiterima'
        ])
        ->where('role', 'mahasiswa')
        ->whereHas('pendaftaran')
        ->orderBy('created_at', 'desc')
        ->get();

        // Ambil semua dinas untuk dropdown
        $allDinas = Dinas::all();

        // Ambil semua periode dari Info OR
        $allPeriode = InfoOr::select('id', 'periode')
                           ->distinct()
                           ->orderBy('periode', 'desc')
                           ->get();

        // Debug untuk melihat data (uncomment untuk debug)
        // dd('Total Dinas:', $allDinas->count(), 'First Pendaftar:', $pendaftars->first());
        
        // Debug specific pendaftaran
        // if ($pendaftars->count() > 0) {
        //     $firstPendaftaran = $pendaftars->first()->pendaftaran->first();
        //     dd([
        //         'pilihan_dinas_1' => $firstPendaftaran->pilihan_dinas_1,
        //         'dinasPilihan1' => $firstPendaftaran->dinasPilihan1,
        //         'pilihan_dinas_2' => $firstPendaftaran->pilihan_dinas_2,
        //         'dinasPilihan2' => $firstPendaftaran->dinasPilihan2
        //     ]);
        // }

        return view('pendaftar.index', compact('pendaftars', 'allDinas', 'allPeriode'));
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $pendaftar = User::with([
            'pendaftaran.infoOr',
            'pendaftaran.dinasPilihan1',
            'pendaftaran.dinasPilihan2',
            'pendaftaran.dinasDiterima',
            'pendaftaran.jadwalSeleksi'
        ])
        ->where('role', 'mahasiswa')
        ->findOrFail($id);

        return view('pendaftar.show', compact('pendaftar'));
    }

    /**
     * Update status pendaftaran
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:terdaftar,lulus_wawancara,tidak_lulus_wawancara,lulus_magang,tidak_lulus_magang'
        ]);

        $pendaftaran = Pendaftaran::where('user_id', $id)->first();
        
        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        $pendaftaran->update([
            'status_pendaftaran' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status pendaftaran berhasil diupdate.');
    }

    /**
     * Set dinas yang diterima (hanya untuk yang lulus wawancara)
     */
    public function setDinasDiterima(Request $request, $id)
    {
        $request->validate([
            'dinas_diterima_id' => 'required|exists:dinas,id'
        ]);

        $pendaftaran = Pendaftaran::where('user_id', $id)->first();
        
        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        // Cek apakah status lulus wawancara
        if ($pendaftaran->status_pendaftaran !== 'lulus_wawancara') {
            return redirect()->back()->with('error', 'Hanya pendaftar yang lulus wawancara yang dapat ditetapkan dinas.');
        }

        $pendaftaran->update([
            'dinas_diterima_id' => $request->dinas_diterima_id
        ]);

        return redirect()->back()->with('success', 'Dinas penerima berhasil ditetapkan.');
    }

    /**
     * Download CV file
     */
    public function downloadCV($id)
    {
        $pendaftaran = Pendaftaran::where('user_id', $id)->first();
        
        if (!$pendaftaran || !$pendaftaran->file_cv) {
            return redirect()->back()->with('error', 'File CV tidak ditemukan.');
        }

        $filePath = storage_path('app/public/' . $pendaftaran->file_cv);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File CV tidak ditemukan di server.');
        }

        return response()->download($filePath);
    }

    /**
     * Download Transkrip file
     */
    public function downloadTranskrip($id)
    {
        $pendaftaran = Pendaftaran::where('user_id', $id)->first();
        
        if (!$pendaftaran || !$pendaftaran->file_transkrip) {
            return redirect()->back()->with('error', 'File transkrip tidak ditemukan.');
        }

        $filePath = storage_path('app/public/' . $pendaftaran->file_transkrip);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File transkrip tidak ditemukan di server.');
        }

        return response()->download($filePath);
    }
}