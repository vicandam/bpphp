<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Opcodes\LogViewer\Logs\Log;
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
    public function generateForTicket(string $ticketCode, $amount = null): string
    {
        $amount = (int) $amount;

        $redeemUrl = url('/redeem-ticket/' . $ticketCode);
        $filePath = "qrcodes/tickets/{$ticketCode}.png";

        // Ensure directory exists under storage/app/public
        if (!Storage::disk('public')->exists('qrcodes/tickets')) {
            Storage::disk('public')->makeDirectory('qrcodes/tickets');
        }

        // Decide QR color based on amount
        if ($amount === 500) {
            $color = [255, 0, 0]; // Red
        } elseif ($amount === 850) {
            $color = [255, 165, 0]; // Orange
        } elseif ($amount < 500 && $amount !== null) {
            $color = [255, 215, 0]; // Gold
        } else {
            $color = [0, 0, 0]; // Default black
        }

        logger('ticket color & amount: ', ['amount' => $amount, 'color' => $color]);

        // Generate QR code with embedded logo + color
        $qr = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            //->color(...$color) // apply dynamic color
            ->color(255, 0, 0)
            ->backgroundColor(255, 255, 255)
            ->merge(public_path('images/bpphp.png'), 0.3, true)
            ->generate($redeemUrl);

        // Save file to disk
        Storage::disk('public')->put($filePath, $qr);

        // Return public URL
        return Storage::url($filePath);
    }

}
