<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmailAttendee extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;
    protected $referralCode;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $referralCode = null)
    {
        $this->user = $user;
        $this->referralCode = $referralCode;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->to($this->user->email, $this->user->first_name)
            ->subject('🎃 Your Digital Ticket to the VSF Halloween Bazaar & Costume Party')
            ->markdown('emails.welcome_attendee')
            ->with([
                'firstName' => $this->user->first_name,
                'referralCode' => $this->referralCode
            ]);
    }
}
