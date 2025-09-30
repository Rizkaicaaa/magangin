<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JadwalKegiatanController;
use App\Http\Controllers\InfoOrController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HasilWawancaraController;
use App\Http\Controllers\JadwalSeleksiController;

use App\Http\Controllers\UserController;
use App\Models\InfoOr;

use App\Http\Controllers\PenilaianWawancaraController;
use App\Http\Controllers\PendaftarController;
use App\Http\Controllers\DashboardController;
Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
//Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');



// Route::get('/dashboard', [DashboardController::class, 'index'])
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::get('/', function () {
    // Ambil data InfoOr terbaru yang memiliki gambar
    $latestPoster = InfoOr::whereNotNull('gambar')
                          ->orderBy('created_at', 'desc')
                          ->first();

    // Tentukan path gambar. Gunakan gambar default jika tidak ada di database
    $posterPath = $latestPoster ? $latestPoster->gambar : 'images/poster_default.jpg';

    // Kirim path gambar ke view 'login' atau 'welcome'
    return view('welcome', compact('posterPath'));
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/kelola-info-or', [InfoOrController::class, 'index'])->name('info-or.index');
    Route::resource('hasilwawancara', HasilWawancaraController::class);

    Route::resource('/jadwal-seleksi', JadwalSeleksiController::class);

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{id}/destroy', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/jadwal-seleksi/{id}', [JadwalSeleksiController::class, 'show'])->name('jadwal-seleksi.show');
    Route::resource('penilaian-wawancara', PenilaianWawancaraController::class);
    Route::get('penilaian-wawancara/{id}', [PenilaianWawancaraController::class, 'show'])->name('penilaian-wawancara.show');



});

// Route::get('/auth', function () {
//     return view('auth');
// });

Route::get('/penilaian', function () {
    return view('penilaian.index');
});

// Route::get('/pendaftar', function () {
//     return view('pendaftar.index');
// });



Route::get('/info-or', [InfoOrController::class, 'index'])->name('info-or.index');
Route::post('/kelola-info-or', [InfoOrController::class, 'store'])->name('info-or.store');
Route::put('/kelola-info-or/{id}/tutup', [InfoOrController::class, 'updateStatus'])->name('info-or.tutup');


// Routes untuk Jadwal Kegiatan Management
Route::middleware(['auth'])->group(function () {

    // ✅ Halaman utama jadwal kegiatan (semua role bisa akses)
    Route::get('/jadwal-kegiatan', [JadwalKegiatanController::class, 'index'])
        ->name('jadwal-kegiatan.index');

    // ✅ API untuk get kegiatan berdasarkan periode (semua role bisa akses)
    Route::get('/jadwal-kegiatan/api/by-periode', [JadwalKegiatanController::class, 'getByPeriode'])
        ->name('jadwal-kegiatan.by-periode');

    // ✅ CRUD operations (validasi role dilakukan di controller)
    Route::post('/jadwal-kegiatan', [JadwalKegiatanController::class, 'store'])
        ->name('jadwal-kegiatan.store');
    Route::put('/jadwal-kegiatan/{id}', [JadwalKegiatanController::class, 'update'])
        ->name('jadwal-kegiatan.update');
    Route::delete('/jadwal-kegiatan/{id}', [JadwalKegiatanController::class, 'destroy'])
        ->name('jadwal-kegiatan.destroy');
    
    // ✅ Detail kegiatan di akhir agar tidak conflict dengan route lain
    Route::get('/jadwal-kegiatan/{id}', [JadwalKegiatanController::class, 'show'])
        ->name('jadwal-kegiatan.show')
        ->where('id', '[0-9]+');
});
Route::middleware(['auth'])->group(function () {
    // Routes untuk mengelola data pendaftar
    Route::get('/pendaftar', [PendaftarController::class, 'index'])->name('pendaftar.index');
    Route::get('/pendaftar/{id}', [PendaftarController::class, 'show'])->name('pendaftar.show');
    Route::put('/pendaftar/{id}/status', [PendaftarController::class, 'updateStatus'])->name('pendaftar.update-status');
    Route::post('/pendaftar/{id}/dinas', [PendaftarController::class, 'setDinasDiterima'])->name('pendaftar.set-dinas');
    Route::get('/pendaftar/{id}/download-cv', [PendaftarController::class, 'downloadCV'])->name('pendaftar.download-cv');
    Route::get('/pendaftar/{id}/download-transkrip', [PendaftarController::class, 'downloadTranskrip'])->name('pendaftar.download-transkrip');
});

require __DIR__.'/auth.php';