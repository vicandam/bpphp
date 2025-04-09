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
        // Validate other fields
        $request->validate([
            'firstName' => 'nullable|string',
            'lastName' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => ['nullable', 'regex:/^\+?[0-9]{10,15}$/'], // Validate phone format
        ]);

        // Sanitize and format phone number
        $phone = $request->phone;
        if ($phone) {
            // Remove non-numeric characters
            $phone = preg_replace('/[^0-9]/', '', $phone);

            // If it starts with a country code (e.g., +63), ensure it's formatted correctly
            if (strlen($phone) == 11 && substr($phone, 0, 1) !== '+') {
                $phone = '+63' . substr($phone, 1); // Assuming it's a PH number, modify if needed
            }
        }

        // Prepare data for updating the contact
        $data = $request->only('firstName', 'lastName', 'email');
        if ($phone) {
            $data['phone'] = $phone; // Only add the phone if it's valid
        }

        // Get the user and update the contact
        $user = Auth::user();
        $ghl = new GHLService($user->ghl_api_key);

        // Update contact in GoHighLevel
        $ghl->updateContact($id, $data);

        // Return success message
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
            'phone' => $request->phone,
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
