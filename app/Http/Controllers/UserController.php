<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\UiPreference;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(User $user)
    {
        // Ensure the authenticated user can only view their own profile
        if (Auth::id() !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Load relationships if needed
        $user->load('membershipType', 'referredUsers', 'madeReferrals', 'tickets', 'investments', 'payouts', 'donations');

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit(User $user)
    {
        if (Auth::id() !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        return view('users.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request, User $user)
    {
        if (Auth::id() !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'mobile_no' => ['nullable', 'string', 'max:20'],
            'birthday' => ['nullable', 'date'],
            'city_or_province' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'most_favorite_film' => ['nullable', 'string', 'max:255'],
            'most_favorite_song' => ['nullable', 'string', 'max:255'],
            'greatest_dream' => ['nullable', 'string'],
        ]);

        try {
            $user->update($request->all());
            return redirect()->route('users.show', $user)->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            \Log::error('User profile update failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['update_error' => 'An error occurred while updating your profile.']);
        }
    }

    /**
     * Elevate user to Marketing Agent (Admin/System triggered).
     * This method would typically be called internally or by an admin.
     */
    public function elevateToMarketingAgent(User $user)
    {
        // This method should be protected by admin middleware
        // if (!Auth::user()->isAdmin()) { abort(403); }

        if (!$user->is_marketing_agent) {
            $user->is_marketing_agent = true;
            $user->save();

            // Optionally update membership type or add to a pivot table
            // $marketingMembership = MembershipType::where('name', 'Lucky Marketing Agents and Catalysts')->first();
            // if ($marketingMembership) {
            //     $user->membership_type_id = $marketingMembership->id;
            //     $user->save();
            // }

            return back()->with('success', $user->name . ' has been elevated to Marketing Agent.');
        }
        return back()->with('info', $user->name . ' is already a Marketing Agent.');
    }

    /**
     * Elevate user to Marketing Catalyst (Admin/System triggered).
     * This method would typically be called internally or by an admin.
     */
    public function elevateToMarketingCatalyst(User $user)
    {
        // This method should be protected by admin middleware
        // if (!Auth::user()->isAdmin()) { abort(403); }

        if (!$user->is_marketing_catalyst) {
            // Check if they have referred at least 100 new paid members
            $referredCount = Referral::where('referrer_id', $user->id)->count(); // Assuming all referrals are paid
            if ($referredCount >= 100) {
                $user->is_marketing_catalyst = true;
                $user->save();
                return back()->with('success', $user->name . ' has been elevated to Marketing Catalyst.');
            }
            return back()->with('warning', $user->name . ' does not meet the criteria for Marketing Catalyst (needs 100 paid referrals).');
        }
        return back()->with('info', $user->name . ' is already a Marketing Catalyst.');
    }

    /**
     * Elevate user to Angel Investor (Admin/System triggered).
     */
    public function elevateToAngelInvestor(User $user)
    {
        // This method should be protected by admin middleware
        // if (!Auth::user()->isAdmin()) { abort(403); }

        if (!$user->is_angel_investor) {
            $user->is_angel_investor = true;
            $user->save();
            return back()->with('success', $user->name . ' has been elevated to Angel Investor.');
        }
        return back()->with('info', $user->name . ' is already an Angel Investor.');
    }

    /**
     * Elevate user to Golden Hearts Awardee (Admin/System triggered).
     */
    public function elevateToGoldenHeartsAwardee(User $user)
    {
        // This method should be protected by admin middleware
        // if (!Auth::user()->isAdmin()) { abort(403); }

        if (!$user->is_golden_hearts_awardee) {
            $user->is_golden_hearts_awardee = true;
            $user->save();
            return back()->with('success', $user->name . ' has been elevated to Golden Hearts Awardee.');
        }
        return back()->with('info', $user->name . ' is already a Golden Hearts Awardee.');
    }

    public function updateUiPreferences(Request $request)
    {
//        $request->validate([
//            'sidebar_color' => 'required|string',
//            'sidenav_type' => 'required|string',
//            'navbar_fixed' => 'required|boolean',
//            'theme_mode' => 'required|string',
//        ]);


        $user = auth()->user();

        if ($request->type == 'sidenav_type') {
            UiPreference::updateOrCreate(
                ['user_id' => $user->id],
                [
                    //'sidebar_color' => $request->sidebar_color,
                    'sidenav_type' => $request->sidenav_color,
                    //'navbar_fixed' => $request->navbar_fixed,
                    //'theme_mode' => $request->theme_mode,
                ]
            );
        }

        return response()->json(['status' => 'success']);
    }
    public function updateUIPreferencesOld(Request $request)
    {
        $request->validate([
            'sidebar_color' => 'nullable|string',
        ]);

        auth()->user()->update([
            'sidebar_color' => $request->sidebar_color,
        ]);

        return response()->json(['message' => 'Preferences updated']);
    }

}
