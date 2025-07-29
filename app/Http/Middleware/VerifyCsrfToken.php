<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/xendit/payment-link-callback', // Exclude Xendit Payment Link webhook
        '/xendit/callback', // Exclude Xendit Invoice API webhook (if still used)
    ];
}
