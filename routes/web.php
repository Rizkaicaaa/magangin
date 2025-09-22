<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InfoOrController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HasilWawancaraController;
use App\Http\Controllers\JadwalSeleksiController;
use App\Http\Controllers\UserController;
use App\Models\InfoOr;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
});

// Route::get('/auth', function () {
//     return view('auth');
// });

Route::get('/penilaian', function () {
    return view('penilaian.index');
});

Route::get('/info-or', [InfoOrController::class, 'index'])->name('info-or.index');
Route::post('/kelola-info-or', [InfoOrController::class, 'store'])->name('info-or.store');
Route::put('/kelola-info-or/{id}/tutup', [InfoOrController::class, 'updateStatus'])->name('info-or.tutup');

Route::get('/kegiatan', function () {
    return view('kegiatan.index');
});

require __DIR__.'/auth.php';
