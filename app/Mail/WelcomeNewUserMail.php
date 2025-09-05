<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeNewUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password; // The plain-text password to send

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->to($this->user->email, $this->user->name)
            ->subject('Welcome to ' . config('app.name') . '! Your Login Details')
            ->markdown('emails.welcome-new-user')
            ->with([
                'user' => $this->user,
                'password' => $this->password,
            ]);
    }
}
