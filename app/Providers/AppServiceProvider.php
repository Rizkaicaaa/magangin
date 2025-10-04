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
            // Ini benar untuk mengambil SATU objek tunggal
            $infoOr = InfoOr::whereNotNull('gambar')
                                ->orderBy('created_at', 'desc')
                                ->first();

            $view->with('infoOr', $infoOr);
        });
    }
}
