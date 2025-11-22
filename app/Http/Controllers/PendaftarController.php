<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pendaftaran;
use App\Models\Dinas;
use App\Models\InfoOr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PendaftarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua user dengan role mahasiswa yang sudah mendaftar magang ditampilkan di halaman data pendaftar
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

        return view('pendaftar.index', compact('pendaftars', 'allDinas', 'allPeriode'));
    }

    /**
     * Create new pendaftar (register mahasiswa + pendaftaran magang)
     */
public function create(Request $request)
{
    try {
        // Log untuk debugging - TAMBAHKAN SEMUA REQUEST DATA
        Log::info('Registration attempt started', [
            'all_request_data' => $request->all(),
            'files' => $request->allFiles(),
            'email' => $request->email,
            'nim' => $request->nim,
            'nama_lengkap' => $request->nama_lengkap, // Pastikan ini ada
        ]);

        // PERBAIKAN: Pastikan validation rules sesuai dengan nama field yang dikirim
        $validated = $request->validate([
            // Data akun mahasiswa - HARUS SESUAI DENGAN NAME DI HTML
            'nama_lengkap'   => 'required|string|max:50',
            'nim'            => 'required|string|max:10|unique:users,nim',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:8|confirmed',
            'no_telp'        => 'required|string|max:13',

            // Data pendaftaran
            'pilihan_dinas_1' => 'required|exists:dinas,id',
            'pilihan_dinas_2' => 'nullable|different:pilihan_dinas_1|exists:dinas,id',
            'motivasi'        => 'required|string|max:500',
            'pengalaman'      => 'nullable|string|max:500',
            'file_cv'         => 'required|file|mimes:pdf,doc,docx|max:5120',
            'file_transkrip'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            // Custom error messages
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'nama_lengkap.string' => 'Nama lengkap harus berupa teks',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter',
            
            'nim.required' => 'NIM harus diisi',
            'nim.unique' => 'NIM sudah terdaftar',
            'nim.max' => 'NIM maksimal 50 karakter',
            
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            
            'no_telp.max' => 'Nomor telepon maksimal 20 karakter',
            
            'pilihan_dinas_1.required' => 'Pilihan dinas utama harus diisi',
            'pilihan_dinas_1.exists' => 'Dinas yang dipilih tidak valid',
            'pilihan_dinas_2.different' => 'Pilihan dinas kedua harus berbeda dengan yang pertama',
            'pilihan_dinas_2.exists' => 'Dinas alternatif yang dipilih tidak valid',
            
            'motivasi.required' => 'Motivasi harus diisi',
            'motivasi.max' => 'Motivasi maksimal 1000 karakter',
            'pengalaman.max' => 'Pengalaman maksimal 1000 karakter',
            
            'file_cv.required' => 'File CV harus diupload',
            'file_cv.file' => 'CV harus berupa file',
            'file_cv.mimes' => 'File CV harus berformat PDF, DOC, atau DOCX',
            'file_cv.max' => 'Ukuran file CV maksimal 5MB',
            
            'file_transkrip.required' => 'File transkrip harus diupload',
            'file_transkrip.file' => 'Transkrip harus berupa file',
            'file_transkrip.mimes' => 'File transkrip harus berformat PDF, JPG, JPEG, atau PNG',
            'file_transkrip.max' => 'Ukuran file transkrip maksimal 5MB',
        ]);

        Log::info('Validation passed successfully', ['validated_data' => $validated]);

        DB::beginTransaction();

        // 1. Buat user dengan data yang sudah divalidasi
        $user = User::create([
            'nama_lengkap'   => $validated['nama_lengkap'], // PASTIKAN MENGGUNAKAN VALIDATED DATA
            'nim'            => $validated['nim'],
            'email'          => $validated['email'],
            'password'       => Hash::make($validated['password']),
            'no_telp'        => $validated['no_telp'],
            'role'           => 'mahasiswa',
            'tanggal_daftar' => now(),
            'status'         => 'aktif',
        ]);

        Log::info('User created successfully', [
            'user_id' => $user->id,
            'nama_lengkap' => $user->nama_lengkap,
            'email' => $user->email
        ]);

        // 2. Cari Info OR yang aktif
        $infoOr = InfoOr::where('status', 'buka')
            ->orderBy('tanggal_buka', 'desc')
            ->first();

        if (!$infoOr) {
            DB::rollBack();
            Log::error('No active InfoOr found');
            
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada periode Info OR yang sedang buka saat ini.',
                'errors' => ['info_or' => ['Periode pendaftaran belum dibuka']]
            ], 400);
        }

        Log::info('Active InfoOr found', ['info_or_id' => $infoOr->id]);

        // 3. Upload files dengan error handling
        try {
            // Validasi file sebelum upload
            if (!$request->hasFile('file_cv') || !$request->file('file_cv')->isValid()) {
                throw new \Exception('File CV tidak valid atau rusak');
            }
            
            if (!$request->hasFile('file_transkrip') || !$request->file('file_transkrip')->isValid()) {
                throw new \Exception('File transkrip tidak valid atau rusak');
            }

            $cvPath = $request->file('file_cv')->store('pendaftaran/cv', 'public');
            $transkripPath = $request->file('file_transkrip')->store('pendaftaran/transkrip', 'public');

            Log::info('Files uploaded successfully', [
                'cv_path' => $cvPath,
                'transkrip_path' => $transkripPath
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('File upload failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload file: ' . $e->getMessage(),
                'errors' => [
                    'file_cv' => ['Gagal upload file CV'],
                    'file_transkrip' => ['Gagal upload file transkrip']
                ]
            ], 400);
        }

        // 4. Simpan data pendaftaran
        $pendaftaran = Pendaftaran::create([
            'user_id'            => $user->id,
            'info_or_id'         => $infoOr->id,
            'pilihan_dinas_1'    => (int) $validated['pilihan_dinas_1'],
            'pilihan_dinas_2'    => $validated['pilihan_dinas_2'] ? (int) $validated['pilihan_dinas_2'] : null,
            'motivasi'           => $validated['motivasi'],
            'pengalaman'         => $validated['pengalaman'],
            'file_cv'            => $cvPath,
            'file_transkrip'     => $transkripPath,
            'status_pendaftaran' => 'terdaftar',
        ]);

        Log::info('Registration completed successfully', [
            'user_id' => $user->id,
            'pendaftaran_id' => $pendaftaran->id,
            'nama_lengkap' => $user->nama_lengkap
        ]);

        DB::commit();

        // Return JSON response untuk AJAX
        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil! Silakan login dengan akun Anda.',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'nim' => $user->nim,
                'nama_lengkap' => $user->nama_lengkap, // GUNAKAN NAMA_LENGKAP
                'pendaftaran_id' => $pendaftaran->id
            ]
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        
        Log::error('Validation failed', [
            'errors' => $e->errors(),
            'request_data' => $request->except(['password', 'password_confirmation'])
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Data yang Anda masukkan tidak valid. Periksa kembali form Anda.',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Registration failed with exception', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'request_data' => $request->except(['password', 'password_confirmation'])
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
            'errors' => ['system' => ['Server error occurred']],
            'debug' => config('app.debug') ? [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ] : null
        ], 500);
    }
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


