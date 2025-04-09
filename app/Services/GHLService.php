<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GHLService
{
    protected $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getContacts($locationId)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->get("https://rest.gohighlevel.com/v1/contacts/", [
            'locationId' => $locationId
        ]);

        if ($response->successful()) {
            return $response->json(); // has "contacts" key
        }

        return ['contacts' => []];
    }

    public function getContactById($id)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->get("https://rest.gohighlevel.com/v1/contacts/{$id}");

        return $response->successful() ? $response->json()['contact'] : null;
    }

    public function createContact($data)
    {
        return Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->post("https://rest.gohighlevel.com/v1/contacts/", $data);
    }

    public function updateContact($id, $data)
    {
        return Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->put("https://rest.gohighlevel.com/v1/contacts/{$id}", $data);
    }

    public function deleteContact($id)
    {
        return Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
        ])->delete("https://rest.gohighlevel.com/v1/contacts/{$id}");
    }

}
