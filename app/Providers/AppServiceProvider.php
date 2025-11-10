<?php

namespace App\Providers;

use App\Mail\Transport\BrevoTransport;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Opcodes\LogViewer\Facades\LogViewer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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

        /* // Register Brevo mail driver with custom transport but not using right now since we are using brevo SMTP
        app(MailManager::class)->extend('brevo', function ($config) {
            return new BrevoTransport(
                app(HttpClientInterface::class),
                $config['api_key']
            );
        }); */


        View::composer('*', function ($view) {
            $user = Auth::user();

            // Load with preference if authenticated
            if ($user) {
                $user->loadMissing('uiPreference');
                $view->with('uiPreference', $user->uiPreference);
            }
        });

        LogViewer::auth(function ($request) {
            return $request->user()
                && in_array($request->user()->email, [
                    'vicajobs@gmail.com',
                ]);
        });
    }
}
