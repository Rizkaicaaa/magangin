<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluasiMagangController;
use App\Http\Controllers\HasilWawancaraController;
use App\Http\Controllers\InfoOrController;
use App\Http\Controllers\JadwalKegiatanController;
use App\Http\Controllers\JadwalSeleksiController;
use App\Http\Controllers\KelulusanMagangController;
use App\Http\Controllers\KelulusanWawancaraController;
use App\Http\Controllers\PendaftarController;
use App\Http\Controllers\PenilaianWawancaraController;
use App\Http\Controllers\PengumumanMagangController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeleksiWawancaraController;
use App\Http\Controllers\TemplateSertifikatController;
use App\Http\Controllers\UserController;
use App\Models\InfoOr;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/info-or', [InfoOrController::class, 'index'])->name('info-or.index');

// --------------------------------------------------------------------------
// Authenticated Routes (Akses Setelah Login)
// --------------------------------------------------------------------------

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('verified')
        ->name('dashboard');

    // Profile & Password Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Management User Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{id}/destroy', [UserController::class, 'destroy'])->name('users.destroy');

    // Kelola Info OR Routes
    Route::get('/kelola-info-or/create', [InfoOrController::class, 'create'])->name('info-or.create');
    Route::post('/kelola-info-or', [InfoOrController::class, 'store'])->name('info-or.store');
    Route::put('/kelola-info-or/{id}/tutup', [InfoOrController::class, 'updateStatus'])->name('info-or.tutup');

    // Jadwal Kegiatan Routes
    Route::get('/jadwal-kegiatan', [JadwalKegiatanController::class, 'index'])->name('jadwal-kegiatan.index');
    Route::get('/jadwal-kegiatan/api/by-periode', [JadwalKegiatanController::class, 'getByPeriode'])->name('jadwal-kegiatan.by-periode');
    Route::post('/jadwal-kegiatan', [JadwalKegiatanController::class, 'store'])->name('jadwal-kegiatan.store');
    Route::put('/jadwal-kegiatan/{id}', [JadwalKegiatanController::class, 'update'])->name('jadwal-kegiatan.update');
    Route::delete('/jadwal-kegiatan/{id}', [JadwalKegiatanController::class, 'destroy'])->name('jadwal-kegiatan.destroy');
    Route::get('/jadwal-kegiatan/{id}', [JadwalKegiatanController::class, 'show'])->name('jadwal-kegiatan.show')->where('id', '[0-9]+');

    // Pendaftar Routes
    Route::get('/pendaftar', [PendaftarController::class, 'index'])->name('pendaftar.index');
    Route::get('/pendaftar/{id}', [PendaftarController::class, 'show'])->name('pendaftar.show');
    Route::put('/pendaftar/{id}/status', [PendaftarController::class, 'updateStatus'])->name('pendaftar.update-status');
    Route::post('/pendaftar/{id}/dinas', [PendaftarController::class, 'setDinasDiterima'])->name('pendaftar.set-dinas');
    Route::get('/pendaftar/{id}/view-cv', [PendaftarController::class, 'viewCV'])->name('pendaftar.view-cv');
    Route::get('/pendaftar/{id}/view-transkrip', [PendaftarController::class, 'viewTranskrip'])->name('pendaftar.view-transkrip');

    // Seleksi & Wawancara Routes
    Route::resource('hasilwawancara', HasilWawancaraController::class);
    Route::resource('/jadwal-seleksi', JadwalSeleksiController::class);
    Route::get('/jadwal-seleksi/{id}', [JadwalSeleksiController::class, 'show'])->name('jadwal-seleksi.show');
    Route::resource('penilaian-wawancara', PenilaianWawancaraController::class);
    Route::post('/penilaian-wawancara/update-status', [PenilaianWawancaraController::class, 'updateStatus'])->name('penilaian-wawancara.updateStatus');
    Route::get('penilaian-wawancara/{id}', [PenilaianWawancaraController::class, 'show'])->name('penilaian-wawancara.show');
    Route::get('/seleksi-wawancara', [SeleksiWawancaraController::class, 'index'])->name('mahasiswa.jadwal-seleksi');
    Route::get('/kelulusan-wawancara', [KelulusanWawancaraController::class, 'index'])->name('kelulusanwawancara.index');

    // Evaluasi & Kelulusan Magang Routes
    Route::post('/penilaian', [EvaluasiMagangController::class, 'storeOrUpdate'])->name('penilaian.store');
    Route::put('/penilaian/{id}', [EvaluasiMagangController::class, 'storeOrUpdate'])->name('penilaian.update');
    Route::get('/penilaian', [EvaluasiMagangController::class, 'index'])->name('penilaian.index');
    Route::delete('/penilaian/{id}', [EvaluasiMagangController::class, 'destroy'])->name('penilaian.destroy');
    Route::get('/pengumuman-kelulusan', [PengumumanMagangController::class, 'index'])->name('pengumuman.kelulusan');
    Route::post('/pengumuman/{evaluasi_id}/store', [PengumumanMagangController::class, 'store'])->name('pengumuman.store');
    Route::get('/kelulusan-magang', [KelulusanMagangController::class, 'index'])->name('kelulusan-magang.index');

    // Template Sertifikat Routes
    Route::get('/upload-template', [TemplateSertifikatController::class, 'index'])->name('template.upload');
    Route::post('/upload-template', [TemplateSertifikatController::class, 'store'])->name('template.store');
});

// --------------------------------------------------------------------------
// Auth Routes (Login, Register, dll.)
// --------------------------------------------------------------------------
require __DIR__.'/auth.php';