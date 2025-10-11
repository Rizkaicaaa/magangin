<?php

namespace App\Http\Controllers;

use App\Models\InfoOr;
use App\Models\JadwalKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

class JadwalKegiatanController extends Controller
{
    /**
     * Halaman utama jadwal kegiatan
     */
public function index()
{
    try {
        $user = Auth::user();
        $userRole = $user->role;
        
        // Ambil semua periode untuk ditampilkan di select
        $periodes = InfoOr::select('id', 'periode', 'status', 'tanggal_buka', 'tanggal_tutup')
            ->orderBy('id', 'desc') // Urutkan berdasarkan ID terbaru
            ->get();
        
        // Inisialisasi selectedPeriode
        $selectedPeriode = null;
        
        // Untuk mahasiswa, ambil periode berdasarkan pendaftaran mereka
        if ($userRole === 'mahasiswa') {
            // Ambil info_or_id dari tabel pendaftaran mahasiswa
            $pendaftaran = \App\Models\Pendaftaran::where('user_id', $user->id)
                ->latest()
                ->first();
            
            $selectedPeriode = $pendaftaran->info_or_id;
        } else {
            // Untuk admin/superadmin, set default ke periode terbaru
            $periodeTerbaru = $periodes->first(); // ID terbesar = data terbaru
            $selectedPeriode = $periodeTerbaru ? $periodeTerbaru->id : null;
        }
        
        return view('kegiatan.index', compact('periodes', 'userRole', 'selectedPeriode'));
    } catch (Exception $e) {
        Log::error('Error loading jadwal kegiatan page: ' . $e->getMessage());
        
        return back()->with('error', 'Gagal memuat halaman jadwal kegiatan');
    }
}

