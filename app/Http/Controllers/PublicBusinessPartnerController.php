<?php

namespace App\Http\Controllers;

use App\Models\BusinessPartner;
use Illuminate\Http\Request;

class PublicBusinessPartnerController extends Controller
{
    /**
     * Display a listing of the business partners (public view).
     */
    public function index()
    {
        $businessPartners = BusinessPartner::all();
        return view('public.business_partners.index', compact('businessPartners'));
    }

    /**
     * Display the specified business partner.
     */
    public function show(BusinessPartner $businessPartner)
    {
        $businessPartner->load('productsServices');
        return view('public.business_partners.show', compact('businessPartner'));
    }
}
