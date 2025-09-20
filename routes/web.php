<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JadwalKegiatanController;
use Illuminate\Support\Facades\Route;

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
});

// Route::get('/auth', function () {
//     return view('auth');
// });

Route::get('/penilaian', function () {
    return view('penilaian.index');
});

Route::get('/info-or', function () {
    return view('info_or.index');
});


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