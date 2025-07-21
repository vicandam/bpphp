<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DonationController extends Controller
{
    public function __construct()
    {
        // Publicly allow creating donations, but management is admin-only
        $this->middleware('auth')->except(['create', 'store']);
        $this->middleware('admin')->except(['create', 'store']);
    }

    /**
     * Show the form for creating a new donation.
     */
    public function create()
    {
        // Users can make donations, optionally as a registered user
        return view('donations.create');
    }

    /**
     * Store a newly created donation in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'donor_name' => ['required_without:user_id', 'string', 'max:255'],
            'donation_type' => ['required', 'string', 'in:Cash,In Kind (Products/Services)'],
            'amount' => ['nullable', 'numeric', 'min:0', 'required_if:donation_type,Cash'],
            'description' => ['nullable', 'string', 'required_if:donation_type,In Kind (Products/Services)'],
        ]);

        try {
            $donationData = $request->all();
            $donationData['user_id'] = Auth::id(); // Associate with logged-in user if available
            $donationData['donation_date'] = now();

            Donation::create($donationData);
            return redirect()->route('donations.create')->with('success', 'Thank you for your generous donation!');
        } catch (\Exception $e) {
            Log::error('Donation creation failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to record donation. Please try again.']);
        }
    }

    /**
     * Display a listing of the donations (Admin view).
     */
    public function index()
    {
        $donations = Donation::with('user')->orderBy('donation_date', 'desc')->get();
        return view('donations.index', compact('donations'));
    }

    /**
     * Display the specified donation.
     */
    public function show(Donation $donation)
    {
        $donation->load('user');
        return view('donations.show', compact('donation'));
    }

    /**
     * Show the form for editing the specified donation.
     */
    public function edit(Donation $donation)
    {
        $users = User::all(); // For assigning to a user if not already
        return view('donations.edit', compact('donation', 'users'));
    }

    /**
     * Update the specified donation in storage.
     */
    public function update(Request $request, Donation $donation)
    {
        $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'donor_name' => ['required_without:user_id', 'string', 'max:255'],
            'donation_type' => ['required', 'string', 'in:Cash,In Kind (Products/Services)'],
            'amount' => ['nullable', 'numeric', 'min:0', 'required_if:donation_type,Cash'],
            'description' => ['nullable', 'string', 'required_if:donation_type,In Kind (Products/Services)'],
            'donation_date' => ['required', 'date'],
        ]);

        try {
            $donation->update($request->all());
            return redirect()->route('donations.index')->with('success', 'Donation updated successfully.');
        } catch (\Exception $e) {
            Log::error('Donation update failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update donation.']);
        }
    }

    /**
     * Remove the specified donation from storage.
     */
    public function destroy(Donation $donation)
    {
        try {
            $donation->delete();
            return redirect()->route('donations.index')->with('success', 'Donation deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Donation deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete donation.']);
        }
    }
}
