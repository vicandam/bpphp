<?php

namespace App\Http\Controllers;

use App\Services\GHLService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GHLContactController extends Controller
{
    public function edit($id)
    {
        $user = Auth::user();
        $ghl = new GHLService($user->ghl_api_key);

        $response = $ghl->getContactById($id);
        if (!$response) {
            return redirect()->route('dashboard')->with('status', 'Contact not found.');
        }

        return view('contacts.edit', ['contact' => $response]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'firstName' => 'nullable|string',
            'lastName' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $user = Auth::user();
        $ghl = new GHLService($user->ghl_api_key);

        $ghl->updateContact($id, $request->only('firstName', 'lastName', 'email'));

        return redirect()->route('dashboard')->with('status', 'Contact updated!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'nullable|string',
            'email' => 'required|email',
        ]);

        $user = Auth::user();
        $ghl = new GHLService($user->ghl_api_key);

        $ghl->createContact([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'locationId' => $user->ghl_location_id, // important!
        ]);

        return redirect()->route('dashboard')->with('status', 'Contact added!');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $ghl = new GHLService($user->ghl_api_key);
        $ghl->deleteContact($id);

        return redirect()->route('dashboard')->with('status', 'Contact deleted.');
    }

}
