<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SponsorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin'); // Only admins can manage sponsors
    }

    /**
     * Display a listing of the sponsors.
     */
    public function index()
    {
        $sponsors = Sponsor::with('referredBy')->get();
        return view('sponsors.index', compact('sponsors'));
    }

    /**
     * Show the form for creating a new sponsor.
     */
    public function create()
    {
        $users = User::where('is_marketing_agent', true)->orWhere('is_marketing_catalyst', true)->get();
        return view('sponsors.create', compact('users'));
    }

    /**
     * Store a newly created sponsor in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'sponsorship_type' => ['nullable', 'string', 'max:255'],
            'amount_pledged' => ['nullable', 'numeric', 'min:0'],
            'referred_by_user_id' => ['nullable', 'exists:users,id'],
        ]);

        try {
            $sponsor = Sponsor::create($request->all());

            // Reward referrer if applicable (15% lucky rewards2 for event sponsorships closed)
            if ($request->filled('referred_by_user_id') && $request->filled('amount_pledged')) {
                $referrer = User::find($request->referred_by_user_id);
                if ($referrer && ($referrer->is_marketing_agent || $referrer->is_marketing_catalyst)) {
                    $rewardAmount = $request->amount_pledged * 0.15;
                    $referrer->bpp_wallet_balance += $rewardAmount;
                    $referrer->save();
                    // Create payout record
                    // Payout::create(['user_id' => $referrer->id, 'type' => 'Sponsor Referral', 'amount' => $rewardAmount, 'transaction_date' => now()]);
                }
            }

            return redirect()->route('sponsors.index')->with('success', 'Sponsor added successfully.');
        } catch (\Exception $e) {
            Log::error('Sponsor creation failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to add sponsor.']);
        }
    }

    /**
     * Show the form for editing the specified sponsor.
     */
    public function edit(Sponsor $sponsor)
    {
        $users = User::where('is_marketing_agent', true)->orWhere('is_marketing_catalyst', true)->get();
        return view('sponsors.edit', compact('sponsor', 'users'));
    }

    /**
     * Update the specified sponsor in storage.
     */
    public function update(Request $request, Sponsor $sponsor)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'sponsorship_type' => ['nullable', 'string', 'max:255'],
            'amount_pledged' => ['nullable', 'numeric', 'min:0'],
            'referred_by_user_id' => ['nullable', 'exists:users,id'],
        ]);

        try {
            $sponsor->update($request->all());
            return redirect()->route('sponsors.index')->with('success', 'Sponsor updated successfully.');
        } catch (\Exception $e) {
            Log::error('Sponsor update failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update sponsor.']);
        }
    }

    /**
     * Remove the specified sponsor from storage.
     */
    public function destroy(Sponsor $sponsor)
    {
        try {
            $sponsor->delete();
            return redirect()->route('sponsors.index')->with('success', 'Sponsor deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Sponsor deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete sponsor.']);
        }
    }
}
