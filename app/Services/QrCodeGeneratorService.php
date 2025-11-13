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
        $redeemUrl = url('/redeem-ticket/' . $ticketCode);
        $filePath = "qrcodes/tickets/{$ticketCode}.png";

        // Ensure the directory exists under storage/app/public
        if (!Storage::disk('public')->exists('qrcodes/tickets')) {
            Storage::disk('public')->makeDirectory('qrcodes/tickets');
        }

        // Generate QR code with embedded logo
//        $qr = QrCode::format('png')
//            ->size(300)
//            ->errorCorrection('H')
//            ->merge(public_path('images/bpphp.png'), 0.3, true)
//            ->generate($redeemUrl);

        $qr = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H') // High error correction to allow logo overlay
            ->color(255, 165, 0)   // Orange foreground (RGB)
            ->backgroundColor(255, 255, 255) // White background (optional)
            ->merge(public_path('images/bpphp.png'), 0.3, true)
            ->generate($redeemUrl);

        // Save to storage/app/public/qrcodes/tickets/
        Storage::disk('public')->put($filePath, $qr);

        // Return public URL (via /storage symlink)
        return Storage::url($filePath);
    }

}