public function viewCV($id)
{
    $pendaftar = User::findOrFail($id); // kalau tabel utamanya User
    $pendaftaran = $pendaftar->pendaftaran->first();

    if (!$pendaftaran || !$pendaftaran->file_cv) {
        abort(404, 'File CV tidak ditemukan');
    }

   $filePath = storage_path('app/public/' . $pendaftaran->file_cv);


    if (!file_exists($filePath)) {
        abort(404, 'File CV tidak ditemukan');
    }

    return response()->file($filePath, [
        'Content-Type' => mime_content_type($filePath),
        'Content-Disposition' => 'inline; filename="'.basename($filePath).'"'
    ]);
}

/**
 * View Transkrip file in browser
 */
public function viewTranskrip($id)
{
    
    $pendaftar = User::findOrFail($id);
    $pendaftaran = $pendaftar->pendaftaran->first();
    
    if (!$pendaftaran || !$pendaftaran->file_transkrip) {
        abort(404, 'File Transkrip tidak ditemukan');
    }
    
   $filePath = storage_path('app/public/' . $pendaftaran->file_transkrip);

    
    if (!file_exists($filePath)) {
        abort(404, 'File Transkrip tidak ditemukan');
    }
    
    $mimeType = mime_content_type($filePath);
    $fileName = basename($filePath);
    
    // Set headers untuk menampilkan file di browser
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="' . $fileName . '"'
    ]);
}


}