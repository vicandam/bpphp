<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin'); // Only admins can manage payouts
    }

    /**
     * Display a listing of the payouts.
     */
    public function index()
    {
        $payouts = Payout::with('user')->orderBy('created_at', 'desc')->get();
        return view('payouts.index', compact('payouts'));
    }

    /**
     * Show the form for creating a new payout (typically triggered by system, but for admin manual creation).
     */
    public function create()
    {
        $users = User::all();
        return view('payouts.create', compact('users'));
    }

    /**
     * Store a newly created payout in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'type' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:pending,paid'],
            'transaction_date' => ['required', 'date'],
        ]);

        try {
            Payout::create($request->all());
            return redirect()->route('payouts.index')->with('success', 'Payout created successfully.');
        } catch (\Exception $e) {
            Log::error('Payout creation failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create payout.']);
        }
    }

    /**
     * Display the specified payout.
     */
    public function show(Payout $payout)
    {
        $payout->load('user');
        return view('payouts.show', compact('payout'));
    }

    /**
     * Show the form for editing the specified payout.
     */
    public function edit(Payout $payout)
    {
        $users = User::all();
        return view('payouts.edit', compact('payout', 'users'));
    }

    /**
     * Update the specified payout in storage.
     */
    public function update(Request $request, Payout $payout)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'type' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:pending,paid'],
            'transaction_date' => ['required', 'date'],
        ]);

        try {
            $payout->update($request->all());
            return redirect()->route('payouts.index')->with('success', 'Payout updated successfully.');
        } catch (\Exception $e) {
            Log::error('Payout update failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update payout.']);
        }
    }

    /**
     * Remove the specified payout from storage.
     */
    public function destroy(Payout $payout)
    {
        try {
            $payout->delete();
            return redirect()->route('payouts.index')->with('success', 'Payout deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Payout deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete payout.']);
        }
    }
}
