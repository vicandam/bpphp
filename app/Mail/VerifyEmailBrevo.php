<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailBrevo extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verifyUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $verifyUrl)
    {
        $this->user = $user;
        $this->verifyUrl = $verifyUrl;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->to($this->user->email, $this->user->name)
            ->subject('Verify Your Email Address')
            ->markdown('emails.verify_email_brevo')
            ->with([
                'name' => $this->user->name,
                'verifyUrl' => $this->verifyUrl,
            ]);
    }
}
