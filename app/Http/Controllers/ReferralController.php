<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Only authenticated users can view their referrals
    }

    /**
     * Display a listing of the user's referrals.
     */
    public function index()
    {
        $user = Auth::user();
        $referrals = $user->madeReferrals()->with('referredMember')->get();
        return view('referrals.index', compact('referrals', 'user'));
    }

    // The 'store' method for Referral is typically handled within the RegisteredUserController
    // when a new user registers with a referral code.
}
