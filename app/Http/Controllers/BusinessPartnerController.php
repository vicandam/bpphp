<?php

namespace App\Http\Controllers;

use App\Models\BusinessPartner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BusinessPartnerController extends Controller
{
    /**
     * Display a listing of the business partners (public view).
     */
    public function index()
    {
        $businessPartners = BusinessPartner::all();
        return view('business_partners.index', compact('businessPartners'));
    }

    /**
     * Display the specified business partner.
     */
    public function show(BusinessPartner $businessPartner)
    {
        $businessPartner->load('productsServices');
        return view('business_partners.show', compact('businessPartner'));
    }

    // Admin/Marketing Agent-only methods below
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        // Only admins or marketing agents can create/manage partners
        $this->middleware('can:manage-partners')->except(['index', 'show']); // Custom gate/policy
    }

    /**
     * Show the form for creating a new business partner.
     */
    public function create()
    {
        $users = User::where('is_marketing_agent', true)->orWhere('is_marketing_catalyst', true)->get();
        return view('business_partners.create', compact('users'));
    }

    /**
     * Store a newly created business partner in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'agreement_details' => ['nullable', 'string'],
            'referred_by_user_id' => ['nullable', 'exists:users,id'],
        ]);

        try {
            $partner = BusinessPartner::create($request->all());

            // Reward referrer if applicable
            if ($request->filled('referred_by_user_id')) {
                $referrer = User::find($request->referred_by_user_id);
                if ($referrer && ($referrer->is_marketing_agent || $referrer->is_marketing_catalyst)) {
                    $referrer->bpp_wallet_balance += 0.15 * 1000; // Example: 15% of a hypothetical 1000 base fee for referring a partner
                    $referrer->save();
                    // Create payout record
                    // Payout::create(['user_id' => $referrer->id, 'type' => 'Business Partner Referral', 'amount' => 0.15 * 1000, 'transaction_date' => now()]);
                }
            }

            return redirect()->route('business_partners.index')->with('success', 'Business Partner added successfully.');
        } catch (\Exception $e) {
            Log::error('Business Partner creation failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to add business partner.']);
        }
    }

    /**
     * Show the form for editing the specified business partner.
     */
    public function edit(BusinessPartner $businessPartner)
    {
        $users = User::where('is_marketing_agent', true)->orWhere('is_marketing_catalyst', true)->get();
        return view('business_partners.edit', compact('businessPartner', 'users'));
    }

    /**
     * Update the specified business partner in storage.
     */
    public function update(Request $request, BusinessPartner $businessPartner)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'agreement_details' => ['nullable', 'string'],
            'referred_by_user_id' => ['nullable', 'exists:users,id'],
        ]);

        try {
            $businessPartner->update($request->all());
            return redirect()->route('business_partners.index')->with('success', 'Business Partner updated successfully.');
        } catch (\Exception $e) {
            Log::error('Business Partner update failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update business partner.']);
        }
    }

    /**
     * Remove the specified business partner from storage.
     */
    public function destroy(BusinessPartner $businessPartner)
    {
        try {
            $businessPartner->delete();
            return redirect()->route('business_partners.index')->with('success', 'Business Partner deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Business Partner deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete business partner.']);
        }
    }
}
