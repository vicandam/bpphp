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

    public function getContacts1($locationId, $startAfter = null, $startAfterId = null)
    {
        $queryParams = [
            'locationId' => $locationId,
        ];

        if ($startAfter) {
            $queryParams['startAfter'] = $startAfter;
        }

        if ($startAfterId) {
            $queryParams['startAfterId'] = $startAfterId;
        }

        $response = Http::withToken($this->apiKey)
            ->get("https://rest.gohighlevel.com/v1/contacts", $queryParams);

        return $response->json();
    }
    public function getContacts($locationId, $startAfter = null, $startAfterId = null, $search = null)
    {
        $url = "https://rest.gohighlevel.com/v1/contacts";

        $queryParams = [
            'locationId' => $locationId
        ];

        if ($startAfter) {
            $queryParams['startAfter'] = $startAfter;
        }

        if ($startAfterId) {
            $queryParams['startAfterId'] = $startAfterId;
        }

        if ($search) {
            $queryParams['query'] = $search; // this triggers actual search in GHL
        }

        $response = Http::withToken($this->apiKey)
            ->get($url, $queryParams);

        return $response->json();
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
