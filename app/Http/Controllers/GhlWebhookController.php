<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GhlWebhookController extends Controller
{
    public function store(Request $request)
    {
        try {
            Log::info('Received from GHL:', $request->all());

            $referralCode = $request->input('Referral code:');

            $user = User::firstOrNew(['email' => $request->input('email')]);

            if ($user->exists && $user->referred_by_member_id !== null) {
                Log::info('User already has a referrer. Skipping referral update.', [
                    'name'  => $user->name,
                    'email' => $user->email,
                ]);
                $user->save();
                return; // wala nay JSON response, logging lang
            }

            if (!empty($referralCode)) {
                $referrer = User::where('referral_code', $referralCode)->first();

                if ($referrer) {
                    if ($user->exists && $user->id === $referrer->id) {
                        Log::warning('User attempted to use own referral code.', [
                            'name'  => $user->name,
                            'email' => $user->email,
                        ]);
                    } else {
                        $user->referred_by_member_id = $referrer->id;
                        Log::info('Referral link created.', [
                            'user_name'  => $user->name,
                            'user_email' => $user->email,
                            'referrer_id' => $referrer->id,
                        ]);
                    }
                } else {
                    Log::warning('Invalid referral code provided.', [
                        'referral_code' => $referralCode,
                        'user_email'    => $user->email,
                    ]);
                }
            }

            $user->name = $request->input('full_name');
            $user->mobile_no = $request->input('phone');
            $user->password = '';
            //$user->city_or_province = trim($request->input('city') . ', ' . $request->input('state'));
            //$user->country = $request->input('country');
            $user->birthday = $request->input('date_of_birth');

            $user->save();

            Log::info('User record created/updated successfully.', [
                'name'  => $user->name,
                'email' => $user->email,
            ]);

        } catch (\Exception $e) {
            Log::error('GHL Webhook processing failed: ' . $e->getMessage(), [
                'payload' => $request->all(),
            ]);
        }
    }
}
