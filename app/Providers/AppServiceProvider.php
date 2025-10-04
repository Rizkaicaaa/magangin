<?php

namespace App\Providers;

use App\Models\InfoOr;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View Composer untuk halaman login
        View::composer('auth.login', function ($view) {
            // Ambil data InfoOr terbaru yang memiliki gambar
            $latestPoster = InfoOr::whereNotNull('gambar')
                                  ->orderBy('created_at', 'desc')
                                  ->first();

            // Tentukan path gambar. Gunakan gambar default jika tidak ada di database
            $posterPath = $latestPoster ? $latestPoster->gambar : 'images/poster_default.jpg';

            // Kirim variabel $posterPath ke view
            $view->with('posterPath', $posterPath);
        });
    }
}
