<!-- resources/views/referrals/index.blade.php -->
@extends('layouts.master')

@section('title', 'My Referrals')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3 mb-0">Members I Have Referred</h6>
                        <span class="text-white me-3">My Referral Code: <strong class="text-lg">{{ $user->referral_code ?? 'N/A' }}</strong></span>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        @if($referrals->isEmpty())
                            <p class="text-center text-muted py-4">You haven't referred any members yet. Share your referral code (<strong>{{ $user->referral_code ?? 'N/A' }}</strong>) to start earning!</p>
                        @else
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Referred Member</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Amount Earned</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Referral Date</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($referrals as $referral)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <i class="material-icons text-lg me-3">person</i>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $referral->referredMember->name ?? 'Unknown User' }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $referral->referredMember->email ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">₱{{ number_format($referral->amount_earned, 2) }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $referral->created_at->format('M d, Y') }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('users.show', $referral->referredMember) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="View Referred Member">
                                                View Profile
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
