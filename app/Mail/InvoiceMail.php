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

    /**
     * Create a new message instance.
     */
    public function __construct($invoiceData, $ticket, $customMessage=null)
    {
        $this->invoiceData = $invoiceData;
        $this->customMessage = $customMessage;
        $this->ticket = $ticket;
    }

    public function build()
    {
        try {
            $pdf = Pdf::loadView('invoice.template', ['invoice_data' => $this->invoiceData]);

            return $this->subject('Your Invoice from ' . config('app.name'))
                ->markdown('vendor.xendivel.emails.invoices.paid')
                ->with([
                    'customMessage' => $this->customMessage,
                    'ticket' => $this->ticket
                ])
                ->attachData($pdf->output(), 'invoice.pdf', [
                    'mime' => 'application/pdf',
                ]);
        } catch (\Exception $e) {
            Log::error('Email build failed: ' . $e->getMessage());
        }
    }
}
