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
        if (env('APP_ENV') == 'production') {
            $this->app['request']->server->set('HTTPS', true);
        }

        View::composer('*', function ($view) {
            $user = Auth::user();

            // Load with preference if authenticated
            if ($user) {
                $user->loadMissing('uiPreference');
                $view->with('uiPreference', $user->uiPreference);
            }
        });
    }
}
