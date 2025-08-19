<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

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

        // Set locale Carbon ke Indonesia agar nama bulan/tanggal terjemahan tampil bahasa Indonesia
        try {
            Carbon::setLocale('id');
            CarbonImmutable::setLocale('id');
            \Locale::setDefault('id_ID');
        } catch (\Throwable $e) {
            // silent fail jika ekstensi intl tidak tersedia
        }
    }
}
