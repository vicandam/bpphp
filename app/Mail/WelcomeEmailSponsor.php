<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmailSponsor extends Mailable
{
    use Queueable, SerializesModels;

    public $sponsorName;
    protected $user;

    public function __construct($sponsorName, $user)
    {
        $this->sponsorName = $sponsorName;
        $this->user = $user;
    }

    public function build()
    {
        return $this->to($this->user->email, $this->user->contact_person_name)
            ->subject('🌟 Welcome to the VSF Halloween Bazaar & Costume Party — Event Sponsor')
            ->markdown('emails.welcome_sponsor');
    }
}
