<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
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
        View::composer('*', function ($view) {
            $unreadMessages = 0;
            if (Auth::check() && method_exists(Auth::user(), 'unreadMessages')) {
                $unreadMessages = Auth::user()->unreadMessages()->count();
            }
            $view->with('unreadMessages', $unreadMessages);
        });
    }
}
