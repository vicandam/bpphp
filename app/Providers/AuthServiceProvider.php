<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('manage-partners', function ($user) {
            return $user->isAdmin() || $user->isMarketingAgent();
        });

        // 🧩 add this for Log Viewer access
        Gate::define('viewLogViewer', function ($user) {
            return $user->isAdmin();
        });
    }
}
