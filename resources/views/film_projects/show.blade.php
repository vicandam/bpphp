@extends('layouts.master')

@section('title', $filmProject->title . ' Details')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0">{{ $filmProject->title }}</h5>
                        @if(Auth::user() && Auth::user()->is_admin)
                            <a href="{{ route('film_projects.edit', $filmProject) }}" class="btn btn-primary btn-sm ms-auto">Edit Film Project</a>
                        @endif
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="row px-4">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <p class="text-muted">{{ $filmProject->description ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <p class="text-muted">
                            <span class="badge badge-sm bg-gradient-{{
                                $filmProject->status == 'Released' ? 'success' :
                                ($filmProject->status == 'Production' ? 'info' :
                                ($filmProject->status == 'Post-production' ? 'warning' : 'secondary'))
                            }}">{{ $filmProject->status }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Target Fund Amount</label>
                            <p class="text-muted">₱{{ number_format($filmProject->target_fund_amount, 2) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Net Theatrical Ticket Sales</label>
                            <p class="text-muted">₱{{ number_format($filmProject->total_net_theatrical_ticket_sales, 2) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Created At</label>
                            <p class="text-muted">{{ $filmProject->created_at->format('M d, Y H:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Updated</label>
                            <p class="text-muted">{{ $filmProject->updated_at->format('M d, Y H:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>Investors in This Project</h6>
                </div>
                <div class="card-body p-3">
                    @if($filmProject->investments->isEmpty())
                        <p class="text-muted text-center">No investments recorded for this film project yet.</p>
                    @else
                        <ul class="list-group">
                            @foreach($filmProject->investments as $investment)
                                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                                    <div class="d-flex align-items-center">
                                        <div class="icon icon-shape icon-sm me-3 bg-gradient-success shadow text-center">
                                            <i class="material-symbols-rounded opacity-10">person</i>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">{{ $investment->user->name ?? 'N/A' }}</h6>
                                            <span class="text-xs">Invested: ₱{{ number_format($investment->investment_amount, 2) }} ({{ $investment->number_of_shares }} shares)</span>
                                        </div>
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
