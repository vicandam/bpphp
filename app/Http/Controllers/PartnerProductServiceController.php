<?php

namespace App\Http\Controllers;

use App\Models\BusinessPartner;
use App\Models\PartnerProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PartnerProductServiceController extends Controller
{
    public function __construct()
    {
        // Publicly viewable for redemption, but CRUD requires authentication/admin
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('admin')->except(['index', 'show', 'redeem']); // Admin for CRUD, any authenticated for redeem
    }

    /**
     * Display a listing of products/services for a specific business partner.
     */
    public function index(BusinessPartner $businessPartner)
    {
        $productsServices = $businessPartner->productsServices;
        return view('partner_products_services.index', compact('businessPartner', 'productsServices'));
    }

    /**
     * Display the specified product/service.
     */
    public function show(BusinessPartner $businessPartner, PartnerProductService $partnerProductService)
    {
        return view('partner_products_services.show', compact('businessPartner', 'partnerProductService'));
    }

    /**
     * Show the form for creating a new product/service for a business partner.
     */
    public function create(BusinessPartner $businessPartner)
    {
        return view('partner_products_services.create', compact('businessPartner'));
    }

    /**
     * Store a newly created product/service in storage.
     */
    public function store(Request $request, BusinessPartner $businessPartner)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'points_for_redemption' => ['nullable', 'numeric', 'min:0'],
            'is_voucher' => ['boolean'],
        ]);

        try {
            $businessPartner->productsServices()->create($request->all());
            return redirect()->route('business_partners.show', $businessPartner)->with('success', 'Product/Service added successfully.');
        } catch (\Exception $e) {
            Log::error('Product/Service creation failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to add product/service.']);
        }
    }

    /**
     * Show the form for editing the specified product/service.
     */
    public function edit(BusinessPartner $businessPartner, PartnerProductService $partnerProductService)
    {
        return view('partner_products_services.edit', compact('businessPartner', 'partnerProductService'));
    }

    /**
     * Update the specified product/service in storage.
     */
    public function update(Request $request, BusinessPartner $businessPartner, PartnerProductService $partnerProductService)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'points_for_redemption' => ['nullable', 'numeric', 'min:0'],
            'is_voucher' => ['boolean'],
        ]);

        try {
            $partnerProductService->update($request->all());
            return redirect()->route('business_partners.show', $businessPartner)->with('success', 'Product/Service updated successfully.');
        } catch (\Exception $e) {
            Log::error('Product/Service update failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update product/service.']);
        }
    }

    /**
     * Remove the specified product/service from storage.
     */
    public function destroy(BusinessPartner $businessPartner, PartnerProductService $partnerProductService)
    {
        try {
            $partnerProductService->delete();
            return redirect()->route('business_partners.show', $businessPartner)->with('success', 'Product/Service deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Product/Service deletion failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete product/service.']);
        }
    }

    /**
     * Redeem a product/service using BPP points.
     */
    public function redeem(Request $request, PartnerProductService $partnerProductService)
    {
        $user = Auth::user();

        if (is_null($partnerProductService->points_for_redemption) || $partnerProductService->points_for_redemption <= 0) {
            return back()->with('error', 'This item cannot be redeemed with points.');
        }

        if ($user->bpp_points_balance < $partnerProductService->points_for_redemption) {
            return back()->with('error', 'Insufficient BPP points to redeem this item.');
        }

        try {
            $user->bpp_points_balance -= $partnerProductService->points_for_redemption;
            $user->save();

            // Record the redemption (e.g., in a new 'Redemptions' table or a log)
            // Redemption::create([
            //     'user_id' => $user->id,
            //     'product_service_id' => $partnerProductService->id,
            //     'points_used' => $partnerProductService->points_for_redemption,
            //     'redemption_date' => now(),
            // ]);

            return back()->with('success', 'Successfully redeemed ' . $partnerProductService->name . '!');
        } catch (\Exception $e) {
            Log::error('Product/Service redemption failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to redeem item. Please try again.']);
        }
    }
}
