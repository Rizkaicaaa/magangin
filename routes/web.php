<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JadwalKegiatanController;
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


// Routes untuk Jadwal Kegiatan Management
Route::middleware(['auth'])->group(function () {
    
    // Halaman utama jadwal kegiatan
    Route::get('/jadwal-kegiatan', [JadwalKegiatanController::class, 'index'])->name('jadwal-kegiatan.index');
    
    // API routes untuk AJAX
    Route::prefix('jadwal-kegiatan')->name('jadwal-kegiatan.')->group(function () {
        
        // Get kegiatan by periode (untuk load tabel)
        
        // CRUD operations
        Route::post('/', [JadwalKegiatanController::class, 'store'])->name('store');
        Route::get('/{id}', [JadwalKegiatanController::class, 'show'])->name('show');
        Route::put('/{id}', [JadwalKegiatanController::class, 'update'])->name('update');
        Route::delete('/{id}', [JadwalKegiatanController::class, 'destroy'])->name('destroy');
        
    });
    
});

require __DIR__.'/auth.php';