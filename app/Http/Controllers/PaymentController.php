<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    public function checkout()
    {
        $event = Event::getActiveCampaignEvent();
        $externalId = $this->paymentService->generateExternalId();

        if (!$event) {
            abort(404, 'No active campaign event found.');
        }

        return view('payments.checkout', [
            'external_id' => $externalId,
            'ticketPrice' => $event->ticket_price,
        ]);
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

    public function payViaWallet(Request $request)
    {
        try {
            $result = $this->paymentService->handleEwalletPayment($request);
            return response()->json($result);
        } catch (\Throwable $e) {
            Log::error('Wallet payment failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

}
