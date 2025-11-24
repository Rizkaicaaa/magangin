<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InfoOr;
use App\Models\Pendaftaran;
use App\Models\JadwalKegiatan;
use App\Models\Dinas;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard utama yang menyesuaikan tampilan berdasarkan role user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Data yang akan dikirim ke view
        $data = [
            'user' => $user,
            'selectedInfoOr' => 'all',
            'selectedInfoOrData' => null,
            'allInfoOr' => collect(),
            'showFilter' => false,
        ];
        
        // Role-based data preparation
        switch ($user->role) {
            case 'superadmin':
                $data = array_merge($data, $this->getSuperadminData($request));
                break;
                
            case 'admin':
            case 'admin_dinas':
                $data = array_merge($data, $this->getAdminData($request, $user));
                break;
                
            case 'mahasiswa':
            case 'user':
            default:
                $data = array_merge($data, $this->getMahasiswaData($user));
                break;
        }
        
        return view('dashboard', $data);
    }
    
    /**
     * Data untuk Superadmin - bisa lihat semua data dan filter per periode
     */
    private function getSuperadminData(Request $request)
    {
        $selectedInfoOr = $request->get('info_or_id', 'all');
        
        // Get all info_or for dropdown
        $allInfoOr = InfoOr::select('id', 'judul', 'periode')
            ->orderBy('created_at', 'desc')
            ->get();
        
  if ($selectedInfoOr === 'all') {
    // Show all data
    $data = [
        'totalPendaftar' => Pendaftaran::count(),
        'totalDinas' => Dinas::count(), // ✅ perbaikan
        'totalInfo' => InfoOr::count(),
        'totalKegiatan' => JadwalKegiatan::count(),
        'pendaftarTerbaru' => Pendaftaran::with(['user', 'infoOr', 'dinasPilihan1', 'dinasPilihan2', 'dinasDiterima'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(),
        'kegiatanTerdekat' => JadwalKegiatan::with('infoOr')
            ->where('tanggal_kegiatan', '>=', now())
            ->orderBy('tanggal_kegiatan', 'asc')
            ->limit(8)
            ->get(),
        'additionalStats' => []
    ];
} else {
    $data = [
        'totalPendaftar' => Pendaftaran::where('info_or_id', $selectedInfoOr)->count(),
        'totalDinas' => Dinas::count(), // ✅ perbaikan di sini
        'totalInfo' => 1,
        'totalKegiatan' => JadwalKegiatan::where('info_or_id', $selectedInfoOr)->count(),
        'pendaftarTerbaru' => Pendaftaran::with(['user', 'infoOr', 'dinasPilihan1', 'dinasDiterima'])
            ->where('info_or_id', $selectedInfoOr)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(),
        'kegiatanTerdekat' => JadwalKegiatan::with('infoOr')
            ->where('info_or_id', $selectedInfoOr)
            ->where('tanggal_kegiatan', '>=', now())
            ->orderBy('tanggal_kegiatan', 'asc')
            ->limit(8)
            ->get(),
        'additionalStats' => [
            'terdaftar' => Pendaftaran::where('info_or_id', $selectedInfoOr)
                    ->where('status_pendaftaran', 'terdaftar')
                    ->count(),

                'pendaftar_lulus_wawancara' => Pendaftaran::where('info_or_id', $selectedInfoOr)
                    ->where('status_pendaftaran', 'lulus_wawancara')
                    ->count(),

                'pendaftar_ditolak' => Pendaftaran::where('info_or_id', $selectedInfoOr)
                    ->where('status_pendaftaran', 'tidak_lulus_wawancara')
                    ->count(),

                'pendaftar_lulus_magang' => Pendaftaran::where('info_or_id', $selectedInfoOr)
                    ->where('status_pendaftaran', 'lulus_magang')
                    ->count(),

                'pendaftar_tidak_lulus_magang' => Pendaftaran::where('info_or_id', $selectedInfoOr)
                    ->where('status_pendaftaran', 'tidak_lulus_magang')
                    ->count(),
         ]
    ];
}

        
        return array_merge($data, [
            'selectedInfoOr' => $selectedInfoOr,
            'selectedInfoOrData' => $selectedInfoOr !== 'all' ? InfoOr::find($selectedInfoOr) : null,
            'allInfoOr' => $allInfoOr,
            'showFilter' => true,
            'canManage' => true
        ]);
    }
    
    /**
     * Data untuk Admin : hanya bisa lihat data dinas mereka dan filter per periode
     */
private function getAdminData(Request $request, $user)
    {
        $selectedInfoOr = $request->get('info_or_id', 'all');
        if (!$user->dinas_id) {
            return [
                'totalPendaftar' => 0,
                'totalDinas' => 0,
                'totalInfo' => 0,
                'totalKegiatan' => 0,
                'pendaftarTerbaru' => collect(),
                'kegiatanTerdekat' => collect(),
                'additionalStats' => [],
                'selectedInfoOr' => 'all',
                'selectedInfoOrData' => null,
                'allInfoOr' => collect(),
                'showFilter' => false
            ];
        }

        $dinasId = $user->dinas_id;

        $allInfoOr = InfoOr::whereHas('pendaftaran', function($query) use ($dinasId) {
            $query->where('pilihan_dinas_1', $dinasId)
                  ->orWhere('pilihan_dinas_2', $dinasId)
                  ->orWhere('dinas_diterima_id', $dinasId);
        })->orderBy('created_at', 'desc')->get();

        $baseQuery = Pendaftaran::with(['user', 'infoOr', 'dinasPilihan1', 'dinasPilihan2', 'dinasDiterima'])
                        ->where(function($q) use ($dinasId) {
                            $q->where('pilihan_dinas_1', $dinasId)
                              ->orWhere('pilihan_dinas_2', $dinasId)
                              ->orWhere('dinas_diterima_id', $dinasId);
                        });

        if ($selectedInfoOr !== 'all') {
            $baseQuery->where('info_or_id', $selectedInfoOr);
        }

        $pendaftarTerbaru = $baseQuery->orderBy('created_at', 'desc')->limit(10)->get();

        $additionalStats = [
        'terdaftar' => (clone $baseQuery)->where('status_pendaftaran', 'terdaftar')->count(),
        'pendaftar_lulus_wawancara' => (clone $baseQuery)->where('status_pendaftaran', 'lulus_wawancara')->count(),
        'pendaftar_ditolak' => (clone $baseQuery)->where('status_pendaftaran', 'tidak_lulus_wawancara')->count(),
        'pendaftar_lulus_magang' => (clone $baseQuery)->where('status_pendaftaran', 'lulus_magang')->count(),
        'pendaftar_tidak_lulus_magang' => (clone $baseQuery)->where('status_pendaftaran', 'tidak_lulus_magang')->count(),
        ];

        $totalPendaftar = $baseQuery->count();
        $totalKegiatan = JadwalKegiatan::whereIn('info_or_id', $allInfoOr->pluck('id'))->count();

        return [
            'totalPendaftar' => $totalPendaftar,
            'totalDinas' => 1,
            'totalInfo' => $allInfoOr->count(),
            'totalKegiatan' => $totalKegiatan,
            'pendaftarTerbaru' => $pendaftarTerbaru,
            'kegiatanTerdekat' => JadwalKegiatan::whereIn('info_or_id', $allInfoOr->pluck('id'))
                                    ->where('tanggal_kegiatan', '>=', now())
                                    ->orderBy('tanggal_kegiatan')->limit(8)->get(),
            'additionalStats' => $additionalStats,
            'selectedInfoOr' => $selectedInfoOr,
            'selectedInfoOrData' => $selectedInfoOr !== 'all' ? InfoOr::find($selectedInfoOr) : null,
            'allInfoOr' => $allInfoOr,
            'showFilter' => $allInfoOr->count() > 0
        ];
    }

    private function getMahasiswaData($user)
    {
        $pendaftaranUser = Pendaftaran::with(['infoOr', 'dinasPilihan1', 'dinasPilihan2', 'dinasDiterima'])
                            ->where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->get();

        $acceptedInfoOrIds = $pendaftaranUser
                                ->where('status_pendaftaran', 'lulus_wawancara')
                                ->pluck('info_or_id')
                                ->toArray();

        $kegiatanUser = JadwalKegiatan::with('infoOr')
                            ->whereIn('info_or_id', $acceptedInfoOrIds)
                            ->where('tanggal_kegiatan', '>=', now())
                            ->orderBy('tanggal_kegiatan', 'asc')
                            ->limit(8)
                            ->get();

        return [
            'totalKegiatan' => $kegiatanUser->count(),
            'pendaftaranUser' => $pendaftaranUser,
            'kegiatanTerdekat' => $kegiatanUser,
            'selectedInfoOr' => 'all',
            'selectedInfoOrData' => null,
            'allInfoOr' => collect(),
            'showFilter' => false
        ];
    }
}