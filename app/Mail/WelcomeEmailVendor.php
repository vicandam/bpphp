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
    protected $vendorPassAmount;

    /**
     * Create a new message instance.
     */
    public function __construct(string $vendorName, $user, $vendorPassNumber, $vendorPassAmount)
    {
        $this->vendorName = $vendorName;
        $this->user = $user;
        $this->vendorPassNumber = $vendorPassNumber;
        $this->vendorPassAmount = $vendorPassAmount;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $vendorPassImageUrl = $this->generateVendorPassImage($this->vendorPassNumber, $this->vendorPassAmount);

        return $this->to($this->user->email, $this->user->contact_person_name)
            ->subject('Your Digital Vendor Pass to Alive 2025 Heal As One Nationwide Love Tour')
            ->markdown('emails.welcome_vendor')
            ->with([
                'vendorName' => $this->user->contact_person_name,
                'vendorPassNumber' => $this->vendorPassNumber,
                'vendorPassImageUrl' => $vendorPassImageUrl,
            ]);
    }
    public function generateVendorPassImageOld($vendorPassNumber, $amount)
    {
        // COLORS
        $yellow = [255, 215, 0];     // P1,500 – Entrance Lobby
        $green = [46, 204, 113];     // P1,000 – Left Side Lobby

        $color = $amount == 1500 ? $yellow : $green;

        // Canvas
        $width = 600;
        $height = 350;
        $img = imagecreatetruecolor($width, $height);

        // Background
        $bg = imagecolorallocate($img, $color[0], $color[1], $color[2]);
        imagefilledrectangle($img, 0, 0, $width, $height, $bg);

        // Text color
        $white = imagecolorallocate($img, 255, 255, 255);

        // Text to display
        $text = "BVP " . $vendorPassNumber;

        // Use larger built-in font (GD font 5)
        $font = 24;

        // Calculate center position
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);

        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2;

        // Draw centered text
        imagestring($img, $font, $x, $y, $text, $white);

        // Ensure directory exists
        $directory = storage_path('app/public/vendor_pass');
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Save image
        $filename = 'vendor_pass_' . $vendorPassNumber . '.png';
        $path = $directory . '/' . $filename;

        imagepng($img, $path);
        imagedestroy($img);

        return asset("storage/vendor_pass/" . $filename);
    }
    public function generateVendorPassImage($vendorPassNumber, $amount)
    {
        // COLORS
        $yellow = [255, 215, 0];     // P1,500 – Entrance Lobby
        $green = [46, 204, 113];     // P1,000 – Left Side Lobby

        $color = $amount == 1500 ? $yellow : $green;

        // Canvas
        $width = 600;
        $height = 350;
        $img = imagecreatetruecolor($width, $height);

        // Background color
        $bg = imagecolorallocate($img, $color[0], $color[1], $color[2]);
        imagefilledrectangle($img, 0, 0, $width, $height, $bg);

        // Text color
        $white = imagecolorallocate($img, 255, 255, 255);

        // Final output text
        $text = "BVP " . $vendorPassNumber;

        // Path to TTF font
        $fontPath = public_path('fonts/Montserrat-ExtraBold.ttf');

        // Big text size (adjustable)
        $fontSize = 80;

        // Calculate bounding box
        $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);

        $textWidth = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];

        // Center text coordinates
        $x = ($width - $textWidth) / 2;
        $y = ($height + $textHeight) / 2;

        // Draw text
        imagettftext($img, $fontSize, 0, $x, $y, $white, $fontPath, $text);

        // Ensure directory exists
        $directory = storage_path('app/public/vendor_pass');
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Save file
        $filename = 'vendor_pass_' . $vendorPassNumber . '.png';
        $path = $directory . '/' . $filename;

        imagepng($img, $path);
        imagedestroy($img);

        return asset("storage/vendor_pass/" . $filename);
    }
}
