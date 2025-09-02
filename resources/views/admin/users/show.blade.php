@extends('layouts.master')

@section('title', 'Admin - ' . $user->name . ' Profile')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3 mb-0">User Profile: {{ $user->name }}</h6>
                    </div>
                </div>
                <div class="card-body p-3 pb-2">
                    <div class="row">
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">wallet</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Wallet Balance</p>
                                        <h4 class="mb-0">₱{{ number_format($user->bpp_wallet_balance, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">star</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Points Balance</p>
                                        <h4 class="mb-0">{{ number_format($user->bpp_points_balance, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">group_add</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Members Referred</p>
                                        <h4 class="mb-0">{{ $user->referredUsers->count() }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">monetization_on</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Revenue Contributed</p>
                                        <h4 class="mb-0">₱{{ number_format($revenueContributed, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nav-wrapper position-relative mt-4">
                        <ul class="nav nav-pills nav-fill p-1" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link mb-0 px-0 py-1 d-flex align-items-center justify-content-center active" data-bs-toggle="tab" href="#profile_info" role="tab" aria-controls="profile_info" aria-selected="true">
                                    <i class="material-icons text-lg me-2">person</i> Profile Info
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mb-0 px-0 py-1 d-flex align-items-center justify-content-center" data-bs-toggle="tab" href="#referrals_tab" role="tab" aria-controls="referrals_tab" aria-selected="false">
                                    <i class="material-icons text-lg me-2">group</i> Referrals
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mb-0 px-0 py-1 d-flex align-items-center justify-content-center" data-bs-toggle="tab" href="#tickets_tab" role="tab" aria-controls="tickets_tab" aria-selected="false">
                                    <i class="material-icons text-lg me-2">confirmation_number</i> Tickets
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mb-0 px-0 py-1 d-flex align-items-center justify-content-center" data-bs-toggle="tab" href="#investments_tab" role="tab" aria-controls="investments_tab" aria-selected="false">
                                    <i class="material-icons text-lg me-2">trending_up</i> Investments
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mb-0 px-0 py-1 d-flex align-items-center justify-content-center" data-bs-toggle="tab" href="#payouts_tab" role="tab" aria-controls="payouts_tab" aria-selected="false">
                                    <i class="material-icons text-lg me-2">receipt_long</i> Payouts
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content mt-2">
                        <div class="tab-pane fade show active" id="profile_info" role="tabpanel" aria-labelledby="profile_info-tab">
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
                                    <label class="form-label">Current Membership</label>
                                    <p class="text-muted">{{ $user->membershipType->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Referred By</label>
                                    <p class="text-muted">{{ $user->referrer->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Referral Code</label>
                                    <p class="text-muted">{{ $user->referral_code ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Account Status Flags</label>
                                    <p class="text-muted">
                                        @if($user->is_marketing_agent) <span class="badge bg-gradient-success">Marketing Agent</span> @endif
                                        @if($user->is_marketing_catalyst) <span class="badge bg-gradient-info">Marketing Catalyst</span> @endif
                                        @if($user->is_angel_investor) <span class="badge bg-gradient-warning">Angel Investor</span> @endif
                                        @if($user->is_golden_hearts_awardee) <span class="badge bg-gradient-danger">Golden Hearts Awardee</span> @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Created At</label>
                                    <p class="text-muted">{{ $user->created_at->format('M d, Y H:i A') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Updated</label>
                                    <p class="text-muted">{{ $user->updated_at->format('M d, Y H:i A') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="referrals_tab" role="tabpanel" aria-labelledby="referrals_tab-tab">
                            <div class="table-responsive p-0">
                                @if($user->referredUsers->isEmpty())
                                    <p class="text-center text-muted py-4">This user has not referred anyone yet.</p>
                                @else
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Referred User</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Membership Type</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Joined At</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($user->referredUsers as $referredUser)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $referredUser->name }}</h6>
                                                            <p class="text-xs text-secondary mb-0">{{ $referredUser->email }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $referredUser->membershipType->name ?? 'N/A' }}</p>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="text-secondary text-xs font-weight-bold">{{ $referredUser->created_at->format('M d, Y') }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tickets_tab" role="tabpanel" aria-labelledby="tickets_tab-tab">
                            <div class="table-responsive p-0">
                                @if($user->tickets->isEmpty())
                                    <p class="text-center text-muted py-4">This user has not purchased any tickets.</p>
                                @else
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Event</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ticket Code</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Purchase Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($user->tickets as $ticket)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $ticket->event->name ?? 'N/A' }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $ticket->ticket_code }}</p>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="badge badge-sm bg-gradient-{{ $ticket->is_redeemed ? 'secondary' : 'success' }}">{{ $ticket->is_redeemed ? 'Redeemed' : 'Active' }}</span>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="text-secondary text-xs font-weight-bold">{{ $ticket->purchase_date->format('M d, Y') }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>

                        <div class="tab-pane fade" id="investments_tab" role="tabpanel" aria-labelledby="investments_tab-tab">
                            <div class="table-responsive p-0">
                                @if($user->investments->isEmpty())
                                    <p class="text-center text-muted py-4">This user has not made any investments.</p>
                                @else
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Project/Event</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Amount</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Shares</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($user->investments as $investment)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $investment->filmProject->title ?? $investment->event->name ?? 'N/A' }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">₱{{ number_format($investment->investment_amount, 2) }}</p>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="text-secondary text-xs font-weight-bold">{{ $investment->number_of_shares }}</span>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="text-secondary text-xs font-weight-bold">{{ $investment->investment_date->format('M d, Y') }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>

                        <div class="tab-pane fade" id="payouts_tab" role="tabpanel" aria-labelledby="payouts_tab-tab">
                            <div class="table-responsive p-0">
                                @if($user->payouts->isEmpty())
                                    <p class="text-center text-muted py-4">This user has not received any payouts yet.</p>
                                @else
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Type</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Amount</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Transaction Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($user->payouts as $payout)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $payout->type }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">₱{{ number_format($payout->amount, 2) }}</p>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="badge badge-sm bg-gradient-{{ $payout->status == 'paid' ? 'success' : 'warning' }}">{{ ucfirst($payout->status) }}</span>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="text-secondary text-xs font-weight-bold">{{ $payout->created_at->format('M d, Y') }}</span>
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
        </div>
    </div>
@endsection
