<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmailAttendee extends Mailable
{
    use Queueable, SerializesModels;

    public string $fullName;
    public ?string $referralCode;

    /**
     * Create a new message instance.
     */
    public function __construct(string $fullName, ?string $referralCode = null)
    {
        $this->fullName = $fullName;
        $this->referralCode = $referralCode;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎃 Welcome to the VSF Halloween Bazaar & Costume Party!'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.welcome_attendee',
            with: [
                'fullName' => $this->fullName,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
