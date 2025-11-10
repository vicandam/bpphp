<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendeeRegistrationRequest;
use App\Http\Requests\SponsorRegistrationRequest;
use App\Http\Requests\VendorRegistrationRequest;
use App\Mail\WelcomeEmailAttendeeOld;
use App\Mail\WelcomeEmailAttendee;
use App\Mail\WelcomeEmailSponsor;
use App\Mail\WelcomeEmailVendor;
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

    public function storeAttendee(Request $request)
    {
        Log::info('Attendee:', $request->all());

        try {
            $plainPassword = Str::random(12);
            $user = User::updateOrCreate(
                ['email' => $request->input('email')],
                [
                'type' => 'attendee',
                'name' => $request->input('full_name') ?? '',
                'first_name' => $request->input('first_name') ?? '',
                'last_name' => $request->input('last_name') ?? '',
                'email' => $request->input('email'),
                'mobile_number' => $request->input('phone'),
                'birthday' => $request->input('date_of_birth'),
                'password' => bcrypt($plainPassword),
            ]);

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
    public function storeVendor(Request $request)
    {
        try {
            // flatten the products array if it exists
            $productsToSell = is_array($request->input('Products To Sell'))
                ? implode(', ', $request->input('Products To Sell'))
                : $request->input('Products To Sell');

            // count existing vendor passes (still increment)
            $nextPassNumber = (User::where('type', 'vendor')->max('vendor_pass_number') ?? 0) + 1;

            $user = User::updateOrCreate(
                ['email' => $request->input('email')],
                [
                    'type' => 'vendor',
                    'company_name' => $request->input('Company Name'),
                    'brand_name' => $request->input('Brand Name'),
                    'products_to_sell' => $productsToSell,
                    'other_products' => $request->input('Please specify'),
                    'contact_person_name' => $request->input("Contact Person's Full Name"),
                    'mobile_number' => $request->input('phone'),
                    'birthday' => $request->input('date_of_birth'),
                    'office_address' => $request->input('Office Address') ?? $request->input('full_address'),
                    'city' => $request->input('city'),
                    'country' => $request->input('country'),
                    'timezone' => $request->input('timezone'),
                    'contact_source' => $request->input('contact_source'),

                    // only assign new vendor_pass_number if user doesn't have one yet
                    'vendor_pass_number' => $user->vendor_pass_number ?? $nextPassNumber,
                    'name' => $request->input('full_name') ?? '',
                    'first_name' => $request->input('first_name') ?? '',
                    'last_name' => $request->input('last_name') ?? '',
                    'password' => '',
                ]
            );

            // Make sure vendor_pass_number is populated even if just created
            if (empty($user->vendor_pass_number)) {
                $user->vendor_pass_number = $nextPassNumber;
                $user->save();
            }

            // send welcome email
            if ($user->email) {
                Mail::to($user->email)->send(
                    new WelcomeEmailVendor(
                        $user->contact_person_name ?? 'Vendor',
                        $user
                    )
                );
            }

            Log::info('Vendor data:',[
                    'success' => true,
                    'message' => $user->wasRecentlyCreated
                        ? "Vendor registered successfully with pass #{$nextPassNumber}."
                        : "Vendor information updated successfully (Pass #{$user->vendor_pass_number}).",
                    'data' => $user->contact_person_name
                ]
            );

            return response()->json([
                'success' => true,
                'message' => "Vendor registered successfully with pass #{$user->vendor_pass_number}.",
                'data' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('Saving vendor failed: ' . $e->getMessage(), [
                'payload' => $request->all(),
            ]);
        }
    }

    public function storeSponsor(Request $request)
    {
        try {
            Log::info('Sponsor:', $request->all());

            $user = User::updateOrCreate(
                ['email' => $request->input('email')],
                [
                'type' => 'sponsor',
                'email' => $request->input('email'),
                'company_name' => $request->input('Company Name'),
                'brand_name' => $request->input('Brand Name'),
                'contact_person_name' => $request->input("Contact Person's Full Name"),
                'mobile_number' => $request->input('phone'),
                'office_address' => $request->input('Office Address') ?? $request->input('full_address'),
                'birthday' => $request->input('date_of_birth'),
                'name' => $request->input('full_name') ?? '',
                'first_name' => $request->input('first_name') ?? '',
                'last_name' => $request->input('last_name') ?? '',
                'password' => '',
            ]);

            Mail::to($user->email)->send(new WelcomeEmailSponsor($user->contact_person_name, $user));

            return response()->json([
                'success' => true,
                'message' => 'Sponsor registered successfully.',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Saving sponsor failed: ' . $e->getMessage(), [
                'payload' => $request->all(),
            ]);
        }
    }
}
