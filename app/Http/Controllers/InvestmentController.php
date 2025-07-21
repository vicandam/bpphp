<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\FilmProject;
use App\Models\Investment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InvestmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // All investment actions require authentication
    }

    /**
     * Display a listing of the user's investments.
     */
    public function index()
    {
        $investments = Auth::user()->investments()->with('filmProject', 'event')->get();
        return view('investments.index', compact('investments'));
    }

    /**
     * Show the form for creating a new investment.
     */
    public function create()
    {
        $filmProjects = FilmProject::all();
        $events = Event::all();
        $referrers = User::where('is_marketing_agent', true)->orWhere('is_marketing_catalyst', true)->get();
        return view('investments.create', compact('filmProjects', 'events', 'referrers'));
    }

    /**
     * Store a newly created investment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'investment_amount' => ['required', 'numeric', 'min:10000'], // Minimum P10,000 investment
            'film_project_id' => ['nullable', 'exists:film_projects,id', 'required_without:event_id'],
            'event_id' => ['nullable', 'exists:events,id', 'required_without:film_project_id'],
            'referred_by_user_id' => ['nullable', 'exists:users,id'],
            'source' => ['required', 'in:new_money,bpp_wallet'], // How the investment is funded
        ]);

        $user = Auth::user();

        // Calculate number of shares (P10,000 = 1 share)
        $numberOfShares = floor($request->investment_amount / 10000);

        if ($numberOfShares < 1) {
            return back()->withErrors(['investment_amount' => 'Minimum investment amount is Php10,000 for at least 1 share.']);
        }

        // Handle investment source
        if ($request->source === 'bpp_wallet') {
            if ($user->bpp_wallet_balance < $request->investment_amount) {
                return back()->withErrors(['source' => 'Insufficient BPP Wallet balance.']);
            }
            $user->bpp_wallet_balance -= $request->investment_amount;
            $user->save();
        } else {
            // Simulate payment success for 'new_money' (replace with actual payment gateway logic)
            $paymentSuccessful = true; // Placeholder
            if (!$paymentSuccessful) {
                return back()->withErrors(['payment_error' => 'Payment failed for new investment.']);
            }
        }

        try {
            $investment = Investment::create([
                'user_id' => $user->id,
                'investment_amount' => $request->investment_amount,
                'number_of_shares' => $numberOfShares,
                'film_project_id' => $request->film_project_id,
                'event_id' => $request->event_id,
                'referred_by_user_id' => $request->referred_by_user_id,
                'investment_date' => now(),
            ]);

            // Elevate user to Angel Investor if they directly invest Php10,000 and above
            if (!$user->is_angel_investor && $request->investment_amount >= 10000) {
                $user->is_angel_investor = true;
                $user->save();
            }

            // Reward referrer if applicable (2.5% lucky rewards3 for referred Angel Film/Event Investors)
            if ($request->filled('referred_by_user_id') && $request->investment_amount > 0) {
                $referrer = User::find($request->referred_by_user_id);
                if ($referrer && ($referrer->is_marketing_agent || $referrer->is_marketing_catalyst)) {
                    $rewardAmount = $request->investment_amount * 0.025;
                    $referrer->bpp_wallet_balance += $rewardAmount;
                    $referrer->save();
                    // Create payout record
                    // Payout::create(['user_id' => $referrer->id, 'type' => 'Investor Referral', 'amount' => $rewardAmount, 'transaction_date' => now()]);
                }
            }

            return redirect()->route('investments.index')->with('success', 'Investment recorded successfully.');
        } catch (\Exception $e) {
            Log::error('Investment creation failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to record investment.']);
        }
    }

    /**
     * Display the specified investment.
     */
    public function show(Investment $investment)
    {
        // Ensure the authenticated user owns this investment
        if (Auth::id() !== $investment->user_id) {
            abort(403, 'Unauthorized action.');
        }
        $investment->load('user', 'filmProject', 'event', 'referredBy');
        return view('investments.show', compact('investment'));
    }
}
