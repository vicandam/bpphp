<?php

namespace App\Providers;

use App\Mail\Transport\BrevoTransport;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BrevoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(HttpClientInterface::class, function () {
            return HttpClient::create();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        app(MailManager::class)->extend('brevo', function ($config) {
            return new BrevoTransport(
                app(HttpClientInterface::class),
                $config['api_key']
            );
        });
    }
}