    /**
     * Ambil daftar kegiatan berdasarkan periode
     */
    public function getByPeriode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'periode_id' => 'required|integer|exists:info_or,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter periode_id tidak valid',
                    'errors' => $validator->errors()
                ], 400);
            }

            $periodeId = $request->get('periode_id');

            // Ambil data periode
            $periode = InfoOr::findOrFail($periodeId);

            // Ambil data kegiatan
            $kegiatan = JadwalKegiatan::where('info_or_id', $periodeId)
                ->orderBy('tanggal_kegiatan', 'asc')
                ->orderBy('waktu_mulai', 'asc')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nama_kegiatan' => $item->nama_kegiatan,
                        'deskripsi_kegiatan' => $item->deskripsi_kegiatan,
                        'tanggal_kegiatan' => $item->tanggal_kegiatan ? $item->tanggal_kegiatan->format('Y-m-d') : null,
                        'tanggal_kegiatan_formatted' => $item->tanggal_kegiatan ? $item->tanggal_kegiatan->format('d F Y') : null,
                        'waktu_mulai' => $item->waktu_mulai ? $item->waktu_mulai->format('H:i') : null,
                        'waktu_selesai' => $item->waktu_selesai ? $item->waktu_selesai->format('H:i') : null,
                        'tempat' => $item->tempat,
                        'info_or_id' => $item->info_or_id,
                        'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $item->updated_at->format('Y-m-d H:i:s')
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $kegiatan,
                'periode' => [
                    'id' => $periode->id,
                    'periode' => $periode->periode,
                    'status' => $periode->status,
                    'tanggal_buka' => $periode->tanggal_buka,
                    'tanggal_tutup' => $periode->tanggal_tutup
                ],
                'total' => $kegiatan->count()
            ]);
        } catch (Exception $e) {
            Log::error('Error mengambil jadwal kegiatan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data jadwal kegiatan',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display the specified resource
     */
    public function show($id)
    {
        try {
            $kegiatan = JadwalKegiatan::with('infoOr')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => [
                    'id' => $kegiatan->id,
                    'nama_kegiatan' => $kegiatan->nama_kegiatan,
                    'deskripsi_kegiatan' => $kegiatan->deskripsi_kegiatan,
                    'tanggal_kegiatan' => $kegiatan->tanggal_kegiatan->format('Y-m-d'),
                    'tanggal_kegiatan_formatted' => $kegiatan->tanggal_kegiatan->format('d F Y'),
                    'waktu_mulai' => $kegiatan->waktu_mulai->format('H:i'),
                    'waktu_selesai' => $kegiatan->waktu_selesai ? $kegiatan->waktu_selesai->format('H:i') : null,
                    'tempat' => $kegiatan->tempat,
                    'info_or_id' => $kegiatan->info_or_id,
                    'periode' => $kegiatan->infoOr ? [
                        'id' => $kegiatan->infoOr->id,
                        'periode' => $kegiatan->infoOr->periode,
                        'status' => $kegiatan->infoOr->status
                    ] : null,
                    'created_at' => $kegiatan->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $kegiatan->updated_at->format('Y-m-d H:i:s')
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Error mengambil detail jadwal kegiatan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Jadwal kegiatan tidak ditemukan',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 404);
        }
    }

    /**
     * Store a newly created resource
     */
    public function store(Request $request)
    {
        // Validasi role superadmin
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hanya superadmin yang dapat menambah kegiatan.'
            ], 403);
        }

        try {
            $validator = Validator::make($request->all(), [
                'info_or_id' => 'required|integer|exists:info_or,id',
                'nama_kegiatan' => 'required|string|max:255',
                'deskripsi_kegiatan' => 'nullable|string|max:1000',
                'tanggal_kegiatan' => 'required|date|after_or_equal:today|before:2100-01-01',
                'waktu_mulai' => 'required|date_format:H:i',
                'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
                'tempat' => 'nullable|string|max:255',
            ], [
                'info_or_id.required' => 'Periode harus dipilih',
                'info_or_id.exists' => 'Periode tidak valid',
                'nama_kegiatan.required' => 'Nama kegiatan harus diisi',
                'nama_kegiatan.max' => 'Nama kegiatan maksimal 255 karakter',
                'deskripsi_kegiatan.max' => 'Deskripsi maksimal 1000 karakter',
                'tanggal_kegiatan.required' => 'Tanggal kegiatan harus diisi',
                'tanggal_kegiatan.date' => 'Format tanggal tidak valid',
                'tanggal_kegiatan.after_or_equal' => 'Tanggal kegiatan yang anda pilih sudah lewat ',
                'tanggal_kegiatan.before' => 'Tanggal kegiatan tidak valid, tahun maksimal 2099',
                'waktu_mulai.required' => 'Waktu mulai harus diisi',
                'waktu_mulai.date_format' => 'Format waktu mulai tidak valid (HH:MM)',
                'waktu_selesai.date_format' => 'Format waktu selesai tidak valid (HH:MM)',
                'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai',
                'tempat.max' => 'Tempat maksimal 255 karakter',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validasi tambahan - cek bentrok waktu
            $existingKegiatan = JadwalKegiatan::where('info_or_id', $request->info_or_id)
                ->where('tanggal_kegiatan', $request->tanggal_kegiatan)
                ->where(function($query) use ($request) {
                    if ($request->waktu_selesai) {
                        $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                              ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                              ->orWhere(function($q) use ($request) {
                                  $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                                    ->where('waktu_selesai', '>=', $request->waktu_selesai);
                              });
                    }
                })
                ->first();

            if ($existingKegiatan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sudah ada kegiatan lain di tanggal dan waktu yang sama',
                    'existing_kegiatan' => $existingKegiatan->nama_kegiatan
                ], 422);
            }

            $validated = $validator->validated();
            $kegiatan = JadwalKegiatan::create($validated);

            Log::info('Jadwal kegiatan berhasil dibuat', [
                'id' => $kegiatan->id,
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kegiatan berhasil ditambahkan',
                'data' => [
                    'id' => $kegiatan->id,
                    'nama_kegiatan' => $kegiatan->nama_kegiatan,
                    'deskripsi_kegiatan' => $kegiatan->deskripsi_kegiatan,
                    'tanggal_kegiatan' => $kegiatan->tanggal_kegiatan->format('Y-m-d'),
                    'waktu_mulai' => $kegiatan->waktu_mulai->format('H:i'),
                    'waktu_selesai' => $kegiatan->waktu_selesai ? $kegiatan->waktu_selesai->format('H:i') : null,
                    'tempat' => $kegiatan->tempat,
                    'info_or_id' => $kegiatan->info_or_id
                ]
            ], 201);

        } catch (Exception $e) {
            Log::error('Error creating jadwal kegiatan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan kegiatan. Silakan coba lagi.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update the specified resource
     */
    public function update(Request $request, $id)
    {
        // Validasi role superadmin
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hanya superadmin yang dapat mengedit kegiatan.'
            ], 403);
        }

        try {
            $kegiatan = JadwalKegiatan::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'info_or_id' => 'required|integer|exists:info_or,id',
                'nama_kegiatan' => 'required|string|max:255',
                'deskripsi_kegiatan' => 'nullable|string|max:1000',
                'tanggal_kegiatan' => 'required|date|after_or_equal:today|before:2100-01-01',
                'waktu_mulai' => 'required|date_format:H:i',
                'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
                'tempat' => 'nullable|string|max:255',
            ], [
                'info_or_id.required' => 'Periode harus dipilih',
                'info_or_id.exists' => 'Periode tidak valid',
                'nama_kegiatan.required' => 'Nama kegiatan harus diisi',
                'nama_kegiatan.max' => 'Nama kegiatan maksimal 255 karakter',
                'deskripsi_kegiatan.max' => 'Deskripsi maksimal 1000 karakter',
                'tanggal_kegiatan.required' => 'Tanggal kegiatan harus diisi',
                'tanggal_kegiatan.date' => 'Format tanggal tidak valid',
                'tanggal_kegiatan.after_or_equal' => 'Tanggal kegiatan tidak boleh kurang dari hari ini',
                'tanggal_kegiatan.before' => 'Tanggal kegiatan tidak valid, tahun maksimal 2099',
                'waktu_mulai.required' => 'Waktu mulai harus diisi',
                'waktu_mulai.date_format' => 'Format waktu mulai tidak valid (HH:MM)',
                'waktu_selesai.date_format' => 'Format waktu selesai tidak valid (HH:MM)',
                'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai',
                'tempat.max' => 'Tempat maksimal 255 karakter',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validasi bentrok waktu (exclude current)
            $existingKegiatan = JadwalKegiatan::where('info_or_id', $request->info_or_id)
                ->where('id', '!=', $id)
                ->where('tanggal_kegiatan', $request->tanggal_kegiatan)
                ->where(function($query) use ($request) {
                    if ($request->waktu_selesai) {
                        $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                              ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                              ->orWhere(function($q) use ($request) {
                                  $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                                    ->where('waktu_selesai', '>=', $request->waktu_selesai);
                              });
                    }
                })
                ->first();

            if ($existingKegiatan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sudah ada kegiatan lain di tanggal dan waktu yang sama',
                    'existing_kegiatan' => $existingKegiatan->nama_kegiatan
                ], 422);
            }

            $validated = $validator->validated();
            $kegiatan->update($validated);

            Log::info('Jadwal kegiatan berhasil diupdate', [
                'id' => $kegiatan->id,
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kegiatan berhasil diperbarui',
                'data' => [
                    'id' => $kegiatan->id,
                    'nama_kegiatan' => $kegiatan->nama_kegiatan,
                    'deskripsi_kegiatan' => $kegiatan->deskripsi_kegiatan,
                    'tanggal_kegiatan' => $kegiatan->tanggal_kegiatan->format('Y-m-d'),
                    'waktu_mulai' => $kegiatan->waktu_mulai->format('H:i'),
                    'waktu_selesai' => $kegiatan->waktu_selesai ? $kegiatan->waktu_selesai->format('H:i') : null,
                    'tempat' => $kegiatan->tempat,
                    'info_or_id' => $kegiatan->info_or_id
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error updating jadwal kegiatan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kegiatan. Silakan coba lagi.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove the specified resource
     */
    public function destroy($id)
    {
        // Validasi role superadmin
        if (auth()->user()->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Hanya superadmin yang dapat menghapus kegiatan.'
            ], 403);
        }

        try {
            $kegiatan = JadwalKegiatan::findOrFail($id);
            $namaKegiatan = $kegiatan->nama_kegiatan;
            
            $kegiatan->delete();

            Log::info('Jadwal kegiatan berhasil dihapus', [
                'id' => $id,
                'nama_kegiatan' => $namaKegiatan,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kegiatan "' . $namaKegiatan . '" berhasil dihapus'
            ]);

        } catch (Exception $e) {
            Log::error('Error menghapus jadwal kegiatan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kegiatan. Silakan coba lagi.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}