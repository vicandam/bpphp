<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmailVendor extends Mailable
{
    use Queueable, SerializesModels;

    public string $vendorName;
    protected $user;
    protected $vendorPassNumber;

    /**
     * Create a new message instance.
     */
    public function __construct(string $vendorName, $user, $vendorPassNumber)
    {
        $this->vendorName = $vendorName;
        $this->user = $user;
        $this->vendorPassNumber = $vendorPassNumber;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->to($this->user->email, $this->user->contact_person_name)
            ->subject('🎃 Your Digital Vendor Pass to the VSF Halloween Bazaar & Costume Party')
            ->markdown('emails.welcome_vendor')
            ->with([
                'vendorName' => $this->user->contact_person_name,
                'vendorPassNumber' => $this->vendorPassNumber,
            ]);
    }
}
