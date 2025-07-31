<?php

namespace App\Http\Controllers;

use App\Models\PendingOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GhlWebhookController extends Controller
{
    public function store(Request $request)
    {
//          "Referral code:": "REF-09333",
//          "contact_id": "f5TqSsxHzglnllceqH3E",
//          "first_name": "Aretha",
//          "last_name": "Boyd",
//          "full_name": "Aretha Boyd",
//          "email": "pidymizih@mailinator.com",
//          "phone": "+15046214501",
//          "address1": "Consequatur aperiam",
//          "city": "Beatae dolor quidem",
//          "state": "Ea facilis dolor pos",
//          "country": "DZ",
//          "timezone": "Asia/Manila",
//          "date_of_birth": "2025-07-02T00:00:00.000Z",
//          "full_address": "Consequatur aperiam , Beatae dolor quidem  Ea facilis dolor pos 43343",

        try {
            Log::info('Received from GHL:', $request->all());

            // Step 1: Check if user exists by email
            $user = User::where('email', $request->input('email'))->first();

            if (!$user) {
                // Create new user if not found
                $user = new User();
                $user->email = $request->input('email');
            }

            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->name = $request->input('full_name');
            $user->mobile_no = $request->input('phone');
            $user->city_or_province = trim($request->input('city') . ', ' . $request->input('state'));
            $user->country = $request->input('country');
            $user->timezone = $request->input('timezone');
            $user->birthday = $request->input('date_of_birth');
            $user->referral_code = $request->input('Referral code:'); // special key with colon

            $user->save();

            // Step 3: Save pending order to DB
            PendingOrder::create([
                'external_id' => $ticketCode,
                'user_id' => $user->id,
                'event_id' => $event->id,
            ]);

            Log::info($request->email . ' This email already exists. Record just updated.');

        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());

            return back()->withInput()->withErrors(['store_error' => 'An error occurred while creating the user.']);
        }
    }

}
