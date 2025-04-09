<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GHLSettingsController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'ghl_api_key' => 'required|string',
            'ghl_location_id' => 'required|string',
        ]);

        $user = Auth::user();
        $user->update([
            'ghl_api_key' => $request->ghl_api_key,
            'ghl_location_id' => $request->ghl_location_id,
        ]);

        return redirect()->back()->with('status', 'GHL Settings updated successfully!');
    }
}
