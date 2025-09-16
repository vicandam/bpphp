<?php

namespace App\Http\Controllers;

use App\Models\FilmProject;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FilmProjectController extends Controller
{
    /**
     * Display a listing of the film projects (public view).
     */
    public function index()
    {
        $filmProjects = FilmProject::all();
        return view('film_projects.index', compact('filmProjects'));
    }

    /**
     * Display the specified film project.
     */
    public function show(FilmProject $filmProject)
    {
        $filmProject->load('investments.user'); // Load investors
        return view('film_projects.show', compact('filmProject'));
    }

    // Admin-only methods below
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('admin')->except(['index', 'show', 'calculateAngelInvestorShares']); // Admin for CRUD, admin for calculation
    }

    /**
     * Show the form for creating a new film project.
     */
    public function create()
    {
        return view('film_projects.create');
    }

    /**
     * Store a newly created film project in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'link' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:Pre-production,Production,Post-production,Released'],
            'target_fund_amount' => ['nullable', 'numeric', 'min:0'],
            'total_net_theatrical_ticket_sales' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            FilmProject::create($request->all());
            return redirect()->route('film_projects.index')->with('success', 'Film Project created successfully.');
        } catch (\Exception $e) {
            Log::error('Film Project creation failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create film project.']);
        }
    }

    /**
     * Show the form for editing the specified film project.
     */
    public function edit(FilmProject $filmProject)
    {
        return view('film_projects.edit', compact('filmProject'));
    }

    /**
     * Update the specified film project in storage.
     */
    public function update(Request $request, FilmProject $filmProject)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'link' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:Pre-production,Production,Post-production,Released'],
            'target_fund_amount' => ['nullable', 'numeric', 'min:0'],
            'total_net_theatrical_ticket_sales' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $filmProject->update($request->all());
            return redirect()->route('film_projects.index')->with('success', 'Film Project updated successfully.');
        } catch (\Exception $e) {
            Log::error('Film Project update failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update film project.']);
        }
    }

    /**
     * Remove the specified film project from storage.
     */
    public function destroy(FilmProject $filmProject)
    {
        try {
            $filmProject->delete();
            return redirect()->route('film_projects.index')->with('success', 'Film Project deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Film Project deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete film project.']);
        }
    }

    /**
     * Calculate and distribute Angel Investor shares for a film project.
     */
    public function calculateAngelInvestorShares(FilmProject $filmProject)
    {
        // This method should be protected by admin middleware
        $this->middleware('admin');

        if ($filmProject->total_net_theatrical_ticket_sales <= 0) {
            return back()->with('warning', 'No theatrical ticket sales recorded for this film project to distribute shares.');
        }

        $totalAngelInvestorsShare = $filmProject->total_net_theatrical_ticket_sales * 0.30; // 30% of NPTTS

        $totalShares = $filmProject->investments->sum('number_of_shares');

        if ($totalShares === 0) {
            return back()->with('warning', 'No investments recorded for this film project to distribute shares.');
        }

        $amountPerShare = $totalAngelInvestorsShare / $totalShares;

        try {
            foreach ($filmProject->investments as $investment) {
                $investor = $investment->user;
                if ($investor) {
                    $investorShare = $investment->number_of_shares * $amountPerShare;

                    // Add to investor's wallet
                    $investor->bpp_wallet_balance += $investorShare;
                    $investor->save();

                    // Create a payout record
                    Payout::create([
                        'user_id' => $investor->id,
                        'type' => 'Angel Investor Share (Film: ' . $filmProject->title . ')',
                        'amount' => $investorShare,
                        'status' => 'pending', // Or 'paid' if immediately processed
                        'transaction_date' => now(),
                    ]);
                }
            }
            return back()->with('success', 'Angel Investor shares calculated and distributed for ' . $filmProject->title . '.');
        } catch (\Exception $e) {
            Log::error('Angel Investor share calculation failed for film project ' . $filmProject->id . ': ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to calculate and distribute Angel Investor shares.']);
        }
    }
}
