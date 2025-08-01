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

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoiceData;
    public $customMessage;

    /**
     * Create a new message instance.
     */
    public function __construct($invoiceData, $customMessage=null)
    {
        $this->invoiceData = $invoiceData;
        $this->customMessage = $customMessage;
    }

    public function build()
    {
        $pdf = Pdf::loadView('invoice.template',  ['invoice_data' => $this->invoiceData]);

        return $this->subject('Your Invoice from ' . config('app.name'))
            ->markdown('vendor.xendivel.emails.invoices.paid')
            ->with([
                'customMessage' => $this->customMessage,
            ])
            ->attachData($pdf->output(), 'invoice.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
