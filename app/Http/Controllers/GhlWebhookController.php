<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendeeRegistrationRequest;
use App\Http\Requests\SponsorRegistrationRequest;
use App\Http\Requests\VendorRegistrationRequest;
use App\Mail\WelcomeEmailAttendeeOld;
use App\Mail\WelcomeEmailAttendee;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

            if (!empty($user->referred_by_member_id)) {
                Referral::create([
                    'referrer_id' => $referrer->id,
                    'referred_member_id' => $user->id,
                    'amount_earned' => 0.00
                ]);
            }

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

    public function storeAttendee(AttendeeRegistrationRequest $request)
    {
        Log::info('Received from GHL:', $request->all());

        try {
            $plainPassword = Str::random(12);
            $user = User::create([
                'type' => 'attendee',
                'name' => $request->full_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'birthday' => $request->birthday,
                'password' => bcrypt($plainPassword),
            ]);

            Log::info('User: ', [$user]);

            //Mail::to([$user->email => $user->full_name])->send(new WelcomeEmailAttendee($user->full_name));
            //Mail::to($user->email)->send(new WelcomeEmailAttendeeOld($user->full_name));

            Mail::to($user->email)->send(new WelcomeEmailAttendee($user));

            return response()->json([
                'success' => true,
                'message' => 'Attendee registered successfully.',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('GHL Webhook processing failed: ' . $e->getMessage(), [
                'payload' => $request->all(),
            ]);
        }
    }

    public function storeVendor(VendorRegistrationRequest $request)
    {
        $plainPassword = Str::random(12);
        $user = User::create([
            'type' => 'vendor',
            'name' => $request->contact_person_name,
            'email' => $request->email,
            'company_name' => $request->company_name,
            'brand_name' => $request->brand_name,
            'products_to_sell' => $request->products_to_sell,
            'product_category' => $request->product_category,
            'contact_person_name' => $request->contact_person_name,
            'mobile_number' => $request->mobile_number,
            'birthday' => $request->birthday,
            'office_address' => $request->office_address,
            'password' => bcrypt($plainPassword),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vendor registered successfully.',
            'data' => $user
        ]);
    }

    public function storeSponsor(SponsorRegistrationRequest $request)
    {
        $plainPassword = Str::random(12);
        $user = User::create([
            'type' => 'sponsor',
            'name' => $request->contact_person_name,
            'email' => $request->email,
            'sponsor_name' => $request->sponsor_name,
            'brand_name' => $request->brand_name,
            'contact_person_name' => $request->contact_person_name,
            'mobile_number' => $request->mobile_number,
            'office_address' => $request->office_address,
            'birthday' => $request->birthday,
            'password' => bcrypt($plainPassword),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sponsor registered successfully.',
            'data' => $user
        ]);
    }
}
