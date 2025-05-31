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
