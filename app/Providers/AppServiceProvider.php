<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

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
        // Muat polyfill fungsi lama PHP (magic_quotes) bila belum ada agar FPDF tidak error.
        if (!function_exists('get_magic_quotes_runtime')) {
            require_once base_path('bootstrap/polyfills.php');
        }

        View::composer('layouts.navigation', function ($view) {
            if (Auth::check()) {
                $unreadNotifications = Notifikasi::where('user_id', Auth::id())
                    ->where('dibaca', false)
                    ->count();
                $view->with('unreadNotifications', $unreadNotifications);
            }
        });
    }
}
