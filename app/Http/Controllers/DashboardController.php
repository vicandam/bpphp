<?php

namespace App\Http\Controllers;

use App\Services\GHLService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $contacts = [];

        if ($user->ghl_api_key && $user->ghl_location_id) {
            $ghl = new GHLService($user->ghl_api_key);
            $contacts = $ghl->getContacts($user->ghl_location_id)['contacts'] ?? [];
        }

        return view('dashboard', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function settings()
    {
        $user = Auth::user();
        return view('profile.settings', compact('user'));
    }
}
