<!-- resources/views/users/show.blade.php -->
@extends('layouts.master')

@section('title', 'My Profile')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <p class="mb-0">Profile Information</p>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm ms-auto">Edit Profile</a>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="row px-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <p class="text-muted">{{ $user->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <p class="text-muted">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile No.</label>
                            <p class="text-muted">{{ $user->mobile_no ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Birthday</label>
                            <p class="text-muted">{{ $user->birthday ? $user->birthday->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">City/Province</label>
                            <p class="text-muted">{{ $user->city_or_province ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country (if outside PH)</label>
                            <p class="text-muted">{{ $user->country ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Most Favorite Film</label>
                            <p class="text-muted">{{ $user->most_favorite_film ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Most Favorite Song</label>
                            <p class="text-muted">{{ $user->most_favorite_song ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Greatest Dream</label>
                            <p class="text-muted">{{ $user->greatest_dream ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Membership & Rewards</h6>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="row px-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Membership</label>
                            <p class="text-muted">{{ $user->membershipType->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">My Referral Code</label>
                            <p class="text-muted text-lg font-weight-bold">{{ $user->referral_code ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Referred By</label>
                            <p class="text-muted">{{ $user->referrer->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">BPP Wallet Balance</label>
                            <p class="text-muted">₱{{ number_format($user->bpp_wallet_balance, 2) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">BPP Points Balance</label>
                            <p class="text-muted">{{ number_format($user->bpp_points_balance, 2) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Account Status</label>
                            <p class="text-muted">
                                @if($user->is_marketing_agent) <span class="badge bg-gradient-success">Marketing Agent</span> @endif
                                @if($user->is_marketing_catalyst) <span class="badge bg-gradient-info">Marketing Catalyst</span> @endif
                                @if($user->is_angel_investor) <span class="badge bg-gradient-warning">Angel Investor</span> @endif
                                @if($user->is_golden_hearts_awardee) <span class="badge bg-gradient-danger">Golden Hearts Awardee</span> @endif
                                @if(!$user->is_marketing_agent && !$user->is_marketing_catalyst && !$user->is_angel_investor && !$user->is_golden_hearts_awardee)
                                    <span class="badge bg-gradient-secondary">Basic Member</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Virtual Membership Card QR</label>
                            @if($user->virtual_membership_card_qr)
                                <img src="{{ asset($user->virtual_membership_card_qr) }}" alt="Membership QR Code" style="max-width: 150px;">
                            @else
                                <p class="text-muted">QR code not yet generated (requires at least one referral).</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>My Activity Summary</h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="material-symbols-rounded opacity-10">confirmation_number</i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Tickets Purchased</h6>
                                    <span class="text-xs">{{ $user->tickets->count() }} tickets</span>
                                </div>
                            </div>
                            <div class="d-flex">
                                <a href="{{ route('tickets.index') }}" class="btn btn-link btn-icon-only btn-rounded btn-sm text-dark icon-move-right my-auto"><i class="material-symbols-rounded text-sm">arrow_forward</i></a>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-primary shadow text-center">
                                    <i class="material-symbols-rounded opacity-10">trending_up</i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Investments Made</h6>
                                    <span class="text-xs">{{ $user->investments->count() }} investments</span>
                                </div>
                            </div>
                            <div class="d-flex">
                                <a href="{{ route('investments.index') }}" class="btn btn-link btn-icon-only btn-rounded btn-sm text-dark icon-move-right my-auto"><i class="material-symbols-rounded opacity-10 text-sm">arrow_forward</i></a>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-success shadow text-center">
                                    <i class="material-symbols-rounded opacity-10">group_add</i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Members Referred</h6>
                                    <span class="text-xs">{{ $user->madeReferrals->count() }} members</span>
                                </div>
                            </div>
                            <div class="d-flex">
                                <a href="{{ route('referrals.my') }}" class="btn btn-link btn-icon-only btn-rounded btn-sm text-dark icon-move-right my-auto"><i class="material-symbols-rounded text-sm">arrow_forward</i></a>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-info shadow text-center">
                                    <i class="material-symbols-rounded opacity-10">receipt_long</i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Total Payouts</h6>
                                    <span class="text-xs">₱{{ number_format($user->payouts->sum('amount'), 2) }} received</span>
                                </div>
                            </div>
                            <div class="d-flex">
                                {{-- Payouts index is admin only, so no link for regular user --}}
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
