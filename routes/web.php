<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InfoOrController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HasilWawancaraController;
use App\Http\Controllers\JadwalSeleksiController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/kelola-info-or', [InfoOrController::class, 'index'])->name('info-or.index');
    Route::resource('hasilwawancara', HasilWawancaraController::class);

    Route::resource('/jadwal-seleksi', JadwalSeleksiController::class);
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
