<?php

namespace App\Listeners;

use App\Mail\LoginNotification;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class LogLastLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Update last login timestamp
        $user->update([
            'last_login_at' => now(),
        ]);

        // Send email to you
        Mail::to('vicajobs@gmail.com')->send(new LoginNotification($user));
    }
}
