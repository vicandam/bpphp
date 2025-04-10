<?php

namespace App\Http\Controllers;

use App\Services\GHLService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $contacts = [];
        $meta = [];

        if ($user->ghl_api_key && $user->ghl_location_id) {
            $ghl = new GHLService($user->ghl_api_key);

            $startAfter = $request->query('startAfter');
            $startAfterId = $request->query('startAfterId');
            $search = $request->query('search');

            $response = $ghl->getContacts($user->ghl_location_id, $startAfter, $startAfterId, $search);

            $contacts = $response['contacts'] ?? [];
            $meta = $response['meta'] ?? [];
        }
//dd($meta);
        return view('dashboard', compact('contacts', 'meta'));
    }


    public function create()
    {
        return view('contacts.create');
    }

    public function account()
    {
        $user = Auth::user();
        return view('profile.account', compact('user'));
    }
}
