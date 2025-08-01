<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QrCodeGeneratorService
{
    /**
     * Generates a QR code for a given ticket token and returns its storage path.
     *
     * @param string $ticketCode The unique ticket code (UUID).
     * @return string The public URL or storage path of the generated QR code.
     */
    public function generateForTicket(string $ticketCode): string
    {
        // Define the redeem URL for the QR code
        $redeemUrl = url('/redeem-ticket/' . $ticketCode);

        // Define the file path in 'public' disk
        $filePath = "qrcodes/tickets/{$ticketCode}.svg";

        // Ensure directory exists (optional — Laravel creates it automatically if missing)
        Storage::disk('public')->makeDirectory('qrcodes/tickets');

        // Generate QR code and save to storage/app/public/qrcodes/tickets/
        Storage::disk('public')->put($filePath, QrCode::format('svg')
            ->size(250)
            ->errorCorrection('H')
            ->generate($redeemUrl));

        // Return URL accessible via /storage/...
        return Storage::url($filePath);
    }
}
