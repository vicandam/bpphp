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
    public function generateForTicketOld(string $ticketCode): string
    {
        $redeemUrl = url('/redeem-ticket/' . $ticketCode);
        $filePath = "qrcodes/tickets/{$ticketCode}.png";

        // Make sure directory exists
        Storage::disk('public')->makeDirectory('qrcodes/tickets');

        // Generate QR code with embedded logo
        $qr = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H') // High error correction to allow for logo visibility
            ->merge(public_path('images/bpphp.png'), 0.3, true) // 30% logo scale
            ->generate($redeemUrl);

        // Save to disk
        Storage::disk('public')->put($filePath, $qr);

        return Storage::url($filePath); // returns a usable public URL
    }

    public function generateForTicket(string $ticketCode): string
    {
        // Define directory under storage/app/qrcodes/tickets
        $directory = storage_path('app/qrcodes/tickets');
        File::ensureDirectoryExists($directory);

        // Define filename and save path
        $filename = "{$ticketCode}.png";
        $savePath = "{$directory}/{$filename}";

        // Generate redeem URL for QR code
        $redeemUrl = url('/redeem-ticket/' . $ticketCode);

        // Generate QR code with embedded logo
        $qr = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H') // High error correction (good for logo overlay)
            ->merge(public_path('images/bpphp.png'), 0.3, true) // 30% logo scale
            ->generate($redeemUrl);

        // Save the file to disk
        File::put($savePath, $qr);

        // Return relative filename (same style as generateInvoice)
        return $filename;
    }

}
