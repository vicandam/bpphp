<?php

namespace App\Http\Controllers;

use App\Models\MembershipType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MembershipTypeController extends Controller
{
    public function __construct()
    {
        // Only allow admins to manage membership types
        $this->middleware('auth'); // Ensure user is logged in
        $this->middleware('admin'); // Custom admin middleware
    }

    /**
     * Display a listing of the membership types.
     */
    public function index()
    {
        $membershipTypes = MembershipType::all();
        return view('membership_types.index', compact('membershipTypes'));
    }

    /**
     * Show the form for creating a new membership type.
     */
    public function create()
    {
        return view('membership_types.create');
    }

    /**
     * Store a newly created membership type in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:membership_types'],
            'description' => ['nullable', 'string'],
        ]);

        try {
            MembershipType::create($request->all());
            return redirect()->route('membership_types.index')->with('success', 'Membership type created successfully.');
        } catch (\Exception $e) {
            Log::error('Membership type creation failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create membership type.']);
        }
    }

    /**
     * Display the specified membership type.
     */
    public function show(MembershipType $membershipType)
    {
        return view('membership_types.show', compact('membershipType'));
    }

    /**
     * Show the form for editing the specified membership type.
     */
    public function edit(MembershipType $membershipType)
    {
        return view('membership_types.edit', compact('membershipType'));
    }

    /**
     * Update the specified membership type in storage.
     */
    public function update(Request $request, MembershipType $membershipType)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:membership_types,name,' . $membershipType->id],
            'description' => ['nullable', 'string'],
        ]);

        try {
            $membershipType->update($request->all());
            return redirect()->route('membership_types.index')->with('success', 'Membership type updated successfully.');
        } catch (\Exception $e) {
            Log::error('Membership type update failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update membership type.']);
        }
    }

    /**
     * Remove the specified membership type from storage.
     */
    public function destroy(MembershipType $membershipType)
    {
        try {
            $membershipType->delete();
            return redirect()->route('membership_types.index')->with('success', 'Membership type deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Membership type deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete membership type.']);
        }
    }
}
