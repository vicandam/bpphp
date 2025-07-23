<!-- resources/views/business_partners/show.blade.php -->
@extends('layouts.master')

@section('title', $businessPartner->name . ' Details')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0">{{ $businessPartner->name }}</h5>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="row px-4">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <p class="text-muted">{{ $businessPartner->description ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Person</label>
                            <p class="text-muted">{{ $businessPartner->contact_person ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Email</label>
                            <p class="text-muted">{{ $businessPartner->contact_email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Phone</label>
                            <p class="text-muted">{{ $businessPartner->contact_phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Referred By</label>
                            <p class="text-muted">{{ $businessPartner->referredBy->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Agreement Details</label>
                            <p class="text-muted">{{ $businessPartner->agreement_details ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Created At</label>
                            <p class="text-muted">{{ $businessPartner->created_at->format('M d, Y H:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Updated</label>
                            <p class="text-muted">{{ $businessPartner->updated_at->format('M d, Y H:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>Products & Services Offered</h6>
                </div>
                <div class="card-body p-3">
                    @if($businessPartner->productsServices->isEmpty())
                        <p class="text-muted text-center">No products or services listed for this partner yet.</p>
                    @else
                        <ul class="list-group">
                            @foreach($businessPartner->productsServices as $productService)
                                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3 bg-gradient-info shadow text-center">
                                            <i class="material-icons opacity-10">{{ $productService->is_voucher ? 'card_giftcard' : 'shopping_bag' }}</i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">{{ $productService->name }}</h6>
                                            <span class="text-xs">
                                    @if($productService->price) Price: ₱{{ number_format($productService->price, 2) }} @endif
                                                @if($productService->points_for_redemption) | Points: {{ number_format($productService->points_for_redemption, 2) }} @endif
                                </span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        @if(Auth::user() && Auth::user()->id) {{-- Only logged in users can see redeem --}}
                                        @if($productService->points_for_redemption > 0)
                                            <form action="{{ route('products_services.redeem', $productService) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-link text-success text-gradient px-3 mb-0" onclick="return confirm('Are you sure you want to redeem {{ $productService->name }} for {{ number_format($productService->points_for_redemption, 2) }} BPP points?')">Redeem</button>
                                            </form>
                                        @endif
                                        @endif
                                        @if(Auth::user() && Auth::user()->is_admin)
                                            <a href="{{ route('business_partners.products_services.edit', [$businessPartner, $productService]) }}" class="text-secondary font-weight-bold text-xs ms-2" data-toggle="tooltip" data-original-title="Edit Product/Service">
                                                Edit
                                            </a>
                                            <form action="{{ route('business_partners.products_services.destroy', [$businessPartner, $productService]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="return confirm('Are you sure you want to delete this product/service?')">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
