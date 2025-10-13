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
    public string $guestName;
    public ?string $referralCode;

    /**
     * Create a new message instance.
     */
    public function __construct(string $guestName, ?string $referralCode = null)
    {
        $this->guestName = $guestName;
        $this->referralCode = $referralCode;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('🎃 Your Digital Ticket to the VSF Halloween Bazaar & Costume Party')
            ->markdown('emails.welcome_attendee');
    }
}
