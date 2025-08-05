<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoiceData;
    public $customMessage;
    public $ticket;
    public $filename;

    /**
     * Create a new message instance.
     */
    public function __construct($invoiceData, $ticket, string $filename, $customMessage=null)
    {
        $this->invoiceData = $invoiceData;
        $this->customMessage = $customMessage;
        $this->ticket = $ticket;
        $this->filename = $filename;
    }

    public function build()
    {
        try {

            $path = storage_path('app/invoices/' . $this->filename);

            return $this->subject('Your Invoice from ' . config('app.name'))
                ->markdown('vendor.xendivel.emails.invoices.paid')
                ->with([
                    'customMessage' => $this->customMessage ?? '',
                    'ticket' => $this->ticket,
                ])
                ->attach($path, [
                    'as' => 'invoice.pdf',
                    'mime' => 'application/pdf',
                ]);

        } catch (\Exception $e) {
            Log::error('Email build failed: ' . $e->getMessage());
        }
    }
}
