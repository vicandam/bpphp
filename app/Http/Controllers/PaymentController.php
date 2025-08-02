<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    public function checkout()
    {
        $external_id = 'INV-' . now()->format('Ymd') . '-' . mt_rand(10000, 99999);

        return view('payments.checkout', compact('external_id'));
    }
    public function payWithCard(Request $request)
    {
        try {
            $result = $this->paymentService->handleCardPayment($request);
            return response()->json($result);
        } catch (\Throwable $e) {
            Log::error('Card payment failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
