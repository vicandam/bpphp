<?php

namespace App\Services;

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
        // The URL embedded in the QR code.
        // This URL will be scanned by venue staff.
        $redeemUrl = url('/redeem-ticket/' . $ticketCode);

        // Define the storage path
        $fileName = 'tickets/' . $ticketCode . '.svg';
        $filePath = 'qrcodes/' . $fileName; // Path relative to storage/app/public

        // Generate and store the QR code
        QrCode::format('svg')
            ->size(250) // Adjust size as needed
            ->errorCorrection('H') // High error correction
            ->generate($redeemUrl, storage_path('app/public/' . $filePath));

        // Return the public URL to access the QR code image
        return Storage::url($filePath);
    }
}
